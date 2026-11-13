# Blue Olive – Website

Astro 7 (statisch) mit Svelte 5, Threlte und three.js, gebaut mit pnpm.
Aufbau, Animationen und Interaktionen folgen der Referenz; Texte, Medien, Logo und Farben sind Blue Olive.

## Befehle
    pnpm install
    pnpm dev        # Vorschau unter http://localhost:4321
    pnpm build      # erzeugt ./dist

## Hochladen
Inhalt von `dist/` in den Zielordner der Domain laden, einschließlich `.htaccess`, `anfrage.php`,
`_anfrage/` und `Baustelle-Anfrage/`. Den Ordner `Baustelle-Anfrage/` auf dem Server nie löschen:
dort liegen die Anfrage-PDFs und der Zähler.

## Wo was steht
- Texte und Daten der sechs Website-Arten, Phasen, Kompass, Fahrplan: `src/lib/roestwetter.js`
- Abschnitts-Überschriften und Einleitungen: `src/components/*.svelte` und `*.astro`
- Farben: `src/styles/site.css`, erste Zeile (`:root`)
- Anfrage-Fenster: `src/components/Anfrage.astro`; Mail und PDF: `public/anfrage.php`, `public/_anfrage/`
- Medien: `public/m/` (p = Bilder, c = freigestellt, s = Szenen, v = Filme)

Interne Schlüssel wie `morgennebel` stammen aus dem Nachbau und sind nur Dateinamen.
