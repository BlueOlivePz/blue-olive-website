<script>
  import { onMount } from 'svelte';
  import { initChapters } from '../lib/roestwetter.js';

  onMount(() => {
    initChapters();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="lb" id="wetterlagen" aria-label="Projektphasen">
<header class="lb__head sec__head" data-theme="dark"><p class="mono">04 — Projektphasen</p><h2 class="hd"><span>Vier</span><span>Phasen</span></h2><p class="lede">Scrollen spult den Film. Ein Kapitel pro Phase.</p></header>
<div id="chapters"></div>
</section>
