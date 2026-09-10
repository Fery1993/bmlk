<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Baris checklist kegiatan/biaya pada invoice (cth. "Biaya Pelindo",
 * "Tiang pancang beton", dll). Setiap baris punya centang aktif/tidak;
 * hanya baris yang dicentang yang dihitung ke dalam DPP.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->boolean('is_checked')->default(true);
            $table->string('keterangan', 255);
            $table->string('catatan', 255)->nullable();
            $table->decimal('harga', 18, 2)->default(0);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
