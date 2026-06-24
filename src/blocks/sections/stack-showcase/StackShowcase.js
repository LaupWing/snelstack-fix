/**
 * Stack Showcase — React Three Fiber component.
 *
 * A small, static, glossy-plastic stack of 3 rounded plates on the left. The
 * mouse's X position over the surface crossfades the glass card (bottom-right)
 * between the three layers — the 3D stack itself stays put.
 *
 * @package Snel
 */
import { useEffect, useLayoutEffect, useMemo, useRef, useState } from '@wordpress/element';
import * as THREE from 'three';
import { Canvas, useThree, useFrame } from '@react-three/fiber';
import { RoundedBoxGeometry } from 'three/examples/jsm/geometries/RoundedBoxGeometry.js';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';
import normalUrl from './plastic011-normal.jpg';
import roughUrl from './plastic011-rough.jpg';

const COLORS = ['#0ea5e9', '#8b5cf6', '#db2777']; // darker brand: sky-500 → violet-500 → pink-600

// Initial plate geometry. The height is animated by rebuilding the rounded box
// per frame during a transition (keeps corners uniform — a Y-scale would stretch
// them). Shared constants:
const PLATE_W = 3.2;
const PLATE_RADIUS = 0.26;
const H_NORMAL = 0.7;
const H_TALL = 1.3;
const GAP = 0.16; // gap between plate surfaces (tight, like the original)
const PLATE_GEO = new RoundedBoxGeometry(PLATE_W, H_NORMAL, PLATE_W, 5, PLATE_RADIUS);

// Shared plastic surface maps (loaded once; colour stays per-plate).
const _texLoader = new THREE.TextureLoader();
const NORMAL_MAP = _texLoader.load(normalUrl);
const ROUGH_MAP = _texLoader.load(roughUrl);
[NORMAL_MAP, ROUGH_MAP].forEach((t) => {
	t.wrapS = THREE.RepeatWrapping;
	t.wrapT = THREE.RepeatWrapping;
	t.repeat.set(2, 2);
});

// ─── Background grid of boxes that randomly twinkle brand colors ───────────
const GRID = { cols: 62, rows: 40, step: 0.82 };
const BG_BASE = new THREE.Color('#0b1220');
const BG_ACCENTS = ['#0ea5e9', '#8b5cf6', '#db2777', '#22d3ee', '#f472b6'].map((c) => new THREE.Color(c));
const BG_GEO = new THREE.BoxGeometry(0.7, 0.7, 0.06);
const _bgTmp = new THREE.Color();

function BackgroundBoxes() {
	const mesh = useRef(null);
	const count = GRID.cols * GRID.rows;
	const state = useMemo(
		() => ({
			intensity: new Float32Array(count),
			target: Array.from({ length: count }, () => new THREE.Color()),
		}),
		[count]
	);

	// Lay out the grid once.
	useLayoutEffect(() => {
		const m = mesh.current;
		const dummy = new THREE.Object3D();
		let i = 0;
		for (let r = 0; r < GRID.rows; r++) {
			for (let c = 0; c < GRID.cols; c++) {
				dummy.position.set(
					(c - (GRID.cols - 1) / 2) * GRID.step,
					(r - (GRID.rows - 1) / 2) * GRID.step,
					0
				);
				dummy.updateMatrix();
				m.setMatrixAt(i, dummy.matrix);
				m.setColorAt(i, BG_BASE);
				i++;
			}
		}
		m.instanceMatrix.needsUpdate = true;
		if (m.instanceColor) m.instanceColor.needsUpdate = true;
	}, [count]);

	// Ignite random cells and fade them back to the base color.
	useFrame((s, delta) => {
		const m = mesh.current;
		if (!m) return;
		for (let k = 0; k < 2; k++) {
			if (Math.random() < 0.5) {
				const idx = Math.floor(Math.random() * count);
				state.intensity[idx] = 1;
				state.target[idx].copy(BG_ACCENTS[Math.floor(Math.random() * BG_ACCENTS.length)]);
			}
		}
		let changed = false;
		for (let i = 0; i < count; i++) {
			if (state.intensity[i] > 0.001) {
				state.intensity[i] = Math.max(0, state.intensity[i] - delta * 0.7);
				_bgTmp.copy(BG_BASE).lerp(state.target[i], state.intensity[i]);
				m.setColorAt(i, _bgTmp);
				changed = true;
			}
		}
		if (changed && m.instanceColor) m.instanceColor.needsUpdate = true;
		s.invalidate();
	});

	return (
		<instancedMesh ref={mesh} args={[BG_GEO, undefined, count]} position={[2.2, 0.35, -2.5]}>
			<meshBasicMaterial toneMapped={false} />
		</instancedMesh>
	);
}

