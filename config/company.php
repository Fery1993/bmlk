<?php

/**
 * Profil perusahaan untuk kop surat & cetakan invoice.
 * Ubah nilai di sini (atau lewat .env) tanpa perlu menyentuh view.
 */
return [
    'nama' => env('COMPANY_NAME', 'PT. BERKAH MURNI LAHAN KITA'),
    'tagline' => env('COMPANY_TAGLINE', 'PERUSAHAAN BONGKAR MUAT'),
    'cabang' => env('COMPANY_BRANCH', 'PUSAT GRESIK'),
    'alamat' => env('COMPANY_ADDRESS', 'JL. HARUN THOHIR NO.09 RT.03 RW.04 KEL. BEDILAN KEC. GRESIK KAB.GRESIK'),
    'email' => env('COMPANY_EMAIL', 'pt.bmlk@yahoo.com'),
    'telp' => env('COMPANY_PHONE', '031-7666932'),
    'hp' => env('COMPANY_MOBILE', '0812-3543-517'),
    'logo' => env('COMPANY_LOGO'), // path di /public, null = pakai logo teks

    'bank' => [
        'nama_bank' => env('COMPANY_BANK_NAME', 'Bank Mandiri'),
        'nomor_rekening' => env('COMPANY_BANK_NUMBER', '141-00-0670086-8'),
        'atas_nama' => env('COMPANY_BANK_HOLDER', 'PT. Berkah Murni Lahan Kita'),
    ],

    'direktur' => [
        'nama' => env('COMPANY_DIRECTOR_NAME', 'Deby Chrismayanti, S.E'),
        'npwp' => env('COMPANY_DIRECTOR_NPWP', '71.221.907.0-612.000'),
        'alamat' => env('COMPANY_DIRECTOR_ADDRESS', 'Jl. Harun Tohir No 9 RT.3 RW. 4 Kelurahan Bedilan Kecamatan Gresik Kabupaten Gresik Jatim.'),
    ],
];
