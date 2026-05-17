<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leverage_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('coin', 10);                      // BTC | ETH
            $table->enum('direction', ['long', 'short']);     // long = buy, short = sell
            $table->decimal('margin', 20, 8);                // USDT put up by user
            $table->integer('leverage');                     // 2x, 5x, 10x, 20x, 50x, 100x
            $table->decimal('position_size', 20, 8);         // margin * leverage (USDT notional)
            $table->decimal('entry_price', 20, 8);           // price when opened
            $table->decimal('liq_price', 20, 8);             // liquidation price
            $table->decimal('close_price', 20, 8)->nullable();
            $table->decimal('pnl', 20, 8)->default(0);       // realised PnL on close
            $table->decimal('pnl_pct', 8, 4)->default(0);    // % return on margin
            $table->enum('status', ['open', 'closed', 'liquidated'])->default('open');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['coin', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leverage_trades');
    }
};
