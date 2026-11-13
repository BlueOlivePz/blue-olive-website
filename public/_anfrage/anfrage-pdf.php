<?php
// Blue Olive – gestaltet die PDF zur Projektanfrage (Briefkopf, Inhalt, Fußzeile).
declare(strict_types=1);

require_once __DIR__ . '/pdf.php';

/**
 * @param array{nummer:string, zeit:int, name:string, firma:string, email:string, telefon:string,
 *              projektart:string, bestehende_seite:string, start:string, nachricht:string} $d
 */
function bo_anfrage_pdf(array $d): string
{
    // Logofarben
    $navy950 = [1, 4, 41];
    $navy900 = [13, 25, 59];
    $navy700 = [29, 39, 92];
    $steel500 = [60, 85, 115];
    $steel300 = [112, 147, 167];
    $steel100 = [170, 199, 213];
    $silber600 = [96, 96, 96];
    $silber200 = [223, 224, 219];
    $silber100 = [239, 243, 245];
    $tinte = [6, 5, 5];
    $weiss = [255, 255, 255];

    $B = BoPdf::B;
    $H = BoPdf::H;
    $rand = 50.0;
    $innen = $B - 2 * $rand;
    $datum = date('d.m.Y', $d['zeit']);
    $uhrzeit = date('H:i', $d['zeit']);
    $leer = static fn (string $v): string => $v !== '' ? $v : '–';

    $pdf = new BoPdf();
    $pdf->ladeJpeg(__DIR__ . '/logo.jpg');

    // Fußzeile auf jeder Seite
    $pdf->setzeFusszeile(static function (BoPdf $p, int $seite, int $gesamt) use ($B, $H, $rand, $silber200, $silber600, $navy900, $navy700): void {
        $p->linie($rand, $H - 66, $B - $rand, $H - 66, $silber200, 0.8);
        $p->text($rand, $H - 50, 'Blue Olive', 8.5, true, $navy700);
        $p->text($rand + $p->breite('Blue Olive', 8.5, true) + 6, $H - 50,
            'Inhaber Panajotis Zekos · Finsterer Weg 13/1 · 72131 Ofterdingen', 8.5, false, $silber600);
        $p->text($rand, $H - 37, 'info@blue-olive.online · 0173-3917 911 · blue-olive.online', 8.5, false, $silber600);
        $p->text($B - $rand, $H - 50, 'Seite ' . $seite . ' von ' . $gesamt, 8.5, false, $silber600, 'R');
        $p->text($B - $rand, $H - 37, 'Kleinunternehmer gemäß § 19 UStG', 8.5, false, $silber600, 'R');
        $p->flaeche(0, $H - 8, $B, 8, $navy900);
    });

    // Briefkopf groß (Seite 1)
    $kopfGross = static function () use ($pdf, $B, $rand, $navy900, $navy700, $steel300, $steel100, $weiss, $d, $datum, $uhrzeit): float {
        $pdf->flaeche(0, 0, $B, 122, $navy900);
        $pdf->flaeche(0, 122, $B, 5, $navy700);
        $pdf->flaeche(0, 127, $B, 1.5, $steel300);
        $pdf->bild($rand - 8, 20, 82, 82);
        $pdf->text($rand + 86, 60, 'Blue Olive', 26, true, $weiss);
        $pdf->text($rand + 87, 79, 'Webdesign aus Ofterdingen', 10.5, false, $steel100);
        $pdf->text($rand + 87, 94, 'blue-olive.online', 9.5, false, $steel300);
        $pdf->text($B - $rand, 48, 'PROJEKTANFRAGE', 9, true, $steel100, 'R', 1.6);
        $pdf->text($B - $rand, 70, $d['nummer'], 14, true, $weiss, 'R');
        $pdf->text($B - $rand, 88, $datum . ' · ' . $uhrzeit . ' Uhr', 10, false, $steel100, 'R');
        return 172.0;
    };
    // Briefkopf schmal (Folgeseiten)
    $kopfKlein = static function () use ($pdf, $B, $rand, $navy900, $steel300, $steel100, $weiss, $d): float {
        $pdf->flaeche(0, 0, $B, 52, $navy900);
        $pdf->flaeche(0, 52, $B, 1.5, $steel300);
        $pdf->bild($rand - 6, 9, 34, 34);
        $pdf->text($rand + 34, 32, 'Blue Olive', 15, true, $weiss);
        $pdf->text($B - $rand, 32, 'Projektanfrage ' . $d['nummer'] . ' · Fortsetzung', 9.5, false, $steel100, 'R');
        return 90.0;
    };
    $grenze = $H - 92; // Unterkante des Inhalts (Platz für die Fußzeile)

    $pdf->neueSeite();
    $y = $kopfGross();

    // Titel
    $pdf->text($rand, $y, 'Neue Projektanfrage', 22, true, $navy900);
    $y += 20;
    $pdf->text($rand, $y, 'Eingegangen über das Formular auf blue-olive.online am ' . $datum . ' um ' . $uhrzeit . ' Uhr.', 10, false, $silber600);
    $y += 22;

    // Hervorgehobene Karte: Art der Website und Start
    $kartenHoehe = 64.0;
    $pdf->flaeche($rand, $y, $innen, $kartenHoehe, $silber100);
    $pdf->flaeche($rand, $y, 4, $kartenHoehe, $navy700);
    $spalte2 = $rand + $innen * 0.62;
    $pdf->text($rand + 20, $y + 22, 'ART DER WEBSITE', 8, true, $steel500, 'L', 1.2);
    $art = $pdf->umbrechen($d['projektart'], 15, true, $spalte2 - $rand - 36)[0] ?? '';
    $pdf->text($rand + 20, $y + 45, $art, 15, true, $navy900);
    $pdf->linie($spalte2 - 14, $y + 14, $spalte2 - 14, $y + $kartenHoehe - 14, $silber200, 1);
    $pdf->text($spalte2, $y + 22, 'GEWÜNSCHTER START', 8, true, $steel500, 'L', 1.2);
    $pdf->text($spalte2, $y + 45, $d['start'], 12.5, true, $navy900);
    $y += $kartenHoehe + 34;

    // Abschnitt-Überschrift
    $abschnitt = static function (string $titel, float $y) use ($pdf, $B, $rand, $navy700, $steel300, $silber200): float {
        $pdf->flaeche($rand, $y - 9, 3, 11, $steel300);
        $pdf->text($rand + 10, $y, $titel, 12, true, $navy700);
        $pdf->linie($rand, $y + 9, $B - $rand, $y + 9, $silber200, 0.8);
        return $y + 30;
    };

    // Kontakt
    $y = $abschnitt('Kontakt', $y);
    $zeilen = [
        ['Name', $d['name'], true],
        ['Unternehmen', $leer($d['firma']), false],
        ['E-Mail', $d['email'], false],
        ['Telefon', $d['telefon'], false],
        ['Bestehende Website', $leer($d['bestehende_seite']), false],
    ];
    $wertX = $rand + 140;
    foreach ($zeilen as $i => [$label, $wert, $fett]) {
        $pdf->text($rand, $y, $label, 9.5, false, $silber600);
        $wertZeilen = $pdf->umbrechen($wert, 11, $fett, $B - $rand - $wertX);
        foreach ($wertZeilen as $j => $wz) {
            $pdf->text($wertX, $y + $j * 15, $wz, 11, $fett, $tinte);
        }
        $y += 15 * (count($wertZeilen) - 1);
        if ($i < count($zeilen) - 1) {
            $pdf->linie($rand, $y + 9, $B - $rand, $y + 9, $silber200, 0.5);
        }
        $y += 25;
    }
    $y += 14;

    // Nachricht (mit Seitenumbruch bei langen Texten)
    $y = $abschnitt('Nachricht', $y);
    $groesse = 11.0;
    $zeilenhoehe = 16.5;
    $polster = 16.0;
    $nachricht = $pdf->umbrechen($d['nachricht'], $groesse, false, $innen - 2 * $polster - 4);
    while (count($nachricht) > 0) {
        $platz = (int) floor(($grenze - $y - 2 * $polster + $zeilenhoehe - $groesse) / $zeilenhoehe);
        if ($platz < 1) {
            $pdf->neueSeite();
            $y = $kopfKlein();
            continue;
        }
        $stueck = array_splice($nachricht, 0, $platz);
        $kastenHoehe = 2 * $polster + (count($stueck) - 1) * $zeilenhoehe + $groesse;
        $pdf->flaeche($rand, $y - 12, $innen, $kastenHoehe, $silber100);
        $pdf->flaeche($rand, $y - 12, 4, $kastenHoehe, $navy700);
        $ty = $y - 12 + $polster + $groesse * 0.8;
        foreach ($stueck as $z) {
            $pdf->text($rand + $polster + 4, $ty, $z, $groesse, false, $tinte);
            $ty += $zeilenhoehe;
        }
        $y += $kastenHoehe + 12;
        if (count($nachricht) > 0) {
            $pdf->neueSeite();
            $y = $kopfKlein();
        }
    }

    return $pdf->ausgabe('Projektanfrage ' . $d['nummer'] . ' – ' . $d['name'], 'Blue Olive');
}
