<script>
  import { onMount } from 'svelte';
  import { initLooks } from '../lib/roestwetter.js';

  onMount(() => {
    initLooks();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="sec looks" id="momente" data-theme="dark" aria-labelledby="looks-title">
<header class="sec__head"><p class="mono">03 — Der Film</p><h2 class="hd" id="looks-title"><span>Marker, die</span><span>mitziehen</span></h2>
<p class="lede">Vier Phasen, vier Filme. Die Marker sitzen auf dem Bild, nicht auf dem Bildschirm. Tipp einen an, und die Erklärung klappt heraus.</p></header>
<div class="looks__stage"><ol class="looks__index" id="looks-index" aria-label="Phase wählen"></ol>
<div class="looks__film"><div class="looks__big" id="looks-big" aria-hidden="true"></div><div class="film" id="film"><video id="film-video" muted loop playsinline preload="none"></video><div id="film-spots"></div><button class="film__pp mono" id="film-pp" data-cursor="link" aria-label="Film anhalten">Pause</button></div></div>
<aside class="looks__meta" id="looks-meta" aria-live="polite"></aside></div>
</section>
