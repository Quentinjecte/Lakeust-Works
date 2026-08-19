import * as THREE from 'three';
import gsap from 'gsap';
import testVert from './shaders/test.vert';
import testFrag from './shaders/test.frag';

const canvas = document.querySelector('#three-test-canvas');

function getSize() {
    const rect = canvas.getBoundingClientRect();
    return {
        width: rect.width || window.innerWidth,
        height: rect.height || window.innerHeight,
    };
}

const scene = new THREE.Scene();
const { width: initialWidth, height: initialHeight } = getSize();
const camera = new THREE.PerspectiveCamera(50, initialWidth / initialHeight, 0.1, 100);
camera.position.z = 3;

const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setSize(initialWidth, initialHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

const material = new THREE.ShaderMaterial({
    vertexShader: testVert,
    fragmentShader: testFrag,
    transparent: true,
    uniforms: {
        uTime: { value: 0 },
        uColorA: { value: new THREE.Color('#ff7a18') },
        uColorB: { value: new THREE.Color('#3a0ca3') },
    },
});

const mesh = new THREE.Mesh(new THREE.PlaneGeometry(2, 2, 64, 64), material);
scene.add(mesh);

gsap.to(mesh.rotation, {
    z: Math.PI * 2,
    duration: 12,
    repeat: -1,
    ease: 'none',
});

const timer = new THREE.Timer();

function tick(timestamp) {
    timer.update(timestamp);
    material.uniforms.uTime.value = timer.getElapsed();
    renderer.render(scene, camera);
    requestAnimationFrame(tick);
}

tick();

const resizeObserver = new ResizeObserver(() => {
    const { width, height } = getSize();
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
});
resizeObserver.observe(canvas);
