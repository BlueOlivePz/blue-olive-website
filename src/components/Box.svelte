<script>
  import { onMount } from 'svelte';
  import { initBoard } from '../lib/roestwetter.js';

  onMount(() => {
    initBoard();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec bd" id="box" data-theme="light" aria-labelledby="bd-title">
<header class="sec__head"><p class="mono">08 — Der Fahrplan</p><h2 class="hd" id="bd-title"><span>Plan ihn</span><span>selbst</span></h2><p class="lede"><span class="hint-mouse">Zieh Karten auf das Brett.</span><span class="hint-touch">Tipp Karten an, um sie aufs Brett zu legen.</span> Sie finden ihren Platz. Websites wachsen in Stufen: eine für jetzt, eine für bald, eine für später.</p></header>
<div class="bd__body"><div class="bd__rail" id="bd-rail" aria-label="Website-Arten"></div><div class="bd__board" id="bd-board" aria-label="Planbrett"><div id="bd-slots"></div><p class="bd__empty" id="bd-empty"><em>Ein leeres Brett.</em><br>Fang mit dem Jetzt an.</p></div><aside class="bd__sum" id="bd-sum"></aside></div>
</section>
