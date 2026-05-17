<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add drift/automation fields to market_index
        Schema::table('market_index', function (Blueprint $table) {
            $table->decimal('drift_pct', 8, 4)->default(0)->after('change_pct');    // % per interval
            $table->integer('drift_interval')->default(60)->after('drift_pct');      // seconds
            $table->enum('drift_direction', ['up','down','none'])->default('none')->after('drift_interval');
            $table->boolean('drift_enabled')->default(false)->after('drift_direction');
            $table->timestamp('drift_last_run')->nullable()->after('drift_enabled');
        });

        // OHLC candle data (open, high, low, close per interval)
        Schema::create('candles', function (Blueprint $table) {
            $table->id();
            $table->string('coin', 10);
            $table->decimal('open',  20, 8);
            $table->decimal('high',  20, 8);
            $table->decimal('low',   20, 8);
            $table->decimal('close', 20, 8);
            $table->decimal('volume', 20, 8)->default(0);
            $table->integer('interval_minutes')->default(1);  // 1m, 5m, 15m, 60m
            $table->timestamp('candle_time');
            $table->unique(['coin','interval_minutes','candle_time']);
            $table->index(['coin','interval_minutes','candle_time']);
            $table->timestamps();
        });

        // Admin's bulk order book: limit orders that auto-fill when price matches
        Schema::create('order_book', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('coin', 10);
            $table->enum('side', ['buy', 'sell']);
            $table->decimal('trigger_price', 20, 8);   // fill when market reaches this price
            $table->decimal('coin_amount', 20, 8);
            $table->decimal('usdt_amount', 20, 8);
            $table->enum('status', ['open', 'filled', 'cancelled'])->default('open');
            $table->timestamp('filled_at')->nullable();
            $table->unsignedBigInteger('trade_id')->nullable();  // resulting trade
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_book');
        Schema::dropIfExists('candles');
        Schema::table('market_index', function (Blueprint $table) {
            $table->dropColumn(['drift_pct','drift_interval','drift_direction','drift_enabled','drift_last_run']);
        });
    }
};
