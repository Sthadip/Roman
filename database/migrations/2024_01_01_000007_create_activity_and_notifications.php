<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // User activity log
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');        // e.g. 'deposit.form', 'withdraw.form'
            $table->string('page');          // human label e.g. 'Deposit Page'
            $table->string('url')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->timestamps();
        });

        // Admin/SuperAdmin notifications
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');          // 'new_user', 'user_login'
            $table->string('title');
            $table->text('body');
            $table->unsignedBigInteger('ref_user_id')->nullable(); // the user this is about
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('user_activities');
    }
};
