<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('market_index', function (Blueprint $table) {
            $table->boolean('live_mode')->default(false)->after('drift_last_run');
            $table->timestamp('live_seeded_at')->nullable()->after('live_mode');
            $table->decimal('live_open_price', 20, 8)->nullable()->after('live_seeded_at');
        });
    }

    public function down(): void
    {
        Schema::table('market_index', function (Blueprint $table) {
            $table->dropColumn(['live_mode','live_seeded_at','live_open_price']);
        });
    }
};
