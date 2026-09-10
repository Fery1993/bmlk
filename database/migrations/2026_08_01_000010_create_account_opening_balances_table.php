<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_period_id')->constrained('fiscal_periods');
            $table->foreignId('coa_id')->constrained('chart_of_accounts');
            $table->decimal('saldo_awal_debit', 18, 2)->default(0);
            $table->decimal('saldo_awal_kredit', 18, 2)->default(0);

            $table->unique(['fiscal_period_id', 'coa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_opening_balances');
    }
};
