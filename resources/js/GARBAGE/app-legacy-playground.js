/* app-legacy-playground.js — GARBAGE, jamais importé nulle part.
   Extrait tel quel de l'ancien app.js : anime les couleurs de .cube/.triangle
   (classes absentes de tout template actuel), un curseur suivant la souris
   (.cursor, idem — voir la version neuve dans pages/home.js:startCursor,
   sans rapport), un système de nav .initiate/.cease/menu jamais câblé sur le
   markup actuel, un cycle de couleur --nav-color en boucle infinie, et une
   boucle "iteration" totalement inerte (incrémente une variable locale
   jamais lue). Deux listeners "click" identiques y étaient même enregistrés
   deux fois (activate/deactivate). Conservé ici plutôt que supprimé — voir
   core/app.js pour ce qui a réellement remplacé cette partie (rien : ce
   bloc n'avait plus de contrepartie DOM). */

const cubes = document.querySelectorAll('.cube');
const triangles = document.querySelectorAll('.triangle');

function getRandomColor() {
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

// Chanquement de couleur par chaque rotation.
cubes.forEach((cube) => {
    cube.addEventListener('animationiteration', () => {
        const color = getRandomColor();
        cube.style.background = color;
        cube.style.boxShadow = `
            0 0 5px ${color},
            0 0 25px ${color},
            0 0 50px ${color},
            0 0 150px ${color}
        `;
    });
});

triangles.forEach((triangle) => {
    triangle.addEventListener('animationiteration', () => {
        const color = getRandomColor();
        triangle.style.background = color;
        triangle.style.boxShadow = `
            0 0 5px ${color},
            0 0 25px ${color},
            0 0 50px ${color},
            0 0 150px ${color}
        `;
    });
});

const cursor = document.querySelector('.cursor');
if (cursor) {
    document.addEventListener('mousemove', (e) => {
        cursor.style.left = e.clientX + "px";
        cursor.style.top = e.clientY + "px";
    });
}

const nav = document.querySelector("nav");
const menu = document.querySelector("menu");
const initiate = document.querySelector(".initiate");
const cease = document.querySelector(".cease");
const modes = {
    TreeJS: "",
    Normal: ""
};

const type = (str, parent, pos) => {
    const arr = str.split("");

    arr.forEach((item, i) => {
        const span = document.createElement("span");
        span.innerText = item;
        setTimeout(() => {
            parent.appendChild(span);
        }, i * 100 + pos * 250);
    });
}

// Activate
const activate = (e) => {
    if (e.target === initiate && !nav.classList.contains("active")) {
        nav.classList.remove("unactive");
        nav.classList.add("active");
        setTimeout(() => {
            initiate.setAttribute("style", "display: none;");
            Object.keys(modes).forEach((mode, i) => {
                const li = document.createElement("li");
                li.classList.add("btn");
                li.dataset["type"] = mode;
                type(mode, li, i);
                menu.appendChild(li);
            });
        }, 400);
    }
};

setInterval(() => {
    const newColor = getRandomColor();
    transitionColor(getComputedStyle(nav).getPropertyValue('--nav-color'), newColor, 60);
}, 10000);

// Deactivate
const deactivate = (e) => {
    if (e.target === cease) {
        nav.classList.remove("active");
        nav.classList.add("unactive");
        menu.innerHTML = "";
        setTimeout(() => {
            initiate.setAttribute("style", "display: block;")
        }, 800);
    }
};

// Events — étaient enregistrés DEUX FOIS dans l'original (même handler,
// deux addEventListener identiques) : un seul suffit, gardé ici pour
// mémoire de l'architecture, pas pour être réutilisé.
window.addEventListener("click", (e) => {
    activate(e);
    deactivate(e);
});

// Convertit #rrggbb -> [r,g,b]
function hexToRGB(hex) {
    const int = parseFloat(hex.slice(1), 16);
    return [
        (int >> 16) & 255,
        (int >> 8) & 255,
        int & 255
    ];
}

// Convertit [r,g,b] -> #rrggbb
function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
}

function transitionColor(starthex, endhex, steps = 60) {
    const start = hexToRGB(starthex);
    const end = hexToRGB(endhex);
    let step = 0;

    const interval = setInterval(() => {
        const r = Math.round(start[0] + ((end[0] - start[0]) / steps) * step);
        const g = Math.round(start[1] + ((end[1] - start[1]) / steps) * step);
        const b = Math.round(start[2] + ((end[2] - start[2]) / steps) * step);

        nav.style.setProperty('--nav-color', rgbToHex(r, g, b));
        step++;

        if (step > steps) clearInterval(interval)

    }, 16);
}

// Boucle totalement inerte dans l'original : incrémente `step`, assigne une
// chaîne vide à `r` (jamais lu), ne fait rien d'observable. Gardée telle
// quelle pour mémoire.
const steps = 60;
let step = 0;
const iteration = setInterval(() => {
    step++;
    const r = "";
});
