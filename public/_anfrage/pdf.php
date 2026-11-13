<?php
// Blue Olive – kleiner PDF-Erzeuger ohne Fremdbibliothek.
// A4, Schrift Helvetica (in jedem PDF-Programm eingebaut), ein JPEG-Bild (Logo).
declare(strict_types=1);

final class BoPdf
{
    public const B = 595.28; // Seitenbreite in Punkt (A4)
    public const H = 841.89; // Seitenhöhe in Punkt (A4)

    private const W_REGULAR = '278,278,355,556,556,889,667,191,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,278,278,584,584,584,556,1015,667,667,722,722,667,611,778,722,278,500,667,556,833,722,778,667,778,722,667,611,722,667,944,667,667,611,278,278,278,469,556,333,556,556,500,556,556,278,556,556,222,222,500,222,833,556,556,556,556,333,500,278,556,500,722,500,500,500,334,260,334,584,278,556,278,222,556,333,1000,556,556,333,1000,667,333,1000,278,611,278,278,222,222,333,333,350,556,1000,333,1000,500,333,944,333,500,667,278,333,556,556,556,556,260,556,333,737,370,556,584,333,737,333,400,584,333,333,333,556,537,278,333,333,365,556,834,834,834,611,667,667,667,667,667,667,1000,722,667,667,667,667,278,278,278,278,722,722,778,778,778,778,778,584,778,722,722,722,722,667,667,611,556,556,556,556,556,556,889,500,556,556,556,556,278,278,278,278,556,556,556,556,556,556,556,584,611,556,556,556,556,500,556,500';
    private const W_BOLD = '278,333,474,556,556,889,722,238,333,333,389,584,278,333,278,278,556,556,556,556,556,556,556,556,556,556,333,333,584,584,584,611,975,722,722,722,722,667,611,778,722,278,556,722,611,833,722,778,667,778,722,667,611,722,667,944,667,667,611,333,278,333,584,556,333,556,611,556,611,556,333,611,611,278,278,556,278,889,611,611,611,611,389,556,333,611,556,778,556,556,500,389,280,389,584,278,556,278,278,556,500,1000,556,556,333,1000,667,333,1000,278,611,278,278,278,278,500,500,350,556,1000,333,1000,556,333,944,333,500,667,278,333,556,556,556,556,280,556,333,737,370,556,584,333,737,333,400,584,333,333,333,611,556,278,333,333,365,556,834,834,834,611,722,722,722,722,722,722,1000,722,667,667,667,667,278,278,278,278,722,722,778,778,778,778,778,584,778,722,722,722,722,667,667,611,556,556,556,556,556,556,889,556,556,556,556,556,278,278,278,278,611,611,611,611,611,611,611,584,611,611,611,611,611,556,611,556';

    /** @var string[] */
    private array $seiten = [];
    private string $aktuell = '';
    private bool $offen = false;
    /** @var int[][] */
    private array $breiten;
    private ?array $bild = null;
    /** @var callable|null */
    private $fusszeile = null;

    public function __construct()
    {
        $this->breiten = [
            false => array_map('intval', explode(',', self::W_REGULAR)),
            true => array_map('intval', explode(',', self::W_BOLD)),
        ];
    }

    /** Wird beim Ausgeben für jede Seite aufgerufen: fn(BoPdf $pdf, int $seite, int $gesamt) */
    public function setzeFusszeile(callable $fn): void
    {
        $this->fusszeile = $fn;
    }

    public function neueSeite(): void
    {
        if ($this->offen) {
            $this->seiten[] = $this->aktuell;
        }
        $this->aktuell = '';
        $this->offen = true;
    }

    public function ladeJpeg(string $pfad): void
    {
        $info = @getimagesize($pfad);
        $daten = @file_get_contents($pfad);
        if ($info === false || $daten === false || ($info[2] ?? 0) !== IMAGETYPE_JPEG) {
            throw new RuntimeException('Logo fehlt oder ist kein JPEG: ' . $pfad);
        }
        $farben = ($info['channels'] ?? 3) === 1 ? '/DeviceGray' : '/DeviceRGB';
        $this->bild = ['w' => $info[0], 'h' => $info[1], 'cs' => $farben, 'daten' => $daten];
    }

