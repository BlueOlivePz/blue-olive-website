import { defineConfig } from 'astro/config';
import svelte from '@astrojs/svelte';

// Statischer Build nach ./dist – ohne Adapter, ohne Server-Code.
export default defineConfig({
  integrations: [svelte()],
  build: { assets: '_astro' },
  vite: { build: { chunkSizeWarningLimit: 900 } },
});
