import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const canvas = document.querySelector('#scene');
const stage = canvas.parentElement;
const loadMessage = document.querySelector('#load-message');
const scene = new THREE.Scene();
scene.background = new THREE.Color('#10121f');

const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);
camera.position.set(6.7, 4.3, 10.2);

const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: false });
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;
renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.15;

const controls = new OrbitControls(camera, canvas);
controls.target.set(0, 2.85, 0);
controls.enableDamping = true;
controls.dampingFactor = 0.06;
controls.minDistance = 6.5;
controls.maxDistance = 15;
controls.maxPolarAngle = Math.PI * 0.49;
controls.autoRotate = true;
controls.autoRotateSpeed = 1.05;

scene.add(new THREE.HemisphereLight('#d9e3ff', '#25202a', 2.1));

const keyLight = new THREE.DirectionalLight('#fff0d9', 3.2);
keyLight.position.set(4, 8, 6);
keyLight.castShadow = true;
keyLight.shadow.mapSize.set(1024, 1024);
scene.add(keyLight);

const fillLight = new THREE.DirectionalLight('#9ab7ff', 2.1);
fillLight.position.set(-5, 4, -4);
scene.add(fillLight);

const rimLight = new THREE.PointLight('#ff8c1a', 16, 8);
rimLight.position.set(2.2, 4.2, -1.5);
scene.add(rimLight);

const palette = {
    suit: new THREE.MeshStandardMaterial({ color: '#aaa9ad', roughness: 0.78 }),
    suitLight: new THREE.MeshStandardMaterial({ color: '#d1cfd0', roughness: 0.7 }),
    seam: new THREE.MeshStandardMaterial({ color: '#66666d', roughness: 0.85 }),
    dark: new THREE.MeshStandardMaterial({ color: '#22242b', roughness: 0.72 }),
    rubber: new THREE.MeshStandardMaterial({ color: '#17191e', roughness: 0.88 }),
    metal: new THREE.MeshStandardMaterial({ color: '#9da2a8', metalness: 0.72, roughness: 0.32 }),
    orange: new THREE.MeshStandardMaterial({ color: '#f28a18', metalness: 0.32, roughness: 0.38, emissive: '#6e2900', emissiveIntensity: 0.5 }),
    hair: new THREE.MeshStandardMaterial({ color: '#c4c0c1', roughness: 0.78 }),
    hairShade: new THREE.MeshStandardMaterial({ color: '#969398', roughness: 0.83 }),
    skin: new THREE.MeshStandardMaterial({ color: '#d8a18e', roughness: 0.8 }),
    eye: new THREE.MeshStandardMaterial({ color: '#17141a', roughness: 0.34 })
};

function addMesh(parent, geometry, material, position, scale) {
    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.set(...position);
    if (scale) mesh.scale.set(...scale);
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    parent.add(mesh);
    return mesh;
}

function sphere(parent, position, scale, material) {
    return addMesh(parent, new THREE.SphereGeometry(1, 24, 18), material, position, scale);
}

function box(parent, position, size, material, rotation = [0, 0, 0]) {
    const mesh = addMesh(parent, new THREE.BoxGeometry(...size), material, position);
    mesh.rotation.set(...rotation);
    return mesh;
}

function segment(parent, start, end, radius, material) {
    const from = new THREE.Vector3(...start);
    const to = new THREE.Vector3(...end);
    const direction = to.clone().sub(from);
    const geometry = new THREE.CapsuleGeometry(radius, Math.max(0.01, direction.length() - radius * 2), 5, 12);
    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.copy(from.add(to).multiplyScalar(0.5));
    mesh.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), direction.normalize());
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    parent.add(mesh);
    return mesh;
}

function tube(parent, points, radius, material) {
    const curve = new THREE.CatmullRomCurve3(points.map(point => new THREE.Vector3(...point)));
    const mesh = new THREE.Mesh(new THREE.TubeGeometry(curve, 32, radius, 8, false), material);
    mesh.castShadow = true;
    parent.add(mesh);
    return mesh;
}

const character = new THREE.Group();
scene.add(character);

function createBackpack() {
    box(character, [0, 3.83, -0.48], [0.74, 0.98, 0.34], palette.dark);
    box(character, [0, 4.04, -0.69], [0.52, 0.42, 0.11], palette.seam);
    box(character, [0, 3.55, -0.68], [0.47, 0.28, 0.12], palette.metal);
    tube(character, [[-0.44, 4.36, 0.15], [-0.56, 4.07, 0.37], [-0.55, 3.54, 0.32]], 0.09, palette.dark);
    tube(character, [[0.44, 4.36, 0.15], [0.56, 4.07, 0.37], [0.55, 3.54, 0.32]], 0.09, palette.dark);
    tube(character, [[-0.54, 4.02, 0.37], [-0.48, 4.02, 0.39], [-0.4, 4.02, 0.38]], 0.028, palette.orange);
    tube(character, [[0.54, 4.02, 0.37], [0.48, 4.02, 0.39], [0.4, 4.02, 0.38]], 0.028, palette.orange);

    const tool = new THREE.Group();
    tool.position.set(-0.88, 4.72, -0.54);
    tool.rotation.z = -0.16;
    character.add(tool);
    segment(tool, [0, -0.12, 0], [0.2, 0.86, 0], 0.075, palette.metal);
    const head = addMesh(tool, new THREE.TorusGeometry(0.27, 0.105, 10, 28), palette.metal, [0.2, 1.02, 0]);
    head.rotation.z = 0.28;
    addMesh(tool, new THREE.TorusGeometry(0.13, 0.035, 8, 20), palette.orange, [0.2, 1.02, 0.04]);
    box(tool, [-0.09, 1.26, 0], [0.24, 0.13, 0.16], palette.metal, [0, 0, -0.38]);
}

