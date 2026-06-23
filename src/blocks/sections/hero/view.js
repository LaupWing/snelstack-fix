const canvas = document.querySelector('.snel-beams-canvas');
if (canvas) {
	const ctx = canvas.getContext('2d');
	let W = 0, H = 0;

	function resize() {
		W = canvas.parentElement.offsetWidth;
		H = canvas.parentElement.offsetHeight || 384;
		canvas.width  = W;
		canvas.height = H;
	}

	// Mirrors snel_beam_path — same bezier family, 696×316 viewBox coords
	function makeBeam(i) {
		const dx = 7 * i, dy = -8 * i;
		return [
			[-380 + dx, -189 + dy],
			[-312 + dx,  216 + dy],
			[ 152 + dx,  343 + dy],
			[ 684 + dx,  875 + dy],
		];
	}

	function bezierAt([p0, p1, p2, p3], t) {
		const m = 1 - t;
		return [
			m*m*m*p0[0] + 3*m*m*t*p1[0] + 3*m*t*t*p2[0] + t*t*t*p3[0],
			m*m*m*p0[1] + 3*m*m*t*p1[1] + 3*m*t*t*p2[1] + t*t*t*p3[1],
		];
	}

	function px(x) { return x / 696 * W; }
	function py(y) { return y / 316 * H; }

	// Match original SVG: 50 animated beams + 60 base beams, 10–20s cycle
	const COUNT = 50;
	const beams = Array.from({ length: COUNT }, (_, i) => {
		const dur = 10 + (i * 7) % 11; // 10–20s, mirrors SVG
		return {
			pts:   makeBeam(i),
			t:     -((i * 3) % 10) / dur, // negative start = desynced at load
			speed: 1 / (60 * dur),
			tail:  0.18,
		};
	});

	// Faint base bundle — all 60 beams at low opacity (mirrors SVG base path)
	const BASE = Array.from({ length: 60 }, (_, i) => makeBeam(i));

	function drawBase() {
		ctx.globalAlpha = 0.15;
		ctx.strokeStyle = '#64748b';
		ctx.lineWidth = 0.5;
		BASE.forEach(pts => {
			ctx.beginPath();
			for (let s = 0; s <= 20; s++) {
				const [x, y] = bezierAt(pts, s / 20);
				s === 0 ? ctx.moveTo(px(x), py(y)) : ctx.lineTo(px(x), py(y));
			}
			ctx.stroke();
		});
		ctx.globalAlpha = 1;
	}

	function drawBeam(b) {
		// Normalise t to 0–1, skip if before start
		const t1 = ((b.t % 1) + 1) % 1;
		const t0 = Math.max(0, t1 - b.tail);
		if (t1 < 0.001) return;

		ctx.beginPath();
		for (let s = 0; s <= 20; s++) {
			const t = t0 + (t1 - t0) * (s / 20);
			const [x, y] = bezierAt(b.pts, t);
			s === 0 ? ctx.moveTo(px(x), py(y)) : ctx.lineTo(px(x), py(y));
		}

		const [x0, y0] = bezierAt(b.pts, t0);
		const [x1, y1] = bezierAt(b.pts, t1);
		const g = ctx.createLinearGradient(px(x0), py(y0), px(x1), py(y1));
		g.addColorStop(0,   'rgba(56,189,248,0)');
		g.addColorStop(0.1, '#38bdf8');
		g.addColorStop(0.5, '#a78bfa');
		g.addColorStop(0.9, '#f472b6');
		g.addColorStop(1,   'rgba(244,114,182,0)');

		ctx.strokeStyle = g;
		ctx.lineWidth = 1.5;
		ctx.globalAlpha = 0.85;
		ctx.stroke();
		ctx.globalAlpha = 1;
	}

	function tick() {
		ctx.clearRect(0, 0, W, H);
		drawBase();
		beams.forEach(b => {
			b.t += b.speed;
			drawBeam(b);
		});
		requestAnimationFrame(tick);
	}

	resize();
	window.addEventListener('resize', resize);
	requestAnimationFrame(tick);
}
