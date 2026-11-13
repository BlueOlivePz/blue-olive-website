<script>
  import { onMount } from 'svelte';
  import { Canvas } from '@threlte/core';
  import HeroScene from './HeroScene.svelte';
  import { initHero } from '../lib/roestwetter.js';

  let video = $state();
  let ready = $state(false);

  onMount(() => {
    // Threlte nur starten, wenn der Browser WebGL kann; sonst Video + Textmarke als Rückfall.
    let ok = false;
    try {
      const c = document.createElement('canvas');
      ok = !!(c.getContext('webgl2') || c.getContext('webgl'));
    } catch {}
    if (ok) ready = true;
    else initHero(null);
  });
</script>

<!-- svelte-ignore a11y_media_has_caption -->
<section class="hero" id="hero" data-theme="dark" aria-label="Projektfilm">
<div class="hero__pin">
<video bind:this={video} class="hero__video" id="hero-video" muted loop playsinline preload="auto" poster="/m/v/hero.jpg" aria-hidden="true"></video>
<div class="hero__gl" id="hero-gl" aria-hidden="true">
{#if ready}
<Canvas>
<HeroScene {video} onready={(api) => initHero(api)} />
</Canvas>
{/if}
</div>
<div class="hero__fallback" aria-hidden="true">Blue<br>Olive</div>
<h1 class="sr">Blue Olive. Webdesign aus Ofterdingen: Websites mit Glanz, schnell auf jedem Handy.</h1>
<div class="hero__ui hero__ui--a">
<p class="hero__tl mono">Webdesign<br>aus Ofterdingen</p>
<p class="hero__tr mono">Ein-Mann-Studio<br>Panajotis Zekos</p>
<p class="hero__tag"><em>Websites<br>mit Glanz.</em></p>
<dl class="hero__fc mono" aria-label="Auf einen Blick"><div><dt>Entwurf</dt><dd>zuerst fürs Handy</dd></div><div><dt>Technik</dt><dd>Astro, statisch</dd></div><div><dt>Gemessen</dt><dd>vor dem Start</dd></div><div><dt>Klarheit</dt><dd><span id="hero-vis">2</span> %</dd></div></dl>
<p class="hero__hint mono"><span class="hint-mouse">Beweg dich durch den Film. Der Nebel antwortet.</span><span class="hint-touch">Wisch über den Film. Der Nebel antwortet.</span></p>
</div>
<div class="hero__ui hero__ui--b" aria-hidden="true">
<p class="hero__kicker mono">Der Projektfilm · Glas und Chrom · Blue Olive</p>
<p class="hero__big"><span>Frisch</span><span>poliert</span></p>
<a class="btn btn--light" href="#sorten" data-magnetic data-cursor="link"><span>Website-Arten ansehen</span><i aria-hidden="true">↓</i></a>
</div>
<div class="hero__scroll mono" aria-hidden="true">Scroll in den Film hinein</div>
</div>
</section>
