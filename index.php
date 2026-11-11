<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0d17">
    <title>Vega | Field Mechanic</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <style>
        :root {
            color-scheme: dark;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #f3f0e9;
            background: #0b0d17;
            font-synthesis: none;
            text-rendering: optimizeLegibility;
        }

        * { box-sizing: border-box; }
        body { min-width: 320px; min-height: 100vh; margin: 0; }
        button { font: inherit; }

        .app {
            display: flex;
            flex-direction: column;
            width: min(100% - 48px, 1440px);
            min-height: 100vh;
            margin: 0 auto;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 92px;
            border-bottom: 1px solid #ffffff17;
        }

        .brand { display: flex; align-items: center; gap: 14px; }
        .brand-mark {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border: 1px solid #ff9a24;
            border-radius: 50%;
            color: #ff9a24;
            font-size: 19px;
        }
        .eyebrow, .brand-name, .tag, .detail-label, .controls-hint {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
        }
        .brand-name { color: #f2eee6; }
        .brand-subtitle { margin-top: 4px; color: #777986; font-size: 11px; }
        .tag { color: #a7a9b5; }
        .tag span { color: #ff9a24; }

        main { display: grid; flex: 1; grid-template-columns: minmax(0, 1fr) 250px; gap: 32px; padding: 28px 0; }
        .stage {
            position: relative;
            min-height: 590px;
            overflow: hidden;
            border: 1px solid #ffffff12;
            border-radius: 18px;
            background:
                radial-gradient(ellipse at 50% 43%, #252437 0, #151725 40%, #0d101b 76%);
        }
        #scene { display: block; width: 100%; height: 100%; min-height: 590px; }
        .stage-label {
            position: absolute;
            top: 24px;
            left: 25px;
            color: #a9aab6;
            pointer-events: none;
        }
        .stage-label strong { display: block; margin-top: 8px; color: #f3f0e9; font-size: 16px; letter-spacing: .04em; }
        .stage-label .eyebrow { color: #ff9a24; }
        .controls-hint {
            position: absolute;
            right: 24px;
            bottom: 23px;
            color: #8e909e;
            font-size: 9px;
            pointer-events: none;
        }

        aside { display: flex; flex-direction: column; padding: 12px 0; }
        .eyebrow { color: #ff9a24; }
        h1 { margin: 10px 0 7px; font-size: clamp(30px, 4vw, 42px); font-weight: 650; letter-spacing: -.05em; }
        .role { margin: 0; color: #a7a9b5; font-size: 14px; }
        .divider { height: 1px; margin: 27px 0; background: #ffffff17; }
        .detail { margin-bottom: 20px; }
        .detail-label { color: #777986; font-size: 9px; }
        .detail-value { margin-top: 7px; color: #e8e5de; font-size: 13px; }
        .swatches { display: flex; gap: 8px; margin-top: 9px; }
        .swatch { width: 17px; height: 17px; border: 1px solid #ffffff30; border-radius: 50%; }
        .swatch.silver { background: #b7b7ba; }
        .swatch.charcoal { background: #292a31; }
        .swatch.signal { background: #ff941e; }
        .actions { display: flex; flex-wrap: wrap; gap: 9px; margin-top: auto; padding-top: 28px; }
        .actions button {
            min-height: 42px;
            padding: 0 13px;
            border: 1px solid #ffffff24;
            border-radius: 7px;
            color: #e8e5de;
            background: #ffffff08;
            cursor: pointer;
            transition: border-color .2s, background .2s;
        }
        .actions button:hover { border-color: #ff9a24; background: #ff9a2414; }
        .actions .primary { border-color: #ff9a24; color: #ffb456; }
        footer {
            display: flex;
            justify-content: space-between;
            padding: 17px 0 20px;
            border-top: 1px solid #ffffff17;
            color: #777986;
            font-size: 11px;
        }
        #load-message {
            position: absolute;
            inset: 50% auto auto 50%;
            color: #a7a9b5;
            font-size: 13px;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        @media (max-width: 800px) {
            .app { width: min(100% - 28px, 640px); }
            header { min-height: 74px; }
            main { grid-template-columns: 1fr; gap: 18px; padding: 18px 0; }
            .stage, #scene { min-height: min(68vh, 590px); }
            aside { padding: 8px 2px 4px; }
            .divider { margin: 19px 0; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; }
            .actions { margin-top: 0; padding-top: 8px; }
            footer { gap: 12px; font-size: 10px; }
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
    <script type="module" src="assets/character.js"></script>
</head>
<body>
    <div class="app">
        <header>
            <div class="brand">
                <div class="brand-mark" aria-hidden="true">✦</div>
                <div>
                    <div class="brand-name">Armory / Character Lab</div>
                    <div class="brand-subtitle">Independent 3D character study</div>
                </div>
            </div>
            <div class="tag"><span>●</span> Model preview</div>
        </header>

        <main>
            <section class="stage" aria-label="Interactive 3D character preview">
                <canvas id="scene" aria-label="Three-dimensional view of Vega, a mechanic"></canvas>
                <div class="stage-label">
                    <div class="eyebrow">Workshop file / 001</div>
                    <strong>VEGA · FIELD MECHANIC</strong>
                </div>
                <div id="load-message" role="status">Loading 3D preview…</div>
                <div class="controls-hint">DRAG TO ORBIT&nbsp; · &nbsp;SCROLL TO ZOOM</div>
            </section>

            <aside>
                <div>
                    <div class="eyebrow">Character profile</div>
                    <h1>Vega</h1>
                    <p class="role">Field mechanic · Frontier unit</p>
                </div>
                <div class="divider"></div>
                <div class="detail-grid">
                    <div class="detail">
                        <div class="detail-label">Specialty</div>
                        <div class="detail-value">Heavy equipment repair</div>
                    </div>
                    <div class="detail">
                        <div class="detail-label">Loadout</div>
                        <div class="detail-value">Arc wrench · Utility rig</div>
                    </div>
                    <div class="detail">
                        <div class="detail-label">Uniform</div>
                        <div class="detail-value">Reinforced work suit</div>
                    </div>
                    <div class="detail">
                        <div class="detail-label">Palette</div>
                        <div class="swatches" aria-label="Silver, charcoal, and safety orange">
                            <span class="swatch silver"></span>
                            <span class="swatch charcoal"></span>
                            <span class="swatch signal"></span>
                        </div>
                    </div>
                </div>
                <div class="actions">
                    <button class="primary" id="rotation-button" type="button" aria-pressed="true">Pause spin</button>
                    <button id="reset-button" type="button">Reset view</button>
                </div>
            </aside>
        </main>

        <footer>
            <span>VEGA / CHARACTER STUDY</span>
            <span>Interactive 3D · Drag to inspect</span>
        </footer>
    </div>
</body>
</html>
