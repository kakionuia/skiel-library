<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('books', function (Blueprint $table) {
    $table->id();
    $table->string('book_id')->unique(); // <--- BARIS INI WAJIB ADA
    $table->string('title');
    $table->string('author');
    $table->string('publisher')->nullable();
    $table->integer('year')->nullable();
    $table->string('isbn')->nullable();
    $table->string('category')->nullable();
    $table->integer('quantity')->default(1);
    $table->integer('available_quantity')->default(1);
    $table->text('description')->nullable();
    $table->string('status')->default('pending');
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps(); // Ini agar tidak error 'updated_at' lagi
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};