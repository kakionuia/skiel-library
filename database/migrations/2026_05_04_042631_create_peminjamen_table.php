<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) { // perbaiki nama tabel jika perlu
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete(); // asumsikan ada tabel books
            $table->dateTime('borrowed_at')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('returned_at')->nullable();
            $table->integer('late_fee')->default(0);
            $table->string('status')->default('borrowed'); // borrowed, returned, late, dll.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};