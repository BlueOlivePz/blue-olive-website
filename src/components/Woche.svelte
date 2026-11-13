<script>
  import { onMount } from 'svelte';
  import { initWeek } from '../lib/roestwetter.js';

  onMount(() => {
    initWeek();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec wk" id="woche" data-theme="dark" aria-labelledby="wk-title">
<header class="sec__head"><p class="mono">05 — Der Kompass, Fortsetzung</p><h2 class="hd" id="wk-title"><span>Jetzt dein</span><span>Fahrplan</span></h2><p class="lede">Wähl die Website-Art und sag mir, wie weit deine Inhalte sind. Du bekommst die sieben Schritte bis zum Start, und zu jedem drei passende Größen.</p></header>
<div class="wk__ctl">
<div class="wk__row"><span class="mono">Inhalte</span><div class="seg" id="wk-season" role="radiogroup" aria-label="Stand deiner Inhalte"></div></div>
<div class="wk__row"><span class="mono">Art</span><div class="wk__skies" id="wk-sky" role="radiogroup" aria-label="Website-Art"></div></div>
<div class="wk__row"><span class="mono">Seiten</span><div class="temp"><input type="range" id="wk-temp" min="1" max="20" step="1" value="6" aria-label="Ungefähre Zahl der Seiten"><output id="wk-temp-out" for="wk-temp">6</output></div></div>
<div class="wk__row"><span class="mono">Weiter</span><div class="where"><button class="btn btn--light" id="wk-locate" data-magnetic data-cursor="link"><span>Mit diesem Fahrplan anfragen</span><i>→</i></button>
<p class="where__status mono" id="wk-status" aria-live="polite"></p></div></div>
</div>
<div class="wk__days" id="wk-days" role="tablist" aria-label="Die sieben Schritte"></div>
<div class="wk__day" id="wk-day" aria-live="polite"></div>
<p class="wk__fine mono">Der Fahrplan ist ein Richtwert. Den genauen Ablauf und die Termine bekommst du mit dem Angebot.</p>
</section>
