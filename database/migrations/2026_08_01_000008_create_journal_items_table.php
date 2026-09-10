<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('chart_of_accounts');
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->text('keterangan')->nullable();

            $table->index('coa_id');
        });

        // Constraint: hanya salah satu (debit XOR kredit) yang boleh > 0.
        // Ditegakkan di level DB via raw SQL karena Blueprint belum punya helper CHECK bawaan yang portable.
        DB::statement('ALTER TABLE journal_items ADD CONSTRAINT chk_debit_xor_kredit
            CHECK ((debit > 0 AND kredit = 0) OR (kredit > 0 AND debit = 0))');
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_items');
    }
};
