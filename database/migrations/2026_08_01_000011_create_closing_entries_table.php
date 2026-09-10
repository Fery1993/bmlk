<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('closing_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_period_id')->unique()->constrained('fiscal_periods');
            $table->foreignId('journal_id')->constrained('journals');
            $table->decimal('laba_rugi_bersih', 18, 2);
            $table->foreignId('retained_earnings_coa_id')->constrained('chart_of_accounts');
            $table->timestamp('dibuat_pada')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closing_entries');
    }
};
