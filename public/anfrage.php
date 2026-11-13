<?php
// Blue Olive – Baustellen-Seite: nimmt das Formular „Projekt anfragen“ entgegen
// schickt die Anfrage per E-Mail an EMPFAENGER (Text „Neue Anfrage“, Daten als PDF im Anhang)
// und legt die PDF im Ordner Baustelle-Anfrage auf dem Server ab.
// Läuft auf IONOS-Webhosting (PHP 7.4+). Die PDF-Gestaltung steckt in _anfrage/anfrage-pdf.php.
declare(strict_types=1);

date_default_timezone_set('Europe/Berlin');

const EMPFAENGER = 'info@blue-olive.online';
// Absender muss eine Adresse der eigenen Domain sein, sonst verwirft IONOS die Mail.
const ABSENDER = 'info@blue-olive.online';
const SPERRE_SEKUNDEN = 30;

function h(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function seite(int $status, string $titel, string $text): void
{
    http_response_code($status);
    header('Content-Type: text/html; charset=utf-8');
    header('X-Robots-Tag: noindex');
    header('Cache-Control: no-store');
    echo '<!doctype html><html lang="de"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta name="robots" content="noindex"><title>' . h($titel) . ' – Blue Olive</title>'
        . '<link rel="icon" href="/favicon-32.png" sizes="32x32" type="image/png">'
        . '<style>'
        . '@font-face{font-family:"Bebas Neue";src:url("/fonts/bebas-neue-latin.woff2") format("woff2");font-display:swap}'
        . '@font-face{font-family:"Instrument Sans";src:url("/fonts/instrument-sans-latin.woff2") format("woff2");font-weight:400 700;font-display:swap}'
        . 'body{margin:0;min-height:100vh;display:flex;align-items:center;background:#060505;color:#dfe0db;'
        . 'font:400 1.0625rem/1.6 "Instrument Sans",system-ui,sans-serif}'
        . 'main{width:100%;max-width:34rem;margin:0 auto;padding:3rem 1.25rem}'
        . 'h1{margin:0 0 1rem;font:400 clamp(3rem,13vw,5rem)/.95 "Bebas Neue",Impact,sans-serif;color:#eff3f5}'
        . 'a{display:inline-flex;align-items:center;min-height:2.75rem;margin-top:1.5rem;padding:0 1.5rem;'
        . 'border-radius:999px;background:#1d275c;color:#eff3f5;font-weight:600;text-decoration:none;'
        . 'box-shadow:inset 0 0 0 1px rgb(112 147 167/.7)}'
        . 'a:focus-visible{outline:2px solid #aac7d5;outline-offset:3px}'
        . '</style></head><body><main><h1>' . h($titel) . '</h1><p>' . h($text) . '</p>'
        . '<a href="/">Zurück zur Seite</a></main></body></html>';
    exit;
}

function feld(string $name, int $max): string
{
    $wert = isset($_POST[$name]) && is_string($_POST[$name]) ? $_POST[$name] : '';
    $wert = trim(str_replace("\0", '', $wert));
    return function_exists('mb_substr') ? mb_substr($wert, 0, $max, 'UTF-8') : substr($wert, 0, $max);
}

function einzeilig(string $text): string
{
    // Zeilenumbrüche und Steuerzeichen entfernen (Schutz vor Header-Injection)
    return trim((string) preg_replace('/[\x00-\x1F\x7F]+/', ' ', $text));
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: /#anfrage', true, 303);
    exit;
}

// Honigtopf: Menschen sehen das Feld nicht. Ist es ausgefüllt, war es ein Bot – still verwerfen.
if (feld('kontakt_fax', 200) !== '') {
    seite(200, 'Danke!', 'Deine Anfrage ist angekommen. Ich melde mich in der Regel innerhalb eines Werktags.');
}

$name = einzeilig(feld('name', 100));
$email = einzeilig(feld('email', 200));
$nachricht = feld('nachricht', 5000);
$firma = einzeilig(feld('firma', 120));
$telefon = einzeilig(feld('telefon', 40));
$projektart = einzeilig(feld('projektart', 80));
$bestehendeSeite = einzeilig(feld('bestehende_seite', 200));
$start = einzeilig(feld('start', 40));

const PROJEKTARTEN = [
    '3D-Scroll-Website',
    'Digitale Visitenkarte (Microsite)',
    'Landingpage',
    'Noch keine genaue Vorstellung',
    'One Pager',
    'Portfolio Website',
    'Unternehmens Website',
];
const STARTS = ['Noch offen', 'So bald wie möglich', 'In 1 – 3 Monaten', 'Später'];
if (!in_array($start, STARTS, true)) {
    $start = 'Noch offen';
}

$fehler = [];
if (strlen($name) < 2) {
    $fehler[] = 'Bitte gib deinen Namen an.';
}
if (strlen((string) preg_replace('/\D/', '', $telefon)) < 6) {
    $fehler[] = 'Bitte gib eine Telefonnummer an.';
}
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $fehler[] = 'Bitte gib eine gültige E-Mail-Adresse an.';
}
if (!in_array($projektart, PROJEKTARTEN, true)) {
    $fehler[] = 'Bitte wähle die Art der Website aus.';
}
if (strlen($nachricht) < 10) {
    $fehler[] = 'Bitte beschreibe dein Projekt in ein paar Worten.';
}
if ($fehler !== []) {
    seite(422, 'Fast geschafft', implode(' ', $fehler) . ' Mit der Zurück-Taste des Browsers kommst du zum Formular.');
}

