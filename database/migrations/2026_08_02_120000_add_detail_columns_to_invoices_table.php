<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom detail pekerjaan bongkar muat & rincian pajak/materai
 * ke tabel invoices. Kolom-kolom ini dipakai oleh InvoiceController untuk
 * menyimpan hasil hitungTotal() (DPP, PPN 11%, materai) serta detail
 * volume & tarif jasa bongkar muat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('volume_ton', 12, 3)->nullable()->after('piutang_coa_id');
            $table->decimal('tarif_per_ton', 18, 2)->nullable()->after('volume_ton');
            $table->decimal('dpp', 18, 2)->default(0)->after('tarif_per_ton');
            $table->decimal('ppn', 18, 2)->default(0)->after('dpp');
            $table->decimal('materai', 18, 2)->default(0)->after('ppn');
            $table->boolean('pakai_materai')->default(true)->after('materai');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'volume_ton', 'tarif_per_ton', 'dpp', 'ppn', 'materai', 'pakai_materai',
            ]);
        });
    }
};
