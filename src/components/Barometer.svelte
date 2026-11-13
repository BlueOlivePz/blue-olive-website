<script>
  import { onMount } from 'svelte';
  import { initBaro } from '../lib/roestwetter.js';

  onMount(() => {
    initBaro();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec fc" id="barometer" data-theme="dark" aria-labelledby="fc-title">
<header class="sec__head"><p class="mono">05 — Der Kompass</p><h2 class="hd" id="fc-title"><span>Stell den</span><span>Umfang</span></h2><p class="lede">Wie viel Website brauchst du wirklich? Dreh an der Nadel, von schlicht bis groß. Der Kompass zeigt, welche Website-Art dazu passt.</p></header>
<div class="fc__body"><div class="baro" id="baro" role="slider" tabindex="0" aria-label="Umfang des Projekts" aria-valuemin="980" aria-valuemax="1040" aria-valuenow="1010" data-cursor="drag" data-cursor-label="Drehen"><svg viewBox="0 0 200 200" id="baro-svg" aria-hidden="true"></svg><div class="baro__glass"><video id="baro-video" muted loop playsinline preload="none"></video></div><div class="baro__needle" id="baro-needle"><i></i></div></div>
<div class="fc__out" id="fc-out" aria-live="polite"></div></div>
</section>