// Procedural studio environment for soft plastic reflections (zero download).
function Env() {
	const gl = useThree((s) => s.gl);
	const scene = useThree((s) => s.scene);
	const invalidate = useThree((s) => s.invalidate);
	useEffect(() => {
		const pmrem = new THREE.PMREMGenerator(gl);
		const envTex = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
		scene.environment = envTex;
		invalidate();
		return () => {
			scene.environment = null;
			envTex.dispose();
			pmrem.dispose();
		};
	}, [gl, scene, invalidate]);
	return null;
}

// Aim the camera to the right of the stack so it sits on the LEFT of the frame
// on desktop (card overlays the right). On mobile the card is below, so center it.
function CameraRig() {
	const camera = useThree((s) => s.camera);
	useEffect(() => {
		const update = () => {
			const mobile = window.innerWidth < 768;
			camera.fov = mobile ? 58 : 34;
			camera.lookAt(mobile ? 0 : 2.2, mobile ? -3.2 : 0.35, 0);
			camera.updateProjectionMatrix();
		};
		update();
		window.addEventListener('resize', update);
		return () => window.removeEventListener('resize', update);
	}, [camera]);
	return null;
}

function Plate({ index, color, meshRef, onOver, onOut, onSelect }) {
	const camera = useThree((s) => s.camera);
	const gl = useThree((s) => s.gl);
	// Project the hit point to pixel coords within the canvas (for the label).
	const project = (e) => {
		const v = e.point.clone().project(camera);
		const rect = gl.domElement.getBoundingClientRect();
		return { x: (v.x * 0.5 + 0.5) * rect.width, y: (-v.y * 0.5 + 0.5) * rect.height };
	};
	return (
		<mesh
			ref={meshRef}
			geometry={PLATE_GEO}
			onPointerOver={(e) => {
				e.stopPropagation();
				const p = project(e);
				onOver(index, p.x, p.y);
			}}
			onPointerMove={(e) => {
				e.stopPropagation();
				const p = project(e);
				onOver(index, p.x, p.y);
			}}
			onPointerOut={(e) => {
				e.stopPropagation();
				onOut(index);
			}}
			onClick={(e) => {
				e.stopPropagation();
				onSelect(index);
			}}
		>
			<meshStandardMaterial
				color={color}
				roughness={1}
				metalness={0}
				envMapIntensity={0.3}
				normalMap={NORMAL_MAP}
				normalScale={[0.6, 0.6]}
				roughnessMap={ROUGH_MAP}
			/>
		</mesh>
	);
}

