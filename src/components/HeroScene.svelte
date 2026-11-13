<script>
  // Threlte-Szene des Heros: ein bildschirmfüllendes Rechteck mit eigenem Shader.
  // Der Film läuft zuerst nur in der Wortmarke, beim Scrollen fährt die Kamera
  // in einen Buchstaben, bis der Film das ganze Bild füllt. Der Zeiger wirft Wellen.
  import { onMount } from 'svelte';
  import { T, useTask, useThrelte } from '@threlte/core';
  import {
    CanvasTexture,
    LinearFilter,
    TextureLoader,
    Vector2,
    Vector3,
    VideoTexture,
  } from 'three';
  import { drawMask, reducedMotion } from '../lib/roestwetter.js';

  let { video, onready } = $props();

  const { renderer } = useThrelte();
  const el = renderer.domElement;

  const maskCanvas = document.createElement('canvas');
  const maskTex = new CanvasTexture(maskCanvas);
  maskTex.minFilter = maskTex.magFilter = LinearFilter;
  maskTex.generateMipmaps = false;

  let videoTex = null;
  const posterTex = new TextureLoader().load('/m/v/hero.jpg', (t) => {
    if (videoTex) return;
    uniforms.uVR.value.set(t.image.naturalWidth, t.image.naturalHeight);
    uniforms.uOn.value = 1;
  });
  posterTex.minFilter = posterTex.magFilter = LinearFilter;
  posterTex.generateMipmaps = false;

  const uniforms = {
    uV: { value: posterTex },
    uM: { value: maskTex },
    uR: { value: new Vector2(1, 1) },
    uVR: { value: new Vector2(16, 9) },
    uPt: { value: new Vector2(0.5, 0.5) },
    uC: { value: new Vector2(0.5, 0.5) },
    uP: { value: 0 },
    uT: { value: 0 },
    uK: { value: 0 },
    uOn: { value: 0 },
    uBg: { value: new Vector3(0.016, 0.024, 0.11) },
  };

  const vertexShader = /* glsl */ `
    varying vec2 vUv;
    void main() {
      vUv = uv;
      gl_Position = vec4(position.xy, 0.0, 1.0);
    }`;

  const fragmentShader = /* glsl */ `
    precision highp float;
    varying vec2 vUv;
    uniform sampler2D uV, uM;
    uniform vec2 uR, uVR, uPt, uC;
    uniform float uP, uT, uK, uOn;
    uniform vec3 uBg;
    // Rechnen mit Ursprung oben links, beim Abtasten zurückdrehen.
    vec4 smp(sampler2D t, vec2 p) { return texture2D(t, vec2(p.x, 1.0 - p.y)); }
    vec2 cov(vec2 u) {
      float ra = uR.x / uR.y, va = uVR.x / uVR.y;
      vec2 s = ra > va ? vec2(1.0, va / ra) : vec2(ra / va, 1.0);
      return (u - 0.5) * s + 0.5;
    }
    void main() {
      vec2 u = vec2(vUv.x, 1.0 - vUv.y);
      vec2 a = vec2(uR.x / uR.y, 1.0);
      vec2 d = (u - uPt) * a;
      float l = length(d);
      float rip = sin(l * 36.0 - uT * 4.5) * exp(-l * 6.5) * uK;
      vec2 du = u + normalize(d + vec2(1e-4)) * rip * 0.014;
      du.y -= exp(-l * 5.0) * uK * 0.01;
      float q = clamp(uP / 0.5, 0.0, 1.0);
      float z = 1.0 + q * q * q * 70.0;
      vec2 mu = (u - uC) / z + uC;
      float m = smp(uM, mu).r;
      float full = smoothstep(0.38, 0.5, uP);
      float rev = max(m, full);
      vec3 c = smp(uV, cov(du)).rgb * uOn;
      c *= 1.0 + rip * 1.4;
      vec3 g = uBg + c * 0.075;
      vec3 o = mix(g, c, rev);
      o *= mix(1.0, 0.72 + 0.28 * smoothstep(1.0, 0.35, u.y), full);
      gl_FragColor = vec4(o, 1.0);
    }`;

  let mark = null;
  let lastW = 0;
  let lastH = 0;
  const pt = [0.5, 0.5];
  const tp = [0.5, 0.5];
  let k = 0;

  function resize(force = false) {
    const W = el.clientWidth;
    const H = el.clientHeight;
    if (!W || !H || (!force && W === lastW && H === lastH)) return;
    lastW = W;
    lastH = H;
    const dpr = renderer.getPixelRatio();
    const r = drawMask(maskCanvas, W, H, dpr);
    mark = r.mark;
    uniforms.uC.value.set(r.c[0], r.c[1]);
    uniforms.uR.value.set(W * dpr, H * dpr);
    maskTex.needsUpdate = true;
  }

  const task = useTask((delta) => {
    resize();
    if (!videoTex && video && video.readyState >= 2 && video.videoWidth) {
      videoTex = new VideoTexture(video);
      videoTex.minFilter = videoTex.magFilter = LinearFilter;
      videoTex.generateMipmaps = false;
      videoTex.colorSpace = posterTex.colorSpace; // beide gleich behandeln: unverändert durchreichen
      uniforms.uV.value = videoTex;
      uniforms.uVR.value.set(video.videoWidth, video.videoHeight);
      uniforms.uOn.value = 1;
    }
    const dx = tp[0] - pt[0];
    const dy = tp[1] - pt[1];
    pt[0] += dx * 0.09;
    pt[1] += dy * 0.09;
    k = Math.min(1, k * 0.955 + Math.hypot(dx, dy) * 0.5);
    uniforms.uPt.value.set(pt[0], pt[1]);
    uniforms.uK.value = reducedMotion() ? 0 : k;
    uniforms.uT.value += delta;
  });

  onMount(() => {
    const host = el.parentElement ?? el;
    const move = (e) => {
      const r = el.getBoundingClientRect();
      tp[0] = (e.clientX - r.left) / r.width;
      tp[1] = (e.clientY - r.top) / r.height;
    };
    host.addEventListener('pointermove', move, { passive: true });
    host.addEventListener('pointerdown', move, { passive: true });
    resize(true);
    onready?.({
      resize: () => resize(true),
      get mark() {
        return mark;
      },
      setProgress(p) {
        uniforms.uP.value = p;
      },
      start: () => task.start(),
      stop: () => task.stop(),
    });
    return () => {
      host.removeEventListener('pointermove', move);
      host.removeEventListener('pointerdown', move);
      maskTex.dispose();
      posterTex.dispose();
      videoTex?.dispose();
    };
  });
</script>

<T.Mesh frustumCulled={false}>
  <T.PlaneGeometry args={[2, 2]} />
  <T.ShaderMaterial {vertexShader} {fragmentShader} {uniforms} depthTest={false} depthWrite={false} />
</T.Mesh>
