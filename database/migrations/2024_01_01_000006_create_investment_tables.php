<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('investment_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration_days');
            $table->decimal('min_amount', 20, 8);
            $table->decimal('max_amount', 20, 8)->nullable();
            $table->decimal('return_rate', 8, 4); // percentage e.g. 5.00 = 5%
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('investment_packages');
            $table->decimal('amount', 20, 8);
            $table->decimal('expected_return', 20, 8);
            $table->decimal('profit', 20, 8);
            $table->string('coin', 10)->default('USD');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('investments');
        Schema::dropIfExists('investment_packages');
    }
};
