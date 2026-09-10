<?php

namespace App\Support;

/**
 * Konversi angka ke terbilang Bahasa Indonesia, dipakai untuk baris
 * "Terbilang: ..." pada cetakan invoice.
 */
class Terbilang
{
    private const SATUAN = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
        'sepuluh', 'sebelas',
    ];

    public static function rupiah(float|int $angka): string
    {
        $bulat = (int) round($angka);
        $kata = self::convert(abs($bulat));

        return trim(($bulat < 0 ? 'minus ' : '') . $kata . ' rupiah');
    }

    public static function convert(int $angka): string
    {
        if ($angka < 12) {
            return self::SATUAN[$angka];
        }

        if ($angka < 20) {
            return trim(self::convert($angka - 10) . ' belas');
        }

        if ($angka < 100) {
            return trim(self::convert(intdiv($angka, 10)) . ' puluh ' . self::convert($angka % 10));
        }

        if ($angka < 200) {
            return trim('seratus ' . self::convert($angka - 100));
        }

        if ($angka < 1000) {
            return trim(self::convert(intdiv($angka, 100)) . ' ratus ' . self::convert($angka % 100));
        }

        if ($angka < 2000) {
            return trim('seribu ' . self::convert($angka - 1000));
        }

        if ($angka < 1_000_000) {
            return trim(self::convert(intdiv($angka, 1000)) . ' ribu ' . self::convert($angka % 1000));
        }

        if ($angka < 1_000_000_000) {
            return trim(self::convert(intdiv($angka, 1_000_000)) . ' juta ' . self::convert($angka % 1_000_000));
        }

        if ($angka < 1_000_000_000_000) {
            return trim(self::convert(intdiv($angka, 1_000_000_000)) . ' milyar ' . self::convert($angka % 1_000_000_000));
        }

        return trim(self::convert(intdiv($angka, 1_000_000_000_000)) . ' triliun ' . self::convert($angka % 1_000_000_000_000));
    }
}
