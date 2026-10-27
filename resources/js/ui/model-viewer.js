/* model-viewer.js — <lw-model-viewer>, visualiseur de modèles 3D de la branche
   Lakeust Arts (voir pages/arts/*).

   Attributs :
     src          URL d'un .glb / .gltf (Blender et Blockbench exportent tous deux en glTF),
                  ou d'un .lwm : un glTF chiffré par `php artisan models:protect`
     demo         forme procédurale affichée sans src : knot | crystal | golem | totem | orb
     accent       couleur CSS de la forme procédurale et du reflet de sol
     load         "auto" (défaut) | "click" — "click" n'instancie RIEN (ni WebGL, ni
                  téléchargement) avant le clic sur « Charger », pour les modèles lourds
     autorotate   rotation continue
     hover-rotate rotation seulement tant que le pointeur survole l'élément (cartes)
     wireframe    affichage en Maillage
     controls     affiche la barre d'outils (rotation, Maillage, reset, plein écran)
     hud          affiche le nombre de triangles réellement rendus
     no-zoom      ni molette ni pincement : la molette fait défiler la page par-dessus le
                  modèle, et un balayage vertical au doigt aussi (hero de « À propos ») ;
                  la rotation au glisser reste possible (cf. [no-zoom] dans css/arts.css)

   Économie de GPU : le rendu (renderer + contexte WebGL) n'existe que tant que
   l'élément est visible à l'écran ; hors champ il est détruit (une galerie de
   N cartes ne garde jamais N contextes ouverts, les navigateurs en plafonnent
   le nombre). Le rendu n'a lieu que si quelque chose a bougé. */

import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';
import { MeshoptDecoder } from 'three/examples/jsm/libs/meshopt_decoder.module.js';
import { decryptModel } from './model-crypto.js';

const REDUCED = matchMedia('(prefers-reduced-motion: reduce)').matches;

/* glTF déjà téléchargé et parsé, partagé entre les instances (une même scène
   peut être remontée plusieurs fois quand on fait défiler la page). */
const gltfCache = new Map();
function loadGltf(src, onProgress) {
    if (!gltfCache.has(src)) {
        const loader = new GLTFLoader();
        loader.setMeshoptDecoder(MeshoptDecoder);
        gltfCache.set(src, new Promise((res, rej) => {
            if (/\.lwm(\?|$)/.test(src)) {
                /* modèle protégé : le fichier servi est chiffré (php artisan models:protect),
                   il n'est déchiffré qu'en mémoire ici -> l'URL ne donne qu'un fichier illisible */
                fetch(src).then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.arrayBuffer(); })
                    .then(decryptModel)
                    .then(buf => loader.parse(buf, '', g => res(g.scene), rej))
                    .catch(rej);
            } else {
                loader.load(src, g => res(g.scene), onProgress, rej);
            }
        }));
    }
    return gltfCache.get(src);
}

/* ----------------------------------------------------- formes procédurales --- */
/* Stand-ins tant que les vrais .glb ne sont pas exportés. Chacune renvoie un
   Group centré en (0,0,0) ; les matériaux sont créés ici, donc libérables. */
const std = (color, o = {}) => new THREE.MeshStandardMaterial({ color, roughness: .5, metalness: .1, ...o });
const box = (w, h, d, x, y, z, mat) => { const m = new THREE.Mesh(new THREE.BoxGeometry(w, h, d), mat); m.position.set(x, y, z); return m; };

