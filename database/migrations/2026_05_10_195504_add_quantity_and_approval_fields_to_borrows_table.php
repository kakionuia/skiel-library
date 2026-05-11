<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            // Tambah kolom quantity jika belum ada
            if (!Schema::hasColumn('borrows', 'quantity')) {
                $table->integer('quantity')->default(1)->after('book_id');
            }
            
            // Ubah enum status untuk menambah 'pending' dan 'approved'
            // Perbaiki juga kolom-kolom approval
            if (!Schema::hasColumn('borrows', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('notes')->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('borrows', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            
            if (!Schema::hasColumn('borrows', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
            
            if (!Schema::hasColumn('borrows', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
        });

        // Ubah enum status untuk menambah 'pending', 'approved', 'rejected'
        // Ini harus dilakukan dengan raw query karena Laravel tidak support mengubah enum langsung
        DB::statement("ALTER TABLE borrows MODIFY COLUMN status ENUM('pending', 'approved', 'borrowed', 'returned', 'overdue', 'lost', 'rejected') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['quantity', 'approved_by', 'approved_at', 'rejected_at', 'rejection_reason']);
        });
        
        // Kembalikan enum ke semula
        DB::statement("ALTER TABLE borrows MODIFY COLUMN status ENUM('borrowed', 'returned', 'overdue', 'lost') DEFAULT 'borrowed'");
    }
};