<script>
  import { onMount } from 'svelte';
  import { initRail } from '../lib/roestwetter.js';

  onMount(() => {
    initRail();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec rail" id="leine" data-theme="dark" aria-labelledby="rail-title">
<header class="sec__head"><p class="mono">01 — Die Leine</p><h2 class="hd" id="rail-title"><span>An der</span><span>Leine</span></h2>
<p class="lede">Sechs Website-Arten, aufgehängt wie Musterkarten. <span class="hint-mouse">Zieh die Leine und lass los.</span><span class="hint-touch">Schubs die Leine an.</span> Tipp auf eine Karte, um sie umzudrehen. Zieh eine nach unten, und sie landet auf deiner Anfrage-Liste.</p></header>
<div class="rail__stage" id="rail-stage" data-cursor="drag" data-cursor-label="Ziehen"><div class="rail__sky" aria-hidden="true"></div><canvas class="rail__wxc" id="rail-wxc" aria-hidden="true"></canvas><div class="rail__bar" aria-hidden="true"></div><div class="rail__track" id="rail-track"></div><div class="rail__drop mono" id="rail-drop" aria-hidden="true"><b>Anfrage-Liste</b><span>Loslassen zum Merken</span></div></div>
<div class="rail__foot"><div class="rail__wx" id="rail-wx" role="radiogroup" aria-label="Bewegung an der Leine"></div><p class="rail__read mono" id="rail-read" aria-live="polite"></p><div class="rail__nav"><button class="rbtn" id="rail-prev" aria-label="Vorherige Karten" data-cursor="link">←</button><button class="rbtn" id="rail-next" aria-label="Nächste Karten" data-cursor="link">→</button></div></div>
</section>
