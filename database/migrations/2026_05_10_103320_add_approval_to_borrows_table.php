<?php
// database/migrations/xxxx_xx_xx_add_approval_to_borrows_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            // Tambah kolom untuk sistem persetujuan
            if (!Schema::hasColumn('borrows', 'quantity')) {
                $table->integer('quantity')->default(1)->after('book_id');
            }
            if (!Schema::hasColumn('borrows', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('borrows', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('borrows', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('borrows', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            if (!Schema::hasColumn('borrows', 'status')) {
                $table->enum('status', ['pending', 'approved', 'borrowed', 'returned', 'overdue', 'lost', 'rejected'])->default('pending')->change();
            } else {
                // Ubah default status menjadi 'pending'
                DB::statement("ALTER TABLE borrows MODIFY COLUMN status ENUM('pending', 'approved', 'borrowed', 'returned', 'overdue', 'lost', 'rejected') DEFAULT 'pending'");
            }
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['quantity', 'approved_by', 'approved_at', 'rejected_at', 'rejection_reason']);
            // Kembalikan enum ke semula
            DB::statement("ALTER TABLE borrows MODIFY COLUMN status ENUM('borrowed', 'returned', 'overdue', 'lost') DEFAULT 'borrowed'");
        });
    }
};