function StackGroup({ active, selected, onOver, onOut, onSelect }) {
	const meshes = useRef([]);
	const heights = useRef(COLORS.map(() => H_NORMAL));
	const inited = useRef(false);

	useFrame(() => {
		const n = COLORS.length;

		// 1) Animate each plate's height; rebuild its rounded geometry while moving.
		for (let i = 0; i < n; i++) {
			const m = meshes.current[i];
			if (!m) continue;
			const targetH = i === selected ? H_TALL : H_NORMAL;
			if (Math.abs(targetH - heights.current[i]) > 0.002) {
				heights.current[i] += (targetH - heights.current[i]) * 0.16;
				const old = m.geometry;
				m.geometry = new RoundedBoxGeometry(PLATE_W, heights.current[i], PLATE_W, 5, PLATE_RADIUS);
				old.dispose();
			}
		}

		// 2) Stack positions from the live heights, with a constant surface gap, centered.
		const h = heights.current;
		const total = h.reduce((a, b) => a + b, 0) + GAP * (n - 1);
		let cur = total / 2;
		const ys = [];
		for (let i = 0; i < n; i++) {
			cur -= h[i] / 2;
			ys[i] = cur;
			cur -= h[i] / 2 + GAP;
		}

		// 3) Apply positions (snap on first frame). No hover scale — the label is enough.
		for (let i = 0; i < n; i++) {
			const m = meshes.current[i];
			if (!m) continue;
			m.position.y = inited.current ? m.position.y + (ys[i] - m.position.y) * 0.18 : ys[i];
		}
		inited.current = true;
	});

	return (
		<group position={[0, 0, 0]} scale={0.85} rotation={[0, 0, 0]}>
			{COLORS.map((c, i) => (
				<Plate
					key={i}
					index={i}
					color={c}
					meshRef={(el) => (meshes.current[i] = el)}
					onOver={onOver}
					onOut={onOut}
					onSelect={onSelect}
				/>
			))}
		</group>
	);
}