const DEMOS = {
    knot(accent) {
        const g = new THREE.Group();
        g.add(new THREE.Mesh(new THREE.TorusKnotGeometry(.8, .27, 240, 32), std(accent, { metalness: .92, roughness: .22 })));
        return g;
    },
    crystal(accent) {
        const g = new THREE.Group();
        const body = new THREE.Mesh(new THREE.IcosahedronGeometry(.8, 0), std(accent, { flatShading: true, roughness: .12, metalness: .15, emissive: accent, emissiveIntensity: .35 }));
        body.scale.set(.85, 1.5, .85);
        g.add(body);
        for (let i = 0; i < 6; i++) {
            const a = (i / 6) * Math.PI * 2;
            const s = new THREE.Mesh(new THREE.OctahedronGeometry(.13 + (i % 3) * .03, 0), std(accent, { flatShading: true, emissive: accent, emissiveIntensity: .5, roughness: .2 }));
            s.position.set(Math.cos(a) * 1.25, Math.sin(a * 2) * .5 - .1, Math.sin(a) * 1.25);
            s.rotation.set(a, a * .5, 0);
            g.add(s);
        }
        return g;
    },
    golem(accent) {
        const g = new THREE.Group();
        const stone = std('#8a8f9c', { roughness: .85, flatShading: true });
        const dark = std('#555a66', { roughness: .9, flatShading: true });
        const glow = std(accent, { emissive: accent, emissiveIntensity: 1.1, roughness: .4 });
        g.add(box(1, 1.15, .6, 0, .25, 0, stone));                // torse
        g.add(box(.62, .6, .6, 0, 1.15, 0, stone));               // tête
        g.add(box(.14, .1, .05, -.16, 1.2, .31, glow));           // yeux
        g.add(box(.14, .1, .05, .16, 1.2, .31, glow));
        g.add(box(.34, 1.0, .38, -.72, .3, 0, dark));             // bras
        g.add(box(.34, 1.0, .38, .72, .3, 0, dark));
        g.add(box(.42, .8, .45, -.28, -.7, 0, dark));             // jambes
        g.add(box(.42, .8, .45, .28, -.7, 0, dark));
        g.add(box(.3, .3, .06, 0, .35, .32, glow));              // noyau
        return g;
    },
    totem(accent) {
        const g = new THREE.Group();
        const wood = std('#8b6a45', { roughness: .9, flatShading: true });
        const moss = std('#5d7f46', { roughness: .95, flatShading: true });
        const face = std(accent, { emissive: accent, emissiveIntensity: .55 });
        const blocks = [[1.1, .7, 1.1, -1.05], [.9, .8, .9, -.3], [1.0, .7, 1.0, .45], [.7, .6, .7, 1.1]];
        blocks.forEach(([w, h, d, y], i) => {
            g.add(box(w, h, d, 0, y, 0, i % 2 ? moss : wood));
            g.add(box(w * .5, h * .22, .05, 0, y + h * .1, d / 2 + .02, face));   // visage
        });
        g.add(box(.5, .35, .5, 0, 1.6, 0, moss));
        return g;
    },
    orb(accent) {
        const g = new THREE.Group();
        g.add(new THREE.Mesh(new THREE.SphereGeometry(.95, 64, 48), new THREE.MeshPhysicalMaterial({ color: '#ffffff', roughness: .03, metalness: 0, clearcoat: 1, clearcoatRoughness: 0, transparent: true, opacity: .32 })));
        g.add(new THREE.Mesh(new THREE.SphereGeometry(.38, 32, 24), std(accent, { emissive: accent, emissiveIntensity: 1.2, roughness: .3 })));
        const ring = new THREE.Mesh(new THREE.TorusGeometry(1.3, .035, 16, 120), std('#e8c26a', { metalness: 1, roughness: .25 }));
        ring.rotation.x = Math.PI / 2.4;
        g.add(ring);
        return g;
    }
};

/* tache d'ombre douce sous le modèle (un plan à dégradé radial, pas de shadow map) */
function groundShadow(size, y) {
    const c = document.createElement('canvas'); c.width = c.height = 128;
    const x = c.getContext('2d');
    const gr = x.createRadialGradient(64, 64, 4, 64, 64, 64);
    gr.addColorStop(0, 'rgba(0,0,0,.55)'); gr.addColorStop(1, 'rgba(0,0,0,0)');
    x.fillStyle = gr; x.fillRect(0, 0, 128, 128);
    const tex = new THREE.CanvasTexture(c);
    const m = new THREE.Mesh(new THREE.PlaneGeometry(size, size), new THREE.MeshBasicMaterial({ map: tex, transparent: true, depthWrite: false }));
    m.rotation.x = -Math.PI / 2; m.position.y = y;
    return m;
}