function createTorso() {
    sphere(character, [0, 3.75, 0], [0.72, 0.84, 0.43], palette.suit);
    sphere(character, [0, 2.72, 0], [0.63, 0.38, 0.39], palette.suit);
    sphere(character, [-0.48, 4.22, 0], [0.35, 0.31, 0.39], palette.suitLight);
    sphere(character, [0.48, 4.22, 0], [0.35, 0.31, 0.39], palette.suitLight);

    box(character, [0, 3.73, 0.405], [0.075, 1.12, 0.035], palette.seam);
    tube(character, [[-0.08, 4.26, 0.4], [-0.1, 3.92, 0.44], [-0.06, 3.5, 0.44], [0, 3.2, 0.41]], 0.025, palette.metal);
    for (const side of [-1, 1]) {
        tube(character, [[side * 0.39, 4.34, 0.17], [side * 0.62, 4.1, 0.22], [side * 0.68, 3.8, 0.2]], 0.055, palette.orange);
        tube(character, [[side * 0.53, 3.94, 0.3], [side * 0.58, 3.68, 0.36], [side * 0.52, 3.43, 0.34]], 0.018, palette.seam);
    }

    segment(character, [0, 4.42, 0], [0, 4.78, 0], 0.22, palette.skin);
    tube(character, [[-0.26, 4.55, 0.14], [-0.19, 4.37, 0.26], [0, 4.32, 0.29], [0.19, 4.37, 0.26], [0.26, 4.55, 0.14]], 0.075, palette.dark);
    tube(character, [[-0.27, 4.57, 0.13], [-0.19, 4.4, 0.28], [0, 4.35, 0.31], [0.19, 4.4, 0.28], [0.27, 4.57, 0.13]], 0.025, palette.metal);
}

function createArms() {
    for (const side of [-1, 1]) {
        const shoulder = [side * 0.69, 4.1, 0.02];
        const elbow = [side * 1.12, 3.62, 0.08];
        const wrist = [side * 0.74, 2.94, 0.37];
        sphere(character, shoulder, [0.29, 0.32, 0.31], palette.suitLight);
        segment(character, shoulder, elbow, 0.22, palette.suit);
        sphere(character, elbow, [0.22, 0.23, 0.23], palette.seam);
        segment(character, elbow, wrist, 0.19, palette.suit);
        tube(character, [[side * 0.82, 4.21, 0.2], [side * 1.01, 3.99, 0.22], [side * 1.14, 3.72, 0.2]], 0.055, palette.orange);
        segment(character, [side * 0.77, 3.08, 0.36], [side * 0.64, 2.89, 0.41], 0.15, palette.dark);
        sphere(character, [side * 0.61, 2.83, 0.43], [0.19, 0.12, 0.14], palette.rubber);
        for (let finger = 0; finger < 3; finger++) {
            segment(character,
                [side * (0.53 + finger * 0.055), 2.82, 0.53],
                [side * (0.49 + finger * 0.055), 2.72, 0.51],
                0.035, palette.rubber);
        }
    }
}

function createLegs() {
    for (const side of [-1, 1]) {
        const hip = [side * 0.31, 2.55, 0];
        const knee = [side * 0.39, 1.48, 0.01];
        const ankle = [side * 0.43, 0.58, 0];
        sphere(character, hip, [0.36, 0.29, 0.35], palette.suit);
        segment(character, hip, knee, 0.29, palette.suit);
        segment(character, [side * 0.4, 1.39, 0], ankle, 0.25, palette.suit);
        tube(character, [[side * 0.57, 2.52, 0.12], [side * 0.62, 2.04, 0.18], [side * 0.63, 1.62, 0.17], [side * 0.62, 0.72, 0.13]], 0.045, palette.orange);

        sphere(character, [side * 0.4, 1.43, 0.23], [0.29, 0.29, 0.17], palette.dark);
        sphere(character, [side * 0.4, 1.43, 0.34], [0.23, 0.22, 0.08], palette.seam);
        box(character, [side * 0.4, 0.65, 0.17], [0.43, 0.24, 0.36], palette.dark);
        box(character, [side * 0.4, 0.7, 0.365], [0.21, 0.13, 0.04], palette.orange);

        box(character, [side * 0.61, 2.52, 0.15], [0.33, 0.43, 0.28], palette.dark, [0, 0, side * -0.08]);
        box(character, [side * 0.61, 2.55, 0.3], [0.19, 0.14, 0.035], palette.orange);
    }
}

