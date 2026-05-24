<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->decimal('starting_price', 12, 2);
            $table->decimal('min_increment', 12, 2)->default(1);
            $table->decimal('current_price', 12, 2)->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('image_path')->nullable();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('ending_soon_notified_at')->nullable();
            $table->timestamps();

            $table->index(['starts_at', 'ends_at', 'closed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