    /** Rechteck füllen; y von oben gemessen. */
    public function flaeche(float $x, float $y, float $w, float $h, array $rgb): void
    {
        $this->aktuell .= $this->farbe($rgb, 'rg') . sprintf("%.2F %.2F %.2F %.2F re f\n", $x, self::H - $y - $h, $w, $h);
    }

    public function linie(float $x1, float $y1, float $x2, float $y2, array $rgb, float $staerke = 0.5): void
    {
        $this->aktuell .= $this->farbe($rgb, 'RG') . sprintf(
            "%.2F w %.2F %.2F m %.2F %.2F l S\n",
            $staerke, $x1, self::H - $y1, $x2, self::H - $y2
        );
    }

    public function bild(float $x, float $y, float $w, float $h): void
    {
        if ($this->bild === null) {
            return;
        }
        $this->aktuell .= sprintf("q %.2F 0 0 %.2F %.2F %.2F cm /Im1 Do Q\n", $w, $h, $x, self::H - $y - $h);
    }

    /**
     * Text auf Grundlinie y (von oben). $ausrichtung: L, R oder C (x ist dann rechter Rand bzw. Mitte).
     * $sperrung: zusätzlicher Zeichenabstand in Punkt.
     */
    public function text(
        float $x, float $y, string $text, float $groesse, bool $fett, array $rgb,
        string $ausrichtung = 'L', float $sperrung = 0.0
    ): void {
        $t = self::kodiere($text);
        if ($t === '') {
            return;
        }
        $w = $this->breiteRoh($t, $groesse, $fett) + $sperrung * strlen($t);
        if ($ausrichtung === 'R') {
            $x -= $w;
        } elseif ($ausrichtung === 'C') {
            $x -= $w / 2;
        }
        $this->aktuell .= sprintf(
            "BT /%s %.2F Tf %.2F Tc %s%.2F %.2F Td (%s) Tj ET\n",
            $fett ? 'F2' : 'F1', $groesse, $sperrung, $this->farbe($rgb, 'rg'), $x, self::H - $y, self::maskiere($t)
        );
    }

    public function breite(string $text, float $groesse, bool $fett): float
    {
        return $this->breiteRoh(self::kodiere($text), $groesse, $fett);
    }

    /** Text auf eine Breite umbrechen; Zeilenumbrüche im Text bleiben erhalten. @return string[] */
    public function umbrechen(string $text, float $groesse, bool $fett, float $maxBreite): array
    {
        $zeilen = [];
        foreach (preg_split('/\R/u', $text) ?: [''] as $absatz) {
            $zeile = '';
            foreach (preg_split('/[ \t]+/u', trim($absatz)) ?: [] as $wort) {
                if ($wort === '') {
                    continue;
                }
                // Überlange Wörter (z. B. lange Links) zeichenweise trennen
                while ($this->breite($wort, $groesse, $fett) > $maxBreite) {
                    $teil = '';
                    foreach (self::zeichen($wort) as $z) {
                        if ($this->breite($teil . $z, $groesse, $fett) > $maxBreite) {
                            break;
                        }
                        $teil .= $z;
                    }
                    if ($teil === '') {
                        break;
                    }
                    if ($zeile !== '') {
                        $zeilen[] = $zeile;
                        $zeile = '';
                    }
                    $zeilen[] = $teil;
                    $wort = substr($wort, strlen($teil));
                }
                $probe = $zeile === '' ? $wort : $zeile . ' ' . $wort;
                if ($zeile !== '' && $this->breite($probe, $groesse, $fett) > $maxBreite) {
                    $zeilen[] = $zeile;
                    $zeile = $wort;
                } else {
                    $zeile = $probe;
                }
            }
            $zeilen[] = $zeile;
        }
        return $zeilen;
    }

