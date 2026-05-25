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
        Schema::create('system_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'max_borrow_days', 'fine_per_day', 'max_extensions'
            $table->string('value'); // JSON encoded value
            $table->string('type')->default('string'); // string, integer, decimal, boolean
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default parameters
        \DB::table('system_parameters')->insert([
            [
                'key' => 'max_borrow_days',
                'value' => '14',
                'type' => 'integer',
                'description' => 'Jumlah hari maksimal peminjaman buku',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'fine_per_day',
                'value' => '5000',
                'type' => 'integer',
                'description' => 'Nominal denda per hari keterlambatan (dalam Rupiah)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'max_extensions',
                'value' => '2',
                'type' => 'integer',
                'description' => 'Jumlah maksimal perpanjangan peminjaman',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'extension_days',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Jumlah hari yang dapat diperpanjang per permintaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_parameters');
    }
};
