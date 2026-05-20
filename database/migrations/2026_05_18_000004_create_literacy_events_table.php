<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('literacy_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('terms')->nullable();
            $table->string('category', 20)->default('point');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('image_path')->nullable();
            $table->string('status', 20)->default('active');
            $table->foreignId('winner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ended_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'category']);
            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('literacy_event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('literacy_event_id')->constrained('literacy_events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('submission_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_winner')->default(false);
            $table->timestamps();

            $table->unique(['literacy_event_id', 'user_id'], 'event_user_unique');
            $table->index(['literacy_event_id', 'is_winner']);
        });

        if (Schema::hasTable('articles')) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE articles MODIFY COLUMN category ENUM('artikel', 'berita', 'event') DEFAULT 'artikel'");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('literacy_event_participants');
        Schema::dropIfExists('literacy_events');

        if (Schema::hasTable('articles')) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE articles MODIFY COLUMN category ENUM('artikel', 'berita') DEFAULT 'artikel'");
            }
        }
    }
};
