<?php
// database/migrations/[timestamp]_add_damage_and_payment_details_to_borrows_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('borrows', function (Blueprint $table) {
            // Payment fields (only add if not exists)
            if (!Schema::hasColumn('borrows', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('late_fee');
            }
            if (!Schema::hasColumn('borrows', 'payment_due_date')) {
                $table->timestamp('payment_due_date')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('borrows', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('payment_due_date');
            }
            
            // Damage report fields
            if (!Schema::hasColumn('borrows', 'is_damaged')) {
                $table->boolean('is_damaged')->nullable()->after('payment_date');
            }
            if (!Schema::hasColumn('borrows', 'damage_type')) {
                $table->string('damage_type')->nullable()->after('is_damaged');
            }
            if (!Schema::hasColumn('borrows', 'damage_description')) {
                $table->text('damage_description')->nullable()->after('damage_type');
            }
            if (!Schema::hasColumn('borrows', 'damage_images')) {
                $table->text('damage_images')->nullable()->after('damage_description');
            }
            
            // Additional fine from damage
            if (!Schema::hasColumn('borrows', 'additional_fine')) {
                $table->decimal('additional_fine', 10, 2)->default(0)->after('damage_images');
            }
            if (!Schema::hasColumn('borrows', 'staff_comment')) {
                $table->text('staff_comment')->nullable()->after('additional_fine');
            }
            
            // Damage report status (pending/reviewed)
            if (!Schema::hasColumn('borrows', 'damage_status')) {
                $table->string('damage_status')->default('pending')->after('staff_comment'); // pending, reviewed
            }
        });
    }

    public function down()
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status', 'payment_due_date', 'payment_date',
                'is_damaged', 'damage_type', 'damage_description', 'damage_images',
                'additional_fine', 'staff_comment', 'damage_status'
            ]);
        });
    }
};