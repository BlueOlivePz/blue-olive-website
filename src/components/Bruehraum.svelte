<script>
  import { onMount } from 'svelte';
  import { initFit } from '../lib/roestwetter.js';

  onMount(() => {
    initFit();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec fit" id="bruehraum" data-theme="light" aria-labelledby="fit-title">
<header class="sec__head"><p class="mono">06 — Der Becher</p><h2 class="hd" id="fit-title"><span>Miss</span><span>es aus</span></h2><p class="lede">Stell den Becher auf deinen Bedarf ein: Umfang, Bewegung und Bildanteil. Dann füll jede Website-Art hinein und sieh, wie gut sie dazu passt.</p></header>
<div class="fit__body is-setup" id="fit-body">
<div class="fit__stage" id="fit-stage"><div class="fit__ruler" aria-hidden="true"></div><canvas id="cup" role="img" aria-label="Dein Becher"></canvas><p class="fit__cond mono" id="fit-cond"></p><div class="fit__mark" id="fit-mark" aria-hidden="true"></div><button class="fit__edit mono" id="fit-edit" data-cursor="link">Becher ändern</button><p class="fit__hint mono">Gedrückt halten: in den Becher schauen</p></div>
<div class="fit__side">
<form class="fit__panel" id="fit-panel" aria-label="Dein Becher"><p class="mono"><span>Schritt eins von zwei</span><span>Dein Becher</span></p><h3>Mach ihn <em>zu deinem</em></h3>
<div class="sl"><label for="f-size">Umfang</label><input type="range" id="f-size" min="40" max="400" step="10" value="220"><output id="f-size-o"></output></div>
<div class="sl"><label for="f-str">Bewegung</label><input type="range" id="f-str" min="45" max="90" step="1" value="62"><output id="f-str-o"></output></div>
<div class="sl"><label for="f-milk">Bilder</label><input type="range" id="f-milk" min="0" max="60" step="5" value="0"><output id="f-milk-o"></output></div>
<p class="mono" id="f-read"></p><div><button class="btn" type="submit" data-magnetic data-cursor="link"><span>Becher hinstellen</span><i>→</i></button></div></form>
<div class="fit__ward"><div class="fit__chips" id="fit-chips" role="tablist" aria-label="Art"></div><div class="fit__grid" id="fit-grid" data-cursor-label="Einfüllen"></div></div>
<aside class="fit__sum" id="fit-sum" aria-live="polite"></aside>
</div></div>
</section>
