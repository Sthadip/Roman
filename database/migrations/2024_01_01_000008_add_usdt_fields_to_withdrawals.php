<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            // Amount user entered in USDT (always deducted from USDT wallet)
            $table->decimal('usdt_amount', 20, 8)->default(0)->after('amount');
            // Converted coin amount to send (= amount for USDT, converted for BTC/ETH)
            $table->decimal('coin_amount', 20, 8)->default(0)->after('usdt_amount');
            // Exchange rate used at time of request
            $table->decimal('rate_used', 20, 8)->nullable()->after('coin_amount');
        });
    }
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['usdt_amount','coin_amount','rate_used']);
        });
    }
};