// Einfache Sendesperre je Besucher, damit das Formular nicht als Spam-Schleuder taugt.
$sperre = sys_get_temp_dir() . '/bo-anfrage-' . hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? ''));
if (is_file($sperre) && time() - (int) filemtime($sperre) < SPERRE_SEKUNDEN) {
    seite(429, 'Kurz warten', 'Du hast gerade erst eine Anfrage geschickt. Bitte versuche es in einer halben Minute noch einmal.');
}

$zeit = time();

// Ablage auf dem Server: Ordner Baustelle-Anfrage neben dieser Datei, per .htaccess gegen Aufruf von außen gesperrt.
const ABLAGE = __DIR__ . '/Baustelle-Anfrage';

/** Für Dateinamen: Umlaute umschreiben, alles außer Buchstaben/Ziffern zu Bindestrichen. */
function dateiteil(string $text, int $max = 40): string
{
    $text = strtr($text, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss']);
    $text = trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-');
    return trim(substr($text, 0, $max), '-');
}

/** Ordner anlegen und sperren; nächste laufende Nummer holen (gesperrt gegen gleichzeitige Anfragen). */
function naechste_nummer(): int
{
    if (!is_dir(ABLAGE) && !@mkdir(ABLAGE, 0755, true) && !is_dir(ABLAGE)) {
        throw new RuntimeException('Ordner Baustelle-Anfrage kann nicht angelegt werden');
    }
    if (!is_file(ABLAGE . '/.htaccess')) {
        @file_put_contents(ABLAGE . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\n  Deny from all\n</IfModule>\n");
    }
    $datei = @fopen(ABLAGE . '/.zaehler', 'c+');
    if ($datei === false) {
        throw new RuntimeException('Zähler kann nicht geöffnet werden');
    }
    flock($datei, LOCK_EX);
    $nr = (int) stream_get_contents($datei) + 1;
    ftruncate($datei, 0);
    rewind($datei);
    fwrite($datei, (string) $nr);
    fflush($datei);
    flock($datei, LOCK_UN);
    fclose($datei);
    return $nr;
}

$hinweise = [];
try {
    $laufend = naechste_nummer();
} catch (Throwable $e) {
    $laufend = 0;
    $hinweise[] = 'Ablage: ' . $e->getMessage();
}
$laufendText = $laufend > 0 ? sprintf('%02d', $laufend) : date('dHi', $zeit);
$nummer = 'BO-' . date('Y-m', $zeit) . '-' . $laufendText;

// Dateiname: Name-Unternehmen-Jahr-Monat-Nummer.pdf (Unternehmen entfällt, wenn leer)
$teile = array_filter([dateiteil($name), dateiteil($firma), date('Y', $zeit), date('m', $zeit), $laufendText], 'strlen');
$pdfName = implode('-', $teile) . '.pdf';

$betreff = 'Projektanfrage (' . $projektart . ') von ' . $name;

// Ausführlicher Text – nur als Rückfall in der Mail, falls die PDF nicht erstellt werden kann
$text = "Neue Projektanfrage {$nummer} über blue-olive.online\n\n"
    . "Name:               {$name}\n"
    . 'Unternehmen:        ' . ($firma !== '' ? $firma : '–') . "\n"
    . "E-Mail:             {$email}\n"
    . "Telefon:            {$telefon}\n"
    . "Art der Website:    {$projektart}\n"
    . 'Bestehende Website: ' . ($bestehendeSeite !== '' ? $bestehendeSeite : '–') . "\n"
    . "Gewünschter Start:  {$start}\n\n"
    . "Nachricht:\n{$nachricht}\n";
$text = str_replace(["\r\n", "\r"], "\n", $text);

$pdf = '';
try {
    require_once __DIR__ . '/_anfrage/anfrage-pdf.php';
    $pdf = bo_anfrage_pdf([
        'nummer' => $nummer,
        'zeit' => $zeit,
        'name' => $name,
        'firma' => $firma,
        'email' => $email,
        'telefon' => $telefon,
        'projektart' => $projektart,
        'bestehende_seite' => $bestehendeSeite,
        'start' => $start,
        'nachricht' => str_replace(["\r\n", "\r"], "\n", $nachricht),
    ]);
} catch (Throwable $e) {
    $pdf = '';
    $hinweise[] = 'PDF: ' . $e->getMessage();
}

// PDF auf dem Server ablegen (vorhandene Dateien werden nie überschrieben)
if ($pdf !== '' && $laufend > 0) {
    $ziel = ABLAGE . '/' . $pdfName;
    if (is_file($ziel)) {
        $ziel = ABLAGE . '/' . substr($pdfName, 0, -4) . '-' . date('His', $zeit) . '.pdf';
    }
    if (@file_put_contents($ziel, $pdf, LOCK_EX) === false) {
        $hinweise[] = 'Ablage: PDF konnte nicht gespeichert werden';
    }
}

// Mailtext: nur „Neue Anfrage“ – alles Weitere steht in der PDF
$mailText = $pdf !== '' ? "Neue Anfrage\n" : $text;
if ($hinweise !== []) {
    $mailText .= "\n(Hinweis: " . implode('; ', $hinweise) . ")\n";
}

$kopf = [
    'From: Blue Olive Website <' . ABSENDER . '>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
];
if ($pdf !== '') {
    $grenze = 'bo-' . bin2hex(random_bytes(12));
    $kopf[] = 'Content-Type: multipart/mixed; boundary="' . $grenze . '"';
    $inhalt = "--{$grenze}\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($mailText), 76, "\r\n")
        . "--{$grenze}\r\n"
        . "Content-Type: application/pdf; name=\"{$pdfName}\"\r\n"
        . "Content-Transfer-Encoding: base64\r\n"
        . "Content-Disposition: attachment; filename=\"{$pdfName}\"\r\n\r\n"
        . chunk_split(base64_encode($pdf), 76, "\r\n")
        . "--{$grenze}--\r\n";
} else {
    $kopf[] = 'Content-Type: text/plain; charset=UTF-8';
    $kopf[] = 'Content-Transfer-Encoding: 8bit';
    $inhalt = $mailText;
}

$gesendet = @mail(
    EMPFAENGER,
    '=?UTF-8?B?' . base64_encode($betreff) . '?=',
    $inhalt,
    implode("\r\n", $kopf),
    '-f ' . ABSENDER
);

if (!$gesendet) {
    seite(500, 'Das hat nicht geklappt', 'Die Anfrage konnte nicht gesendet werden. Schreib mir bitte direkt an ' . EMPFAENGER . '.');
}

@touch($sperre);
seite(200, 'Danke!', 'Deine Anfrage ist angekommen. Ich melde mich in der Regel innerhalb eines Werktags.');
