<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add image column to reviews
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('comment');
        });

        // Create review_likes table
        Schema::create('review_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'review_id']);
        });

        // Create review_bookmarks table
        Schema::create('review_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'review_id']);
        });

        // Create review_comments table
        Schema::create('review_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_comments');
        Schema::dropIfExists('review_bookmarks');
        Schema::dropIfExists('review_likes');
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