function createBeltAndPouches() {
    const belt = addMesh(character, new THREE.TorusGeometry(0.56, 0.075, 10, 36), palette.dark, [0, 2.8, 0]);
    belt.rotation.x = Math.PI / 2;
    box(character, [0, 2.79, 0.44], [0.3, 0.23, 0.1], palette.metal);
    box(character, [0, 2.79, 0.5], [0.14, 0.1, 0.035], palette.orange);
    for (const side of [-1, 1]) {
        box(character, [side * 0.47, 2.79, 0.08], [0.11, 0.3, 0.31], palette.metal);
        box(character, [side * 0.66, 2.52, 0.19], [0.28, 0.38, 0.34], palette.dark);
        box(character, [side * 0.66, 2.56, 0.37], [0.18, 0.1, 0.045], palette.orange);
        box(character, [side * 0.66, 2.38, 0.37], [0.04, 0.11, 0.035], palette.metal);
    }
}

function createHead() {
    sphere(character, [0, 5.12, 0.02], [0.4, 0.49, 0.36], palette.skin);
    sphere(character, [-0.41, 5.12, 0.02], [0.08, 0.13, 0.1], palette.skin);
    sphere(character, [0.41, 5.12, 0.02], [0.08, 0.13, 0.1], palette.skin);

    sphere(character, [-0.15, 5.17, 0.34], [0.065, 0.045, 0.035], palette.eye);
    sphere(character, [0.15, 5.17, 0.34], [0.065, 0.045, 0.035], palette.eye);
    tube(character, [[-0.24, 5.25, 0.34], [-0.15, 5.28, 0.37], [-0.06, 5.25, 0.34]], 0.025, palette.hairShade);
    tube(character, [[0.06, 5.25, 0.34], [0.15, 5.28, 0.37], [0.24, 5.25, 0.34]], 0.025, palette.hairShade);
    sphere(character, [0, 5.04, 0.37], [0.045, 0.075, 0.05], palette.skin);
    tube(character, [[-0.1, 4.99, 0.36], [0, 4.97, 0.38], [0.1, 4.99, 0.36]], 0.012, palette.hairShade);

    sphere(character, [0, 5.39, -0.02], [0.45, 0.34, 0.4], palette.hair);
    sphere(character, [-0.3, 5.25, -0.01], [0.18, 0.37, 0.29], palette.hairShade);
    sphere(character, [0.3, 5.26, -0.01], [0.17, 0.34, 0.28], palette.hair);
    sphere(character, [-0.28, 5.01, 0.06], [0.13, 0.28, 0.21], palette.hair);
    sphere(character, [0.3, 5.06, 0.04], [0.11, 0.23, 0.2], palette.hairShade);
    for (const [position, scale, rotation] of [
        [[-0.19, 5.5, 0.31], [0.29, 0.1, 0.15], -0.28],
        [[0.06, 5.53, 0.32], [0.28, 0.09, 0.14], 0.12],
        [[0.27, 5.42, 0.25], [0.24, 0.1, 0.14], 0.48],
        [[-0.38, 5.4, 0.13], [0.2, 0.11, 0.16], -0.5]
    ]) {
        const lock = sphere(character, position, scale, palette.hair);
        lock.rotation.z = rotation;
    }
}

createBackpack();
createTorso();
createArms();
createLegs();
createBeltAndPouches();
createHead();

const floor = new THREE.Mesh(
    new THREE.PlaneGeometry(200, 200),
    new THREE.MeshStandardMaterial({ color: '#11131d', roughness: 0.93 })
);
floor.rotation.x = -Math.PI / 2;
floor.position.y = 0.08;
floor.receiveShadow = true;
scene.add(floor);

const halo = new THREE.Mesh(
    new THREE.TorusGeometry(2.45, 0.012, 8, 100),
    new THREE.MeshBasicMaterial({ color: '#ff941e', transparent: true, opacity: 0.42 })
);
halo.position.set(0, 2.9, -1.65);
scene.add(halo);

function resize() {
    const width = stage.clientWidth;
    const height = stage.clientHeight;
    renderer.setSize(width, height, false);
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
}

window.addEventListener('resize', resize);
resize();
loadMessage.remove();

document.querySelector('#rotation-button').addEventListener('click', event => {
    controls.autoRotate = !controls.autoRotate;
    event.currentTarget.textContent = controls.autoRotate ? 'Pause spin' : 'Resume spin';
    event.currentTarget.setAttribute('aria-pressed', String(controls.autoRotate));
});

document.querySelector('#reset-button').addEventListener('click', () => {
    camera.position.set(6.7, 4.3, 10.2);
    controls.target.set(0, 2.85, 0);
    controls.update();
});

function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
}

animate();
