<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_jurnal', 40)->unique();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->enum('sumber_tipe', [
                'operational_fee', 'invoice_payment', 'bill_payment', 'closing', 'manual',
            ]);
            $table->unsignedBigInteger('sumber_id')->nullable(); // polymorphic reference, no FK
            $table->boolean('is_closing')->default(false);
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->timestamps();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
