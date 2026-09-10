<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Invoice {{ $invoice->nomor_invoice }}</title>
<style>
    @page { size: A4; margin: 14mm 16mm; }
    * { box-sizing: border-box; }
    body {
        font-family: 'Times New Roman', Times, serif;
        color: #111;
        font-size: 13px;
        line-height: 1.45;
        margin: 0;
        padding: 0;
    }
    .sheet { max-width: 800px; margin: 0 auto; padding: 24px; }

    .toolbar {
        max-width: 800px; margin: 0 auto 12px; padding: 0 24px;
        display: flex; justify-content: flex-end; gap: 8px;
    }
    .toolbar button {
        font-family: Arial, sans-serif; font-size: 12px; cursor: pointer;
        border: 1px solid #d1d5db; background: #f3f4f6; color: #374151;
        padding: 6px 14px; border-radius: 8px;
    }
    .toolbar button:hover { background: #e5e7eb; }
    .toolbar a { text-decoration: none; }

    /* Header / kop surat */
    .kop { display: flex; align-items: center; gap: 14px; }
    .kop-logo {
        width: 66px; height: 66px; flex-shrink: 0;
        border: 3px solid #111; display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 22px; font-family: Arial, sans-serif;
    }
    .kop-logo img { max-width: 100%; max-height: 100%; }
    .kop-text { text-align: center; flex: 1; }
    .kop-tagline { font-size: 11px; letter-spacing: 1px; font-weight: bold; margin: 0; }
    .kop-nama { font-size: 22px; font-weight: bold; margin: 2px 0 0; letter-spacing: .3px; }
    .kop-cabang { font-size: 15px; font-weight: bold; margin: 0; }
    .kop-alamat { text-align: center; font-size: 11.5px; margin-top: 6px; }
    .kop-kontak { text-align: center; font-size: 11.5px; }
    hr.kop-line { border: none; border-top: 2px solid #111; margin: 8px 0 14px; }

    .judul { text-align: center; font-size: 15px; font-weight: bold; text-decoration: underline; margin: 4px 0 18px; }

    /* Info blok */
    .info-table { border-collapse: collapse; margin-bottom: 16px; }
    .info-table td { padding: 1.5px 0; vertical-align: top; font-size: 13px; }
    .info-table td.label { width: 90px; }
    .info-table td.colon { width: 14px; }

    /* Tabel kegiatan */
    table.kegiatan { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    table.kegiatan th, table.kegiatan td {
        border: 1px solid #111; padding: 6px 8px; font-size: 12.5px; vertical-align: top;
    }
    table.kegiatan th { text-align: center; font-weight: bold; background: #f2f2f2; }
    table.kegiatan td.no { text-align: center; width: 34px; }
    table.kegiatan td.harga { text-align: right; white-space: nowrap; width: 130px; }
    table.kegiatan td.ket { width: 150px; }
    table.kegiatan tr.total td { font-weight: bold; }

    .terbilang { margin-top: 14px; display: flex; gap: 10px; }
    .terbilang .label { width: 90px; flex-shrink: 0; }

    .bayar-info { margin-top: 14px; }
    .bayar-info .label { width: 130px; display: inline-block; }

    .penutup { margin-top: 14px; }

    .ttd-wrap { margin-top: 26px; }
    .ttd-tempat { margin-bottom: 2px; }
    .ttd-space { height: 90px; position: relative; }
    .materai-box {
        position: absolute; left: 0; bottom: 6px;
        width: 80px; height: 80px; border: 1px dashed #999;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; color: #999; font-family: Arial, sans-serif; text-align: center;
    }
    .ttd-nama { font-weight: bold; text-decoration: underline; }
    .nb-table { margin-top: 14px; border-collapse: collapse; font-size: 12px; }
    .nb-table td { padding: 1px 0; vertical-align: top; }
    .nb-table td.label { width: 70px; }
    .nb-table td.colon { width: 14px; }

    @media print {
        .toolbar { display: none !important; }
        body { font-size: 12.5px; }
    }
</style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">Cetak / Simpan PDF</button>
    <a href="{{ route('transaksi.invoices.show', $invoice) }}"><button type="button">Kembali</button></a>
</div>

<div class="sheet">

    {{-- Kop surat --}}
    <div class="kop">
        <div class="kop-logo">
            @if(config('company.logo'))
                <img src="{{ asset(config('company.logo')) }}" alt="Logo">
            @else
                BMLK
            @endif
        </div>
        <div class="kop-text">
            <p class="kop-tagline">{{ config('company.tagline') }}</p>
            <p class="kop-nama">{{ config('company.nama') }}</p>
            <p class="kop-cabang">{{ config('company.cabang') }}</p>
        </div>
        <div style="width:66px;"></div>
    </div>
    <p class="kop-alamat">{{ config('company.alamat') }}</p>
    <p class="kop-kontak">
        Email : {{ config('company.email') }}. TELP/FAX {{ config('company.telp') }} HP. {{ config('company.hp') }}
    </p>
    <hr class="kop-line">

    <p class="judul">INVOICE</p>

    {{-- Info invoice --}}
    <table class="info-table">
        <tr>
            <td class="label">No</td><td class="colon">:</td>
            <td>{{ $invoice->nomor_invoice }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td><td class="colon">:</td>
            <td>{{ $invoice->tanggal->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Kepada</td><td class="colon">:</td>
            <td><strong>{{ $invoice->customer->nama }}</strong></td>
        </tr>
        <tr>
            <td class="label">Alamat</td><td class="colon">:</td>
            <td>{{ $invoice->customer->alamat ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">NPWP</td><td class="colon">:</td>
            <td><strong>{{ $invoice->customer->npwp ?? '—' }}</strong></td>
        </tr>
    </table>

    {{-- Tabel kegiatan --}}
    @php
        $checkedItems = $invoice->items->where('is_checked', true);
        $no = 1;
    @endphp
    <table class="kegiatan">
        <thead>
            <tr>
                <th style="width:34px;">No</th>
                <th>Kegiatan</th>
                <th style="width:130px;">Harga / ongkos</th>
                <th style="width:150px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($checkedItems as $item)
            <tr>
                <td class="no">{{ $no++ }}.</td>
                <td>{{ $item->keterangan }}</td>
                <td class="harga">Rp&nbsp;{{ number_format($item->harga, 0, ',', '.') }},-</td>
                <td class="ket">{{ $item->catatan }}</td>
            </tr>
            @endforeach

            @if($invoice->jasaBongkarMuat() > 0)
            <tr>
                <td class="no">{{ $no++ }}.</td>
                <td>Jasa Bongkar Muat Rp&nbsp;{{ number_format($invoice->tarif_per_ton, 0, ',', '.') }},- X {{ rtrim(rtrim(number_format($invoice->volume_ton, 3, ',', '.'), '0'), ',') }} Ton</td>
                <td class="harga">Rp&nbsp;{{ number_format($invoice->jasaBongkarMuat(), 0, ',', '.') }},-</td>
                <td class="ket"></td>
            </tr>
            @endif

            <tr class="total">
                <td class="no">{{ $no++ }}.</td>
                <td>Jumlah DPP</td>
                <td class="harga">Rp&nbsp;{{ number_format($invoice->dpp, 0, ',', '.') }},-</td>
                <td class="ket"></td>
            </tr>
            <tr>
                <td class="no">{{ $no++ }}.</td>
                <td>PPN</td>
                <td class="harga">Rp&nbsp;{{ number_format($invoice->ppn, 0, ',', '.') }},-</td>
                <td class="ket"></td>
            </tr>
            <tr>
                <td class="no">{{ $no++ }}.</td>
                <td>Materai</td>
                <td class="harga">Rp&nbsp;{{ number_format($invoice->materai, 0, ',', '.') }},-</td>
                <td class="ket"></td>
            </tr>
            <tr class="total">
                <td class="no">{{ $no++ }}.</td>
                <td>Jumlah yang dibayar</td>
                <td class="harga">Rp&nbsp;{{ number_format($invoice->jumlah, 0, ',', '.') }},-</td>
                <td class="ket"></td>
            </tr>
        </tbody>
    </table>

    {{-- Terbilang --}}
    <div class="terbilang">
        <div class="label">Terbilang</div>
        <div>: <em>#{{ \Illuminate\Support\Str::ucfirst(\App\Support\Terbilang::rupiah($invoice->jumlah)) }}#</em></div>
    </div>

    {{-- Info pembayaran --}}
    @php $rekening = $bankAccounts->first(); @endphp
    <div class="bayar-info">
        <p style="margin-bottom:2px;">Pembayaran dilakukan melalui Tranfer ke Rekening :</p>
        <p style="margin:0;">
            <span class="label">{{ $rekening->nama_bank ?? config('company.bank.nama_bank') }}</span>
            : <strong>Rek {{ $rekening->nomor_rekening ?? config('company.bank.nomor_rekening') }}</strong>
        </p>
        <p style="margin:0 0 0 140px;">
            a.n. <strong>{{ $rekening->atas_nama ?? config('company.bank.atas_nama') }}</strong>
        </p>
    </div>

    <p class="penutup">Demikian Invoice ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih</p>

    {{-- Tanda tangan --}}
    <div class="ttd-wrap">
        <p class="ttd-tempat">Gresik, {{ $invoice->tanggal->locale('id')->translatedFormat('d F Y') }}</p>
        <p style="margin:0;">{{ config('company.nama') }}</p>
        <p style="margin:0;">Direktur</p>

        <div class="ttd-space">
            @if($invoice->pakai_materai)
            <div class="materai-box">materai<br>Rp 10.000</div>
            @endif
        </div>

        <p class="ttd-nama">{{ config('company.direktur.nama') }}</p>
    </div>

    <table class="nb-table">
        <tr><td colspan="3"><strong>NB:</strong></td></tr>
        <tr>
            <td class="label">NPWP</td><td class="colon">:</td>
            <td><strong>{{ config('company.direktur.npwp') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Alamat</td><td class="colon">:</td>
            <td>{{ config('company.direktur.alamat') }}</td>
        </tr>
    </table>

</div>

</body>
</html>
