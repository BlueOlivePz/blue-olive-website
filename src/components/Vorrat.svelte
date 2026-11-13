<script>
  import { onMount } from 'svelte';
  import { initGround } from '../lib/roestwetter.js';

  onMount(() => {
    initGround();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec gr" id="vorrat" data-theme="light" aria-labelledby="gr-title">
<header class="sec__head"><p class="mono">07 — Die Großen</p><h2 class="hd" id="gr-title"><span>Für große</span><span>Pläne</span></h2><p class="lede">Drei Formate für mehr als eine schnelle Seite: die Unternehmens-Website, die Landingpage für Kampagnen und das 3D-Scroll-Erlebnis. <span class="hint-touch">Wisch zum Wechseln.</span><span class="hint-mouse">Beweg den Zeiger, und sie drehen sich mit.</span></p></header>
<div class="gr__body"><div class="gr__count" aria-live="polite"><b id="gr-i">01</b><i>/</i><span id="gr-n">03</span></div><div class="gr__stage" id="gr-stage" data-cursor="drag" data-cursor-label="Wischen"></div><div class="gr__info" id="gr-info"></div></div>
<p class="gr__sign" aria-label="Erst schnell, dann schön."><span>Erst schnell,</span><span>dann schön.</span></p><p class="gr__signsub mono">Jede Seite wird gemessen, bevor sie online geht.</p>
</section>
