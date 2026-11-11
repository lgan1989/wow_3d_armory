<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#090d1b">
	<title>Nyx — Neon Energy Guardian</title>
	<style>
		:root {
			color-scheme: dark;
			font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
			background: #090d1b;
			color: #e6f8ff;
			font-synthesis: none;
			text-rendering: optimizeLegibility;
		}

		* { box-sizing: border-box; }

		body {
			margin: 0;
			min-width: 320px;
			min-height: 100vh;
			overflow: hidden;
			background:
				radial-gradient(ellipse at 50% 48%, rgba(35, 83, 129, .25), transparent 44%),
				linear-gradient(145deg, #0b1020, #10182b 58%, #0a1020);
		}

		#stage {
			position: fixed;
			inset: 0;
		}

		#stage canvas {
			display: block;
			width: 100%;
			height: 100%;
			outline: none;
		}

		.topbar {
			position: fixed;
			z-index: 2;
			top: 0;
			left: 0;
			right: 0;
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 20px;
			padding: 26px clamp(20px, 5vw, 72px);
			pointer-events: none;
		}

		.brand, .actions, .card { pointer-events: auto; }

		.eyebrow {
			margin: 0 0 8px;
			color: #69eaff;
			font-size: 10px;
			font-weight: 700;
			letter-spacing: .24em;
			text-transform: uppercase;
		}

		h1 {
			margin: 0;
			font-size: clamp(24px, 3vw, 38px);
			font-weight: 500;
			letter-spacing: .13em;
			line-height: 1;
			text-transform: uppercase;
		}

		.subtitle {
			margin: 10px 0 0;
			color: #9eb4ca;
			font-size: 12px;
			letter-spacing: .06em;
		}

		.actions {
			display: flex;
			gap: 9px;
		}

		button {
			border: 1px solid rgba(118, 224, 255, .26);
			border-radius: 999px;
			padding: 10px 15px;
			background: rgba(10, 21, 39, .7);
			color: #d7f8ff;
			font: inherit;
			font-size: 11px;
			letter-spacing: .05em;
			cursor: pointer;
			backdrop-filter: blur(12px);
			transition: border-color .2s, background .2s, transform .2s;
		}

		button:hover {
			transform: translateY(-1px);
			border-color: rgba(118, 224, 255, .7);
			background: rgba(23, 53, 78, .82);
		}

		button:focus-visible {
			outline: 2px solid #70eeff;
			outline-offset: 3px;
		}

		.card {
			position: fixed;
			z-index: 2;
			right: clamp(18px, 5vw, 72px);
			bottom: 30px;
			width: min(260px, calc(100vw - 36px));
			padding: 16px 18px;
			border: 1px solid rgba(119, 207, 240, .17);
			border-radius: 14px;
			background: rgba(8, 15, 29, .66);
			box-shadow: 0 16px 50px rgba(0, 0, 0, .22);
			backdrop-filter: blur(14px);
		}

		.card-title {
			margin: 0 0 9px;
			color: #70e9ff;
			font-size: 10px;
			font-weight: 700;
			letter-spacing: .18em;
			text-transform: uppercase;
		}

		.card p {
			margin: 0;
			color: #a7bbce;
			font-size: 11px;
			line-height: 1.65;
		}

		.status {
			position: fixed;
			z-index: 2;
			bottom: 32px;
			left: clamp(20px, 5vw, 72px);
			display: flex;
			align-items: center;
			gap: 9px;
			color: #a5bbd0;
			font-size: 10px;
			letter-spacing: .12em;
			text-transform: uppercase;
			pointer-events: none;
		}

		.status-dot {
			width: 7px;
			height: 7px;
			border-radius: 50%;
			background: #53f2ff;
			box-shadow: 0 0 14px #53f2ff;
		}

		@media (max-width: 600px) {
			.topbar {
				align-items: flex-start;
				padding-top: 20px;
			}

			.actions {
				flex-direction: column;
			}

			.card { bottom: 58px; }
			.status { bottom: 24px; }
		}
	</style>
	<script type="importmap">
		{
			"imports": {
				"three": "https://cdn.jsdelivr.net/npm/three@0.170.0/build/three.module.js",
				"three/addons/": "https://cdn.jsdelivr.net/npm/three@0.170.0/examples/jsm/"
			}
		}
	</script>