export default function StackShowcase({ slides = [] }) {
	const [active, setActive] = useState(-1); // plate hovered (drives the subtle 3D scale)
	const [hover, setHover] = useState(null); // { index, x, y } for the callout label
	const [slide, setSlide] = useState(-1); // card: -1 = stack overview, else section index

	// Card height tracks the active slide's content (animated), with a small floor.
	const slideRefs = useRef({});
	const [cardH, setCardH] = useState(undefined);
	useLayoutEffect(() => {
		const measure = () => {
			const el = slideRefs.current[slide];
			if (el) setCardH(el.offsetHeight);
		};
		measure();
		window.addEventListener('resize', measure);
		return () => window.removeEventListener('resize', measure);
	}, [slide]);

	const onOver = (i, x, y) => {
		setActive(i);
		setHover({ index: i, x, y });
	};
	const onOut = (i) => {
		setActive((cur) => (cur === i ? -1 : cur));
		setHover((h) => (h && h.index === i ? null : h));
	};
	const onSelect = (i) => setSlide((cur) => (cur === i ? -1 : i)); // click active again → overview

	// Navigation states: overview (-1) + each layer. Chevrons step through them.
	const STATES = [-1, ...slides.map((_, i) => i)];
	const stepSlide = (dir) => {
		const idx = STATES.indexOf(slide);
		setSlide(STATES[(idx + dir + STATES.length) % STATES.length]);
	};

	return (
		<div className="relative w-full overflow-hidden rounded-xl bg-slate-950 aspect-[3/5] md:aspect-[3/2]">
			<Canvas
				className="absolute inset-0"
				camera={{ position: [7.58, 3.15, 5.31], fov: 34 }}
				dpr={[1, 2]}
				frameloop="demand"
			>
				<CameraRig />
				<Env />
				<BackgroundBoxes />
				<ambientLight intensity={0.7} />
				<directionalLight position={[8, 11, 9]} intensity={1.15} />
				<directionalLight position={[-7, -1, 5]} intensity={0.45} color="#c7d2fe" />
				<StackGroup active={active} selected={slide} onOver={onOver} onOut={onOut} onSelect={onSelect} />
			</Canvas>

			{/* Gradient fade overlay — desktop only */}
			<div className="pointer-events-none absolute inset-0 z-10 hidden rounded-xl bg-gradient-to-t from-slate-950/70 via-slate-950/10 to-transparent md:block" />

			{/* Hover callout */}
			{hover && (
				<div className="pointer-events-none absolute z-30" style={{ left: hover.x, top: hover.y }}>
					<svg className="absolute overflow-visible" width="1" height="1">
						<line x1="0" y1="0" x2="46" y2="-46" stroke="rgba(255,255,255,0.55)" strokeWidth="1" />
					</svg>
					<span className="absolute left-[52px] top-[-66px] flex items-center gap-2 whitespace-nowrap rounded-md bg-slate-950/60 px-2.5 py-1 text-xs font-medium uppercase tracking-wide text-white ring-1 ring-white/20 backdrop-blur-sm">
						<span className="size-2 rounded-full" style={{ background: slides[hover.index]?.dot }} />
						{slides[hover.index]?.title}
					</span>
				</div>
			)}

			{/* Card — full-width overlay at bottom on mobile, bottom-right on desktop */}
			<div className="absolute bottom-4 left-4 right-4 z-20 md:left-auto md:right-4 md:w-[calc(100%-2rem)] md:max-w-sm">
				<div
					className="relative min-h-[170px] overflow-hidden rounded-lg bg-slate-900 text-white antialiased ring-1 ring-inset ring-white/10 transition-[height] duration-500 ease-out md:bg-slate-950/50 md:ring-white/20 md:backdrop-blur-lg"
					style={{ height: cardH ? `${cardH}px` : undefined }}
				>
					{/* Overview — the whole stack */}
					<div
						ref={(el) => (slideRefs.current[-1] = el)}
						className={`absolute inset-x-0 top-0 p-8 transition-all duration-500 ease-out ${
							slide === -1 ? 'opacity-100 translate-y-0' : 'pointer-events-none translate-y-3 opacity-0'
						}`}
					>
						<span className="inline-flex items-center text-xs font-medium uppercase tracking-wider text-white/60">
							<span className="flex -space-x-1.5">
								{slides.map((s, i) => (
									<span key={i} className="size-3.5 rounded-full ring-2 ring-slate-900" style={{ background: s.dot }} />
								))}
							</span>
							<span className="ml-3">De stack</span>
						</span>
						<p className="mt-5 text-2xl font-semibold leading-tight">Strategie, design en techniek — gestapeld tot één sterke digitale basis.</p>
						<p className="mt-4 text-sm text-white/60">Klik op een laag om te zien wat we doen.</p>
					</div>

					{slides.map((s, i) => (
						<div
							key={i}
							ref={(el) => (slideRefs.current[i] = el)}
							className={`absolute inset-x-0 top-0 p-8 transition-all duration-500 ease-out ${
								i === slide ? 'opacity-100 translate-y-0' : 'pointer-events-none translate-y-3 opacity-0'
							}`}
						>
							<span className="inline-flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-white/60">
								<span className="size-2 rounded-full" style={{ background: s.dot }} />
								{s.title}
							</span>
							<p className="mt-5 text-2xl font-semibold leading-tight">{s.text}</p>
							<a href={s.url} className="mt-6 inline-flex rounded-md bg-white px-3 py-1.5 text-sm font-medium text-slate-950 transition hover:bg-brand-primary hover:text-white">
								{s.cta}
							</a>
						</div>
					))}
				</div>

				{/* Progress bar — chevrons + indicators; the active one fills the width */}
				<div className="mt-3 flex w-1/2 items-center gap-3">
					<button
						type="button"
						onClick={() => stepSlide(-1)}
						aria-label="Vorige"
						className="shrink-0 text-white/50 transition-colors hover:text-white"
					>
						<svg className="size-4" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24" strokeLinecap="round" strokeLinejoin="round">
							<path d="M15 18l-6-6 6-6" />
						</svg>
					</button>

					<div className="flex flex-1 items-center gap-1.5">
						{STATES.map((st) => {
							const isActive = slide === st;
							const c = st === -1 ? '#ffffff' : slides[st].dot;
							return (
								<button
									key={st}
									type="button"
									onClick={() => setSlide(st)}
									aria-label={st === -1 ? 'Overzicht' : slides[st].title}
									className={`h-2 rounded-full transition-all duration-500 ease-out ${isActive ? 'flex-1' : 'w-2'}`}
									style={{ background: isActive ? c : 'rgba(255,255,255,0.22)' }}
								/>
							);
						})}
					</div>

					<button
						type="button"
						onClick={() => stepSlide(1)}
						aria-label="Volgende"
						className="shrink-0 text-white/50 transition-colors hover:text-white"
					>
						<svg className="size-4" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24" strokeLinecap="round" strokeLinejoin="round">
							<path d="M9 18l6-6-6-6" />
						</svg>
					</button>
				</div>
			</div>
		</div>
	);
}
