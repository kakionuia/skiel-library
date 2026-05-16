<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            // Payment tracking columns
            $table->enum('payment_method', ['qris', 'cash', 'none'])->default('none')->after('late_fee');
            $table->enum('payment_status', ['unpaid', 'pending', 'confirmed', 'paid'])->default('unpaid')->after('payment_method');
            $table->datetime('payment_date')->nullable()->after('payment_status');
            
            // Extension tracking
            $table->integer('extension_count')->default(0)->after('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'payment_date', 'extension_count']);
        });
    }
};
