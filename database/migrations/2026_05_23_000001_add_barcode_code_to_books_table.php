<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'barcode_code')) {
                $table->string('barcode_code')->nullable()->unique()->after('isbn');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'barcode_code')) {
                $table->dropUnique(['barcode_code']);
                $table->dropColumn('barcode_code');
            }
        });
    }
};