</head>
<body>
	<div id="stage" aria-label="Interactive 3D character preview"></div>
	<header class="topbar">
		<div class="brand">
			<p class="eyebrow">Celestial Sentinel · Character Study 01</p>
			<h1>Nyx</h1>
			<p class="subtitle">Queen of Sleep &amp; Neon Energy Guardian</p>
		</div>
		<div class="actions">
			<button id="spin" type="button" aria-pressed="true">Pause rotation</button>
			<button id="reset" type="button">Reset view</button>
		</div>
	</header>
	<aside class="card">
		<p class="card-title">The dreamkeeper</p>
		<p>A luminous guardian in midnight armor, with a crystalline mantle and shards of sleeping starlight.</p>
	</aside>
	<div class="status"><span class="status-dot"></span>Interactive 3D study</div>

	<script type="module">
		import * as THREE from 'three';
		import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

		const stage = document.querySelector('#stage');
		const scene = new THREE.Scene();
		scene.background = new THREE.Color('#090d1b');
		scene.fog = new THREE.FogExp2('#090d1b', 0.035);

		const camera = new THREE.PerspectiveCamera(32, window.innerWidth / window.innerHeight, 0.1, 100);
		camera.position.set(7.7, 5.2, 11.5);

		const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
		renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
		renderer.setSize(window.innerWidth, window.innerHeight);
		renderer.outputColorSpace = THREE.SRGBColorSpace;
		renderer.toneMapping = THREE.ACESFilmicToneMapping;
		renderer.toneMappingExposure = 1.15;
		stage.appendChild(renderer.domElement);

		const controls = new OrbitControls(camera, renderer.domElement);
		controls.target.set(0, 2.65, 0);
		controls.enableDamping = true;
		controls.dampingFactor = 0.055;
		controls.minDistance = 7;
		controls.maxDistance = 18;
		controls.maxPolarAngle = Math.PI * 0.49;
		controls.autoRotate = true;
		controls.autoRotateSpeed = 0.7;

		scene.add(new THREE.HemisphereLight('#b7eaff', '#141426', 2.1));

		const keyLight = new THREE.DirectionalLight('#d5faff', 3.2);
		keyLight.position.set(4, 8, 7);
		scene.add(keyLight);

		const rimLight = new THREE.PointLight('#08cfff', 38, 12, 2);
		rimLight.position.set(-3, 4, -2);
		scene.add(rimLight);

		const violetLight = new THREE.PointLight('#6455ff', 24, 10, 2);
		violetLight.position.set(4, 2, -3);
		scene.add(violetLight);

		const faceLight = new THREE.PointLight('#a5ecff', 18, 9, 2);
		faceLight.position.set(0, 4, 5);
		scene.add(faceLight);

		const palette = {
			suit: new THREE.MeshStandardMaterial({ color: '#1a2b49', roughness: 0.38, metalness: 0.42 }),
			suitLight: new THREE.MeshStandardMaterial({ color: '#2a4568', roughness: 0.32, metalness: 0.4 }),
			armor: new THREE.MeshStandardMaterial({ color: '#203956', roughness: 0.28, metalness: 0.55 }),
			skin: new THREE.MeshStandardMaterial({ color: '#b9d9ee', roughness: 0.48, metalness: 0.06 }),
			hair: new THREE.MeshStandardMaterial({ color: '#087de3', roughness: 0.26, metalness: 0.28, emissive: '#06386f', emissiveIntensity: 0.38 }),
			hairLight: new THREE.MeshStandardMaterial({ color: '#14ceff', roughness: 0.3, metalness: 0.22, emissive: '#0876a2', emissiveIntensity: 0.55 }),
			glow: new THREE.MeshStandardMaterial({ color: '#43f5ff', roughness: 0.22, metalness: 0.2, emissive: '#13dfff', emissiveIntensity: 2.2 }),
			crystal: new THREE.MeshPhysicalMaterial({
				color: '#5beaff',
				roughness: 0.12,
				metalness: 0.08,
				transmission: 0.32,
				thickness: 0.6,
				ior: 1.25,
				emissive: '#0b9abf',
				emissiveIntensity: 0.72,
				transparent: true,
				opacity: 0.88
			}),
			cape: new THREE.MeshPhysicalMaterial({
				color: '#48cbe8',
				roughness: 0.25,
				metalness: 0.18,
				transmission: 0.12,
				thickness: 0.2,
				emissive: '#08748e',
				emissiveIntensity: 0.25,
				transparent: true,
				opacity: 0.42,
				side: THREE.DoubleSide,
				depthWrite: false
			})
		};

		const root = new THREE.Group();
		scene.add(root);
		const character = new THREE.Group();
		root.add(character);

		function ellipsoid(parent, position, scale, material, segments = 24) {
			const mesh = new THREE.Mesh(new THREE.SphereGeometry(1, segments, Math.max(12, segments >> 1)), material);
			mesh.position.set(...position);
			mesh.scale.set(...scale);
			mesh.castShadow = true;
			mesh.receiveShadow = true;
			parent.add(mesh);
			return mesh;
		}

		function link(parent, start, end, radius, material, topRadius = radius) {
			const a = new THREE.Vector3(...start);
			const b = new THREE.Vector3(...end);
			const direction = b.clone().sub(a);
			const mesh = new THREE.Mesh(
				new THREE.CylinderGeometry(topRadius, radius, direction.length(), 14, 1),
				material
			);
			mesh.position.copy(a).add(b).multiplyScalar(0.5);
			mesh.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), direction.normalize());
			mesh.castShadow = true;
			parent.add(mesh);
			return mesh;
		}

		function tube(parent, points, radius, material, tubularSegments = 32) {
			const curve = new THREE.CatmullRomCurve3(points.map(point => new THREE.Vector3(...point)));
			const mesh = new THREE.Mesh(new THREE.TubeGeometry(curve, tubularSegments, radius, 8, false), material);
			parent.add(mesh);
			return mesh;
		}

		function crystal(parent, position, scale, rotation = [0, 0, 0]) {
			const mesh = new THREE.Mesh(new THREE.OctahedronGeometry(0.45, 0), palette.crystal);
			mesh.position.set(...position);
			mesh.scale.set(...scale);
			mesh.rotation.set(...rotation);
			mesh.castShadow = true;
			parent.add(mesh);
			return mesh;
		}

		function capePanel(side) {
			const shape = new THREE.Shape();
			shape.moveTo(side * 0.27, 3.96);
			shape.bezierCurveTo(side * 0.65, 3.88, side * 0.88, 3.62, side * 1.15, 3.28);
			shape.lineTo(side * 0.91, 2.65);
			shape.bezierCurveTo(side * 0.72, 2.48, side * 0.48, 2.7, side * 0.38, 3.05);
			shape.bezierCurveTo(side * 0.28, 3.38, side * 0.32, 3.68, side * 0.27, 3.96);
			const mesh = new THREE.Mesh(new THREE.ShapeGeometry(shape, 20), palette.cape);
			mesh.position.z = -0.31;
			mesh.renderOrder = 1;
			character.add(mesh);

			const edgePoints = [
				[side * 0.27, 3.96, -0.29],
				[side * 0.66, 3.79, -0.29],
				[side * 1.15, 3.28, -0.29],
				[side * 0.91, 2.65, -0.29],
				[side * 0.62, 2.69, -0.29],
				[side * 0.38, 3.05, -0.29]
			];
			const edgeGeometry = new THREE.BufferGeometry().setFromPoints(edgePoints.map(point => new THREE.Vector3(...point)));
			const edge = new THREE.LineLoop(edgeGeometry, new THREE.LineBasicMaterial({ color: '#45eaff', transparent: true, opacity: 0.82 }));
			character.add(edge);
			tube(character, [[side * 0.36, 3.78, -0.27], [side * 0.7, 3.4, -0.27], [side * 0.61, 2.95, -0.27]], 0.009, palette.glow, 20);
		}

		// Cape sits behind the silhouette so the armor and face remain clearly readable.
		capePanel(-1);
		capePanel(1);

		// Boots and long armored legs.
		for (const side of [-1, 1]) {
			const hipX = side * 0.17;
			const kneeX = side * 0.19;
			const ankleX = side * 0.23;
			ellipsoid(character, [hipX, 2.73, 0], [0.2, 0.25, 0.19], palette.suit);
			link(character, [hipX, 2.72, 0], [kneeX, 1.55, 0.015], 0.17, palette.suit, 0.125);
			ellipsoid(character, [kneeX, 1.54, 0.02], [0.14, 0.17, 0.15], palette.armor);
			link(character, [kneeX, 1.5, 0.015], [ankleX, 0.37, 0.04], 0.12, palette.suit, 0.075);
			ellipsoid(character, [ankleX, 0.34, 0.05], [0.1, 0.15, 0.11], palette.armor);
			ellipsoid(character, [ankleX, 0.16, 0.16], [0.14, 0.11, 0.29], palette.suitLight);
			link(character, [ankleX, 0.21, 0.05], [ankleX, 0.055, 0.22], 0.045, palette.armor, 0.035);
			tube(character, [[side * 0.17, 2.12, 0.12], [side * 0.18, 1.8, 0.14], [side * 0.2, 1.52, 0.15]], 0.012, palette.glow, 18);
			tube(character, [[side * 0.2, 1.45, 0.105], [side * 0.22, 0.92, 0.12], [side * 0.23, 0.48, 0.13]], 0.009, palette.glow, 20);
		}

		// Torso, waist, and chest plating.
		ellipsoid(character, [0, 2.83, 0], [0.32, 0.32, 0.22], palette.armor);
		ellipsoid(character, [0, 3.45, 0], [0.36, 0.55, 0.23], palette.suit);
		ellipsoid(character, [0, 3.91, 0], [0.47, 0.19, 0.22], palette.armor);
		ellipsoid(character, [0, 3.63, 0.195], [0.25, 0.31, 0.07], palette.suitLight);
		ellipsoid(character, [0, 3.08, 0.07], [0.3, 0.18, 0.2], palette.suit);
		link(character, [0, 3.96, 0], [0, 4.17, 0], 0.1, palette.skin, 0.09);
		ellipsoid(character, [0, 3.98, 0.14], [0.34, 0.12, 0.12], palette.armor);

		for (const side of [-1, 1]) {
			ellipsoid(character, [side * 0.44, 3.91, -0.015], [0.19, 0.18, 0.22], palette.armor);
			const shoulder = new THREE.Mesh(new THREE.OctahedronGeometry(0.22, 0), palette.suitLight);
			shoulder.position.set(side * 0.47, 3.98, 0.06);
			shoulder.scale.set(1, 0.75, 0.8);
			character.add(shoulder);
			tube(character, [[side * 0.28, 3.78, 0.19], [side * 0.34, 3.5, 0.21], [side * 0.27, 3.2, 0.2]], 0.012, palette.glow, 20);
		}

		// Arms and articulated gauntlets.
		for (const side of [-1, 1]) {
			const shoulder = [side * 0.49, 3.9, 0];
			const elbow = [side * 0.61, 3.34, 0.015];
			const wrist = [side * 0.57, 2.78, 0.08];
			link(character, shoulder, elbow, 0.135, palette.suitLight, 0.095);
			ellipsoid(character, elbow, [0.12, 0.13, 0.12], palette.armor);
			link(character, elbow, wrist, 0.095, palette.suit, 0.065);
			ellipsoid(character, wrist, [0.1, 0.14, 0.1], palette.armor);
			ellipsoid(character, [side * 0.57, 2.62, 0.1], [0.12, 0.2, 0.1], palette.suit);
			for (let finger = 0; finger < 3; finger++) {
				const x = side * (0.51 + finger * 0.055);
				link(character, [x, 2.57, 0.12], [x + side * 0.01, 2.42, 0.13], 0.026, palette.armor, 0.019);
			}
			tube(character, [[side * 0.59, 3.7, 0.11], [side * 0.62, 3.36, 0.12], [side * 0.58, 2.95, 0.14]], 0.01, palette.glow, 20);
		}

		// Face, luminous eye slits, and the swept-back high ponytail.
		ellipsoid(character, [0, 4.51, 0.015], [0.25, 0.34, 0.23], palette.skin);
		ellipsoid(character, [0, 4.75, -0.025], [0.27, 0.17, 0.23], palette.hair);
		ellipsoid(character, [0, 4.44, 0.198], [0.22, 0.1, 0.065], palette.skin);
		for (const side of [-1, 1]) {
			ellipsoid(character, [side * 0.105, 4.54, 0.27], [0.067, 0.022, 0.014], palette.glow, 16);
			tube(character, [[side * 0.04, 4.59, 0.27], [side * 0.11, 4.61, 0.265], [side * 0.18, 4.59, 0.25]], 0.012, palette.suit, 12);
			tube(character, [[side * 0.04, 4.4, 0.252], [side * 0.1, 4.38, 0.26], [side * 0.15, 4.4, 0.245]], 0.009, palette.armor, 10);
		}
		link(character, [0, 4.52, 0.255], [0, 4.45, 0.275], 0.018, palette.suitLight, 0.008);
		ellipsoid(character, [0, 4.37, 0.255], [0.045, 0.012, 0.012], palette.armor, 12);

		const foreheadGem = new THREE.Mesh(new THREE.OctahedronGeometry(0.065, 0), palette.glow);
		foreheadGem.position.set(0, 4.68, 0.25);
		character.add(foreheadGem);

		const ponytail = new THREE.Group();
		character.add(ponytail);
		tube(ponytail, [[0.08, 4.83, -0.08], [0.26, 4.72, -0.14], [0.39, 4.39, -0.17], [0.34, 4.0, -0.13], [0.25, 3.77, -0.07]], 0.14, palette.hair, 32);
		tube(ponytail, [[0.11, 4.82, 0.005], [0.28, 4.66, -0.025], [0.37, 4.35, -0.06], [0.31, 4.02, -0.04]], 0.026, palette.hairLight, 28);
		ellipsoid(character, [0.07, 4.83, -0.02], [0.18, 0.12, 0.16], palette.hairLight);
		tube(character, [[-0.18, 4.75, 0.05], [-0.12, 4.88, 0.02], [-0.03, 4.92, 0]], 0.045, palette.hairLight, 18);
		tube(character, [[-0.06, 4.91, -0.04], [0.05, 4.96, -0.03], [0.15, 4.91, -0.04]], 0.04, palette.hair, 18);

		// Arc-shaped energy seams and a faceted chest sigil.
		tube(character, [[-0.28, 3.76, 0.2], [-0.17, 3.84, 0.25], [0, 3.8, 0.27], [0.17, 3.84, 0.25], [0.28, 3.76, 0.2]], 0.012, palette.glow, 28);
		for (const side of [-1, 1]) {
			tube(character, [[side * 0.27, 3.48, 0.2], [side * 0.2, 3.29, 0.21], [side * 0.12, 3.16, 0.19]], 0.01, palette.glow, 16);
		}
		const chestGem = new THREE.Mesh(new THREE.OctahedronGeometry(0.16, 0), palette.glow);
		chestGem.position.set(0, 3.65, 0.255);
		chestGem.scale.set(0.74, 1.3, 0.52);
		character.add(chestGem);

		const collar = new THREE.Mesh(new THREE.TorusGeometry(0.16, 0.025, 8, 32, Math.PI), palette.glow);
		collar.position.set(0, 4.06, 0.04);
		collar.rotation.x = Math.PI;
		character.add(collar);

		// Floating shards move independently around the guardian.
		const shardField = new THREE.Group();
		scene.add(shardField);
		const shards = [
			[-1.42, 4.25, 0.2, 0.27, 0.55, 0.22],
			[1.52, 3.72, -0.45, 0.2, 0.38, -0.3],
			[-1.55, 2.55, -0.55, 0.25, 0.43, 0.5],
			[1.34, 1.9, 0.2, 0.18, 0.35, -0.4],
			[-0.96, 1.35, 0.68, 0.15, 0.3, 0.2],
			[0.95, 4.85, -0.1, 0.13, 0.27, 0.7],
			[-0.92, 4.95, -0.35, 0.14, 0.3, -0.3],
			[0.9, 2.65, -0.8, 0.13, 0.24, 0.1]
		];
		const shardMeshes = shards.map(([x, y, z, sx, sy, rz], index) => {
			const mesh = crystal(shardField, [x, y, z], [sx, sy, sx * 0.8], [0.2, index * 0.8, rz]);
			mesh.userData.phase = index * 0.93;
			mesh.userData.baseY = y;
			return mesh;
		});

		// A soft-lit dais anchors the floating figure without distracting from it.
		const dais = new THREE.Mesh(
			new THREE.CylinderGeometry(1.05, 1.18, 0.08, 64),
			new THREE.MeshStandardMaterial({ color: '#12253d', roughness: 0.28, metalness: 0.7, emissive: '#061d31', emissiveIntensity: 0.65 })
		);
		dais.position.y = -0.08;
		dais.receiveShadow = true;
		scene.add(dais);

		const daisRing = new THREE.Mesh(
			new THREE.TorusGeometry(1.08, 0.012, 8, 96),
			palette.glow
		);
		daisRing.rotation.x = Math.PI / 2;
		daisRing.position.y = -0.027;
		scene.add(daisRing);

		const starPositions = new Float32Array(420 * 3);
		for (let i = 0; i < starPositions.length; i += 3) {
			starPositions[i] = (Math.random() - 0.5) * 18;
			starPositions[i + 1] = Math.random() * 8;
			starPositions[i + 2] = (Math.random() - 0.5) * 14 - 2;
		}
		const starGeometry = new THREE.BufferGeometry();
		starGeometry.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
		const stars = new THREE.Points(
			starGeometry,
			new THREE.PointsMaterial({ color: '#9beaff', size: 0.018, transparent: true, opacity: 0.68 })
		);
		scene.add(stars);

		document.querySelector('#spin').addEventListener('click', event => {
			controls.autoRotate = !controls.autoRotate;
			event.currentTarget.textContent = controls.autoRotate ? 'Pause rotation' : 'Resume rotation';
			event.currentTarget.setAttribute('aria-pressed', String(controls.autoRotate));
		});

		document.querySelector('#reset').addEventListener('click', () => {
			camera.position.set(7.7, 5.2, 11.5);
			controls.target.set(0, 2.65, 0);
			controls.update();
		});

		window.addEventListener('resize', () => {
			camera.aspect = window.innerWidth / window.innerHeight;
			camera.updateProjectionMatrix();
			renderer.setSize(window.innerWidth, window.innerHeight);
			renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
		});

		const clock = new THREE.Clock();
		function animate() {
			requestAnimationFrame(animate);
			const time = clock.getElapsedTime();
			root.position.y = Math.sin(time * 1.1) * 0.025;
			ponytail.rotation.z = Math.sin(time * 1.4) * 0.025;
			shardMeshes.forEach(mesh => {
				mesh.position.y = mesh.userData.baseY + Math.sin(time * 1.2 + mesh.userData.phase) * 0.09;
				mesh.rotation.y += 0.003;
			});
			controls.update();
			renderer.render(scene, camera);
		}
		animate();
	</script>
</body>
</html>