    public function ausgabe(string $titel, string $autor): string
    {
        if ($this->offen) {
            $this->seiten[] = $this->aktuell;
            $this->offen = false;
        }
        $gesamt = count($this->seiten);
        if ($this->fusszeile !== null) {
            foreach ($this->seiten as $i => $inhalt) {
                $this->aktuell = '';
                ($this->fusszeile)($this, $i + 1, $gesamt);
                $this->seiten[$i] = $inhalt . $this->aktuell;
            }
        }

        $objekte = [];
        $objekte[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objekte[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objekte[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $objekte[5] = '<< /Title ' . self::utf16($titel) . ' /Author ' . self::utf16($autor)
            . ' /Producer (Blue Olive) /CreationDate (D:' . date('YmdHis') . ') >>';
        $ressourcen = '/Font << /F1 3 0 R /F2 4 0 R >>';
        $n = 6;
        if ($this->bild !== null) {
            $objekte[$n] = ['<< /Type /XObject /Subtype /Image /Width ' . $this->bild['w'] . ' /Height ' . $this->bild['h']
                . ' /ColorSpace ' . $this->bild['cs'] . ' /BitsPerComponent 8 /Filter /DCTDecode', $this->bild['daten']];
            $ressourcen .= ' /XObject << /Im1 ' . $n . ' 0 R >>';
            $n++;
        }
        $kinder = [];
        foreach ($this->seiten as $inhalt) {
            $filter = '';
            if (function_exists('gzcompress')) {
                $inhalt = (string) gzcompress($inhalt, 6);
                $filter = ' /Filter /FlateDecode';
            }
            $objekte[$n] = ['<<' . $filter, $inhalt];
            $objekte[$n + 1] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << %s >> /Contents %d 0 R >>',
                self::B, self::H, $ressourcen, $n
            );
            $kinder[] = ($n + 1) . ' 0 R';
            $n += 2;
        }
        $objekte[2] = '<< /Type /Pages /Kids [' . implode(' ', $kinder) . '] /Count ' . $gesamt . ' >>';
        ksort($objekte);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $versatz = [];
        foreach ($objekte as $nr => $obj) {
            $versatz[$nr] = strlen($pdf);
            if (is_array($obj)) {
                [$kopf, $strom] = $obj;
                $pdf .= $nr . " 0 obj\n" . $kopf . ' /Length ' . strlen($strom) . " >>\nstream\n" . $strom . "\nendstream\nendobj\n";
            } else {
                $pdf .= $nr . " 0 obj\n" . $obj . "\nendobj\n";
            }
        }
        $xref = strlen($pdf);
        $anzahl = max(array_keys($objekte)) + 1;
        $pdf .= "xref\n0 " . $anzahl . "\n0000000000 65535 f \n";
        for ($i = 1; $i < $anzahl; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $versatz[$i] ?? 0);
        }
        $pdf .= "trailer\n<< /Size " . $anzahl . " /Root 1 0 R /Info 5 0 R >>\nstartxref\n" . $xref . "\n%%EOF\n";
        return $pdf;
    }

    private function breiteRoh(string $kodiert, float $groesse, bool $fett): float
    {
        $summe = 0;
        $tabelle = $this->breiten[$fett];
        $laenge = strlen($kodiert);
        for ($i = 0; $i < $laenge; $i++) {
            $c = ord($kodiert[$i]);
            $summe += $c >= 32 ? ($tabelle[$c - 32] ?: 556) : 0;
        }
        return $summe * $groesse / 1000;
    }

    private function farbe(array $rgb, string $op): string
    {
        return sprintf('%.3F %.3F %.3F %s ', $rgb[0] / 255, $rgb[1] / 255, $rgb[2] / 255, $op);
    }

    /** UTF-8 → Windows-1252 (Zeichensatz der eingebauten PDF-Schriften) */
    public static function kodiere(string $text): string
    {
        // Weiche Trennstriche entfernen, Umbrüche und Tabs zu Leerzeichen
        $text = str_replace(["\u{00AD}", "\r", "\n", "\t"], ['', ' ', ' ', ' '], $text);
        if (function_exists('iconv')) {
            $r = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
            if ($r !== false) {
                return $r;
            }
        }
        if (function_exists('mb_convert_encoding')) {
            return (string) mb_convert_encoding($text, 'Windows-1252', 'UTF-8');
        }
        return (string) preg_replace('/[^\x20-\x7E]/', '?', $text);
    }

    private static function maskiere(string $t): string
    {
        return strtr($t, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
    }

    private static function utf16(string $text): string
    {
        $u = function_exists('mb_convert_encoding') ? (string) mb_convert_encoding($text, 'UTF-16BE', 'UTF-8') : $text;
        return '<FEFF' . strtoupper(bin2hex($u)) . '>';
    }

    /** @return string[] */
    private static function zeichen(string $wort): array
    {
        return preg_split('//u', $wort, -1, PREG_SPLIT_NO_EMPTY) ?: [$wort];
    }
}
