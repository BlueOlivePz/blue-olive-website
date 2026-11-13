<script>
  import { onMount } from 'svelte';
  import { initPipe } from '../lib/roestwetter.js';

  onMount(() => {
    initPipe();
  });
</script>

<!-- svelte-ignore a11y_no_noninteractive_tabindex, a11y_media_has_caption, a11y_no_redundant_roles, a11y_interactive_supports_focus -->
<section class="pipe" id="weg" data-theme="dark" aria-labelledby="pipe-title">
<div class="pipe__pin"><header class="pipe__head"><p class="mono">Vom Gespräch zur fertigen Seite</p><h2 class="hd" id="pipe-title"><span>Erst der Schliff,</span><span>dann der Glanz</span></h2></header>
<ol class="pipe__steps" id="pipe-steps"><li><b>01 Zuhören</b><span>Am Anfang steht ein roher Block: deine Ziele, deine Kunden, dein Angebot. Ich frage, bis klar ist, was die Seite leisten soll.</span></li><li><b>02 Entwerfen</b><span>Aus dem Block wird ein Raster. Zuerst fürs Handy, mit echten Inhalten, abgestimmt mit dir.</span></li><li><b>03 Bauen</b><span>Baustein für Baustein entsteht die Seite: statisch, schnell und laufend gemessen.</span></li><li><b>04 Glänzen</b><span>Das Standbild wird zum Film. Die Seite geht online, und der Glanz kommt zum Schluss.</span></li></ol>
<div class="pipe__stage"><div class="pipe__refs" id="pipe-refs" aria-hidden="true"></div><div class="pipe__frame" id="pipe-frame"><span class="pipe__state mono" id="pipe-state">Zuhören</span></div></div>
<div class="pipe__prompt mono" id="pipe-prompt" aria-hidden="true"></div><div class="pipe__rail" aria-hidden="true"><i id="pipe-rail"></i></div></div>
</section>
