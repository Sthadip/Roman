<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Admin-controlled market index (price + high/low per coin)
        Schema::create('market_index', function (Blueprint $table) {
            $table->id();
            $table->string('coin', 10)->unique();   // BTC, ETH
            $table->decimal('price', 20, 8);        // current price in USDT
            $table->decimal('high_24h', 20, 8);     // 24h high
            $table->decimal('low_24h', 20, 8);      // 24h low
            $table->decimal('change_pct', 8, 4)->default(0); // % change
            $table->boolean('trading_enabled')->default(true);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });

        // All buy/sell trades
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('coin', 10);             // BTC or ETH
            $table->enum('side', ['buy', 'sell']);
            $table->decimal('coin_amount', 20, 8);  // amount of BTC/ETH
            $table->decimal('usdt_amount', 20, 8);  // USDT value at execution
            $table->decimal('price', 20, 8);        // price per coin at execution
            $table->enum('status', ['pending', 'filled', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('filled_by')->nullable(); // admin who filled it
            $table->foreign('filled_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('filled_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Price history for chart (admin sets price, we record it)
        Schema::create('price_history', function (Blueprint $table) {
            $table->id();
            $table->string('coin', 10);
            $table->decimal('price', 20, 8);
            $table->decimal('high', 20, 8)->nullable();
            $table->decimal('low', 20, 8)->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->index(['coin', 'recorded_at']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_history');
        Schema::dropIfExists('trades');
        Schema::dropIfExists('market_index');
    }
};
