<script>
  import { onMount } from 'svelte';
  import { initGrid } from '../lib/roestwetter.js';

  onMount(() => {
    initGrid();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec col" id="sorten" data-theme="light" aria-labelledby="col-title">
<header class="sec__head"><p class="mono">02 — Die Website-Arten</p><h2 class="hd" id="col-title"><span>Die</span><span>Sechs</span></h2>
<p class="lede">Fahr über ein Bild, und es wird zum Film. <b>Zieh quer über ein Objekt, um es freizustellen.</b> Tipp auf einen Punkt und wähl deine Ausgangslage.</p>
<div class="filters" id="filters" role="tablist" aria-label="Nach Art filtern"></div></header>
<div class="grid" id="grid"></div>
</section>
