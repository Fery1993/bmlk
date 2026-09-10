<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_fees', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_fee', 40)->unique();
            $table->foreignId('karyawan_id')->constrained('karyawan');
            $table->foreignId('beban_coa_id')->constrained('chart_of_accounts');
            $table->foreignId('bank_account_id')->constrained('bank_accounts');
            $table->foreignId('user_id')->constrained('users');
            $table->date('tanggal');
            $table->decimal('jumlah_fee', 18, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();
            $table->timestamps();

            $table->index('karyawan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_fees');
    }
};
