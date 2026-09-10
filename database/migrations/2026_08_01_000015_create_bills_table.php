<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bill', 40)->unique();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->date('tanggal');
            $table->date('jatuh_tempo');
            $table->foreignId('beban_coa_id')->constrained('chart_of_accounts');
            $table->foreignId('utang_coa_id')->constrained('chart_of_accounts');
            $table->decimal('jumlah', 18, 2);
            $table->enum('status', ['belum_lunas', 'sebagian', 'lunas', 'batal'])->default('belum_lunas');
            $table->text('keterangan')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
