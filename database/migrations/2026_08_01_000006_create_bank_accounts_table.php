<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('chart_of_accounts');
            $table->string('nama_bank', 100);
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('atas_nama', 150)->nullable();
            $table->decimal('saldo_berjalan', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
