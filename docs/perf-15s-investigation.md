# 15s Mobile Load Investigation

**Site:** laupw1.sg-host.com  
**Symptom:** `window.load` fires after ~15 seconds. All individual network resources finish in <200ms. Document TTFB is ~94ms (PHP is fast).

---

## Confirmed: hero block on page = 15s. No hero block = 313ms total.

This means the culprit is something rendered by the hero block — HTML, CSS, or a loaded resource.

---

## RULED OUT

| What | Why ruled out |
|---|---|
| PHP / TTFB | TTFB is 94ms — server-side is fast |
| Translation system | Removed entirely. Not the cause. |
| Theme itself | Basic theme (no hero block) loads at 313ms |
| SiteGround JS (lazysizes, optimizer) | Present on both fast and slow pages |
| Canvas + requestAnimationFrame | Removed canvas → still 15s |
| SVG SMIL animations | Were 15.44s, but removing them alone didn't fix it |
| JavaScript in general | Removing viewScript entirely → still 15s |

---

## STILL IN HERO BLOCK (not yet isolated)

The hero block currently renders (without canvas or JS):
- `snel_mesh()` — 5 CSS blob animations (`transform`/`opacity`, `blur-[140px]`)
- `snel_panel_open()` — hairline gradient borders + corner stack icons with `@property --snel-angle` conic-gradient animation (`snel-gradient-ring`)
- `snel_panel_close()` — same
- Gradient button — also uses `snel-gradient-ring` (spinning conic-gradient ring)
- Inline SVGs — Google logo (4 paths), 5 star SVGs, 2 arrow SVGs
- `@property --snel-angle` in global CSS — CSS Houdini, known Safari/mobile issue

---

## Most likely remaining suspects

1. **`@property --snel-angle` + conic-gradient animation** — CSS Houdini `@property` is known to cause Safari/mobile to recalculate on every paint. Could hold the browser busy for seconds.
2. **`blur-[140px]` on 5 overlapping elements** — extreme blur radius triggers GPU compositing. On mobile, painting 5×`h-[46rem] w-[46rem] rounded-full blur-[140px]` is extremely expensive.
3. **`snel_panel_open/close` corner icons** — the `snel-gradient-ring` conic-gradient spins at 2s linear infinite.

---

## Next steps to try (in order)

1. **Remove snel_mesh() from hero render.php** — does 15s go away? (tests the blur blobs)
2. **Remove snel_panel_open/close + gradient button** — does 15s go away? (tests @property conic-gradient)
3. **Remove @property + snel-gradient-ring from CSS** — does 15s go away? (tests Houdini globally)

---

## Network snapshot (with canvas removed, still 15s)

```
document                   94.2ms
siteground-optimizer CSS   31.3ms
css2 (Google Fonts CSS)    91.9ms
lazysizes.min.js           29.7ms
main.js                    25.5ms
woff2 (Inter font)         42.9ms
---
All resources done:        ~200ms
window.load fires:         15.33s
```

The 15 seconds is NOT a network problem. Something is blocking the browser's load event after all resources are downloaded.