/* ------------------------------------------------------------------ élément --- */
class LwModelViewer extends HTMLElement {
    static observedAttributes = ['src', 'demo', 'accent', 'load', 'wireframe', 'autorotate'];

    connectedCallback() {
        if (!this._built) this._build();
        this._built = true;
        this._io = new IntersectionObserver(([e]) => {
            this._visible = e.isIntersecting;
            this._visible ? this._mount() : this._unmount();
        }, { rootMargin: '120px' });
        this._io.observe(this);
    }

    disconnectedCallback() {
        this._io?.disconnect();
        clearTimeout(this._resume);
        this._unmount();
    }

    attributeChangedCallback(name, old, val) {
        if (!this._built || old === val) return;
        if (name === 'src' || name === 'demo' || name === 'accent' || name === 'load') {
            /* un nouveau modèle lourd repasse en attente de clic ; passer à une forme
               de démo (ou à un modèle léger) doit au contraire monter le rendu si
               l'élément était resté en attente (aucun renderer n'existait). */
            if (name === 'src') this._userLoaded = false;
            this._syncOverlay();
            /* une sélection change 3-4 attributs d'affilée : un seul rechargement */
            if (!this._reloading) {
                this._reloading = true;
                queueMicrotask(() => { this._reloading = false; this._active ? this._loadContent() : this._mount(); });
            }
        } else if (name === 'wireframe') this._applyWireframe();
        else if (name === 'autorotate') this._applyRotate();
    }

    /* -------------------------------------------------------------- DOM --- */
    _build() {
        this.innerHTML = '';
        this.classList.add('lwmv');
        this._stage = Object.assign(document.createElement('div'), { className: 'lwmv-stage' });
        this._status = Object.assign(document.createElement('div'), { className: 'lwmv-status' });
        this._hud = Object.assign(document.createElement('div'), { className: 'lwmv-hud' });
        this.append(this._stage, this._status, this._hud);

        this._overlay = document.createElement('div');
        this._overlay.className = 'lwmv-click';
        this._overlay.innerHTML = '<button type="button" data-act="load"><span class="i18n-fr">Charger le modèle</span><span class="i18n-en">Load the model</span></button>';
        this.append(this._overlay);

        if (this.hasAttribute('controls')) {
            const bar = document.createElement('div');
            bar.className = 'lwmv-bar';
            bar.innerHTML =
                '<button type="button" data-act="rotate" title="Rotation auto / Auto-rotate" aria-label="Rotation auto">↻</button>' +
                '<button type="button" data-act="wire" title="Maillage / Wireframe" aria-label="Maillage">◫</button>' +
                '<button type="button" data-act="reset" title="Recentrer / Reset view" aria-label="Recentrer">⌖</button>' +
                '<button type="button" data-act="full" title="Plein écran / Fullscreen" aria-label="Plein écran">⛶</button>';
            this.append(bar);
        }
        this.addEventListener('click', e => {
            const b = e.target.closest('[data-act]');
            if (!b) return;
            e.preventDefault();      // le viewer peut se trouver dans une carte-lien
            e.stopPropagation();
            ({
                load: () => { this._userLoaded = true; this._syncOverlay(); this._visible && this._mount(); },
                rotate: () => this.toggleAttribute('autorotate'),
                wire: () => this.toggleAttribute('wireframe'),
                reset: () => this._resetView(),
                full: () => (document.fullscreenElement ? document.exitFullscreen() : this.requestFullscreen?.())
            })[b.dataset.act]?.();
        });
        this.addEventListener('pointerenter', () => { this._hover = true; this._applyRotate(); });
        this.addEventListener('pointerleave', () => { this._hover = false; this._applyRotate(); });
        this._syncOverlay();
    }

    _clickMode() { return this.getAttribute('load') === 'click' && !!this.getAttribute('src'); }

