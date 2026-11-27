<?php

namespace App\Services;

class BarcodeService
{
    /**
     * Generate Barcode Code 39 dalam format SVG vektor murni (tanpa dependensi eksternal)
     * Sangat tajam untuk cetak kartu identitas berobat pasien (CR80)
     */
    public static function generateCode39Svg(string $text, int $height = 48, int $barWidth = 2): string
    {
        $code39 = [
            '0' => 'bwbwbwBwb', '1' => 'BwbwbwBwb', '2' => 'bwBwbwBwb', '3' => 'BwBwbwbwb',
            '4' => 'bwbwBwBwb', '5' => 'BwbwBwbwb', '6' => 'bwBwBwbwb', '7' => 'bwbwbwBwB',
            '8' => 'BwbwbwBwB', '9' => 'bwBwbwBwB', 'A' => 'BwbwbwbwB', 'B' => 'bwBwbwbwB',
            'C' => 'BwBwbwbwb', 'D' => 'bwbwBwbwB', 'E' => 'BwbwBwbwB', 'F' => 'bwBwBwbwB',
            'G' => 'bwbwbwBwB', 'H' => 'BwbwbwBwB', 'I' => 'bwBwbwBwB', 'J' => 'bwbwBwBwB',
            'K' => 'Bwbwbwbwb', 'L' => 'bwBwbwbwb', 'M' => 'BwBwbwbwb', 'N' => 'bwbwBwbwb',
            'O' => 'BwbwBwbwb', 'P' => 'bwBwBwbwb', 'Q' => 'bwbwbwBwb', 'R' => 'BwbwbwBwb',
            'S' => 'bwBwbwBwb', 'T' => 'bwbwBwBwb', 'U' => 'BwbwbwbwB', 'V' => 'bwBwbwbwB',
            'W' => 'BwBwbwbwb', 'X' => 'bwbwBwbwB', 'Y' => 'BwbwBwbwB', 'Z' => 'bwBwBwbwB',
            '-' => 'bwbwBwbwB', '.' => 'BwbwBwbwb', ' ' => 'bwBwBwbwb', '$' => 'bwbwbwbwb',
            '/' => 'bwbwbwbwb', '+' => 'bwbwbwbwb', '%' => 'bwbwbwbwb', '*' => 'bwbwBwBwb',
        ];

        $cleanText = '*' . strtoupper(preg_replace('/[^0-9A-Z\-\. \$\/\+\%]/', '', $text)) . '*';
        $narrow = $barWidth;
        $wide = $barWidth * 3;
        $gap = $narrow;

        $totalWidth = 0;
        $bars = [];

        for ($i = 0; $i < strlen($cleanText); $i++) {
            $char = $cleanText[$i];
            if (!isset($code39[$char])) continue;
            $pattern = $code39[$char];

            for ($p = 0; $p < strlen($pattern); $p++) {
                $isBar = ($p % 2 === 0);
                $isWide = ($pattern[$p] === 'B' || $pattern[$p] === 'W');
                $w = $isWide ? $wide : $narrow;

                if ($isBar) {
                    $bars[] = ['x' => $totalWidth, 'w' => $w];
                }
                $totalWidth += $w;
            }
            $totalWidth += $gap;
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $totalWidth . ' ' . $height . '" width="100%" height="' . $height . '">';
        $svg .= '<rect width="100%" height="100%" fill="transparent"/>';
        foreach ($bars as $bar) {
            $svg .= '<rect x="' . $bar['x'] . '" y="0" width="' . $bar['w'] . '" height="' . $height . '" fill="#0f172a"/>';
        }
        $svg .= '</svg>';

        return $svg;
    }
}