    /* en mode "click" tant que rien n'est chargé : pas de rendu, juste l'affiche */
    _syncOverlay() {
        const waiting = this._clickMode() && !this._userLoaded;
        this._overlay.hidden = !waiting;
        this.classList.toggle('is-waiting', waiting);
    }

    /* ---------------------------------------------------------- cycle de vie --- */
    _mount() {
        if (this._active || !this._visible || (this._clickMode() && !this._userLoaded)) return;
        this._active = true;

        const r = this._renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'low-power' });
        r.setPixelRatio(Math.min(devicePixelRatio || 1, 2));
        r.toneMapping = THREE.ACESFilmicToneMapping;
        r.toneMappingExposure = 1.05;
        this._stage.appendChild(r.domElement);

        this._scene = new THREE.Scene();
        const pm = new THREE.PMREMGenerator(r);
        this._env = pm.fromScene(new RoomEnvironment(), .04).texture;   // reflets de studio, propres à ce contexte
        pm.dispose();
        this._scene.environment = this._env;
        const key = new THREE.DirectionalLight('#fff4e0', 1.6); key.position.set(3, 5, 4);
        this._scene.add(key);

        this._camera = new THREE.PerspectiveCamera(35, 1, .01, 1000);
        this._controls = new OrbitControls(this._camera, r.domElement);
        this._controls.enableDamping = true;
        this._controls.enablePan = false;
        this._controls.enableZoom = !this.hasAttribute('no-zoom');
        this._controls.addEventListener('change', () => { this._dirty = true; });
        this._controls.addEventListener('start', () => { clearTimeout(this._resume); this._controls.autoRotate = false; });
        this._controls.addEventListener('end', () => { clearTimeout(this._resume); this._resume = setTimeout(() => this._applyRotate(), 2500); });

        this._ro = new ResizeObserver(() => this._resize());
        this._ro.observe(this);
        this._resize();

        this._loadContent();
        const tick = () => {
            this._raf = requestAnimationFrame(tick);
            this._controls.update();
            if (this._dirty) { this._dirty = false; r.render(this._scene, this._camera); }
        };
        tick();
    }

    _unmount() {
        if (!this._active) return;
        this._active = false;
        cancelAnimationFrame(this._raf);
        this._ro?.disconnect();
        this._controls?.dispose();
        this._disposeContent();
        this._env?.dispose();
        this._renderer?.dispose();
        this._renderer?.forceContextLoss();
        this._renderer?.domElement.remove();
        this._renderer = this._scene = this._camera = this._controls = this._env = null;
    }

    _resize() {
        if (!this._renderer) return;
        const w = Math.max(this.clientWidth, 1), h = Math.max(this.clientHeight, 1);
        this._renderer.setSize(w, h, false);
        this._renderer.domElement.style.cssText = 'width:100%;height:100%;display:block';
        this._camera.aspect = w / h;
        this._camera.updateProjectionMatrix();
        this._dirty = true;
    }

    /* ------------------------------------------------------------- contenu --- */
    _disposeContent() {
        if (!this._holder) return;
        this._scene?.remove(this._holder);
        this._holder.traverse(o => {
            if (!o.isMesh) return;
            if (o.userData.lwOwned) o.geometry.dispose();        // formes procédurales seulement : la géométrie glTF est partagée
            (Array.isArray(o.material) ? o.material : [o.material]).forEach(m => {
                if (o.userData.lwOwned) m?.map?.dispose?.();     // texture d'ombre ; celles du glTF sont partagées
                m?.dispose?.();
            });
        });
        this._holder = null;
    }

    async _loadContent() {
        const token = this._token = (this._token || 0) + 1;
        const src = this.getAttribute('src');
        const accent = this.getAttribute('accent') || '#b3a9e6';
        this.style.setProperty('--c', accent);          // teinte le fond du viewer (css/arts.css)
        this._disposeContent();
        this._status.textContent = '';

        let content;
        if (src && (!this._clickMode() || this._userLoaded)) {
            this.classList.add('is-loading');
            try {
                const base = await loadGltf(src, e => {
                    if (token === this._token && e.lengthComputable) this._status.textContent = Math.round(e.loaded / e.total * 100) + ' %';
                });
                if (token !== this._token || !this._active) return;
                content = base.clone(true);
                /* le clone partage les matériaux avec les autres instances : on les duplique
                   pour que le Maillage d'une vue n'affecte pas les autres. */
                content.traverse(o => {
                    if (o.isMesh) o.material = Array.isArray(o.material) ? o.material.map(m => m.clone()) : o.material.clone();
                });
            } catch (err) {
                console.warn('[lw-model-viewer] chargement impossible :', src, err);
                if (token !== this._token) return;
                this._status.textContent = 'Modèle introuvable — forme de démo affichée';
                content = DEMOS.knot(accent);
                content.traverse(o => { if (o.isMesh) o.userData.lwOwned = true; });
            }
            this.classList.remove('is-loading');
            if (!this._status.textContent.startsWith('Modèle')) this._status.textContent = '';
        } else {
            content = (DEMOS[this.getAttribute('demo')] || DEMOS.knot)(accent);
            content.traverse(o => { if (o.isMesh) o.userData.lwOwned = true; });
        }
        if (!this._active || token !== this._token) return;

        /* recentre le modèle sur l'origine et cadre la caméra dessus */
        const holder = this._holder = new THREE.Group();
        holder.add(content);
        const box3 = new THREE.Box3().setFromObject(holder);
        const size = box3.getSize(new THREE.Vector3()), center = box3.getCenter(new THREE.Vector3());
        content.position.sub(center);
        const maxDim = Math.max(size.x, size.y, size.z) || 1;
        const shadow = groundShadow(maxDim * 2.4, -size.y / 2 - maxDim * .004);
        shadow.userData.lwOwned = true;
        holder.add(shadow);
        this._scene.add(holder);

        const dist = (maxDim / 2) / Math.tan(THREE.MathUtils.degToRad(this._camera.fov / 2)) * 1.7;
        this._home = { dist, pos: new THREE.Vector3(.72, .42, .9).normalize().multiplyScalar(dist) };
        this._camera.near = dist / 100; this._camera.far = dist * 100;
        this._camera.updateProjectionMatrix();
        this._controls.minDistance = dist * .35; this._controls.maxDistance = dist * 3;
        this._resetView();

        let tris = 0;
        content.traverse(o => {
            if (!o.isMesh) return;
            tris += o.geometry.index ? o.geometry.index.count / 3 : o.geometry.attributes.position.count / 3;
        });
        this.tris = Math.round(tris);
        this._hud.textContent = this.hasAttribute('hud') ? this.tris.toLocaleString('fr-FR') + ' tris' : '';
        this.dispatchEvent(new CustomEvent('lw-model-load', { detail: { tris: this.tris } }));

        this._applyWireframe();
        this._applyRotate();
        this._dirty = true;
    }

    _resetView() {
        if (!this._home || !this._camera) return;
        this._camera.position.copy(this._home.pos);
        this._controls.target.set(0, 0, 0);
        this._controls.update();
        this._dirty = true;
    }

    _applyWireframe() {
        const on = this.hasAttribute('wireframe');
        this._holder?.traverse(o => {
            if (!o.isMesh || o.userData.lwOwned && o.geometry.type === 'PlaneGeometry') return;
            (Array.isArray(o.material) ? o.material : [o.material]).forEach(m => { m.wireframe = on; });
        });
        this.classList.toggle('is-wire', on);
        this._dirty = true;
    }

    _applyRotate() {
        if (!this._controls) return;
        const want = !REDUCED && (this.hasAttribute('autorotate') || (this.hasAttribute('hover-rotate') && this._hover));
        this._controls.autoRotate = want;
        this._controls.autoRotateSpeed = 0.5;
        this.classList.toggle('is-rotating', this.hasAttribute('autorotate'));
        this._dirty = true;
    }
}

if (!customElements.get('lw-model-viewer')) customElements.define('lw-model-viewer', LwModelViewer);
