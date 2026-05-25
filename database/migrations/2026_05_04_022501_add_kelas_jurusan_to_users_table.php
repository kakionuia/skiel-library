<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Cek dan tambahkan kolom hanya jika belum ada
        if (!Schema::hasColumn('users', 'kelas')) {
            $table->string('kelas')->nullable()->after('nisn');
        }
        if (!Schema::hasColumn('users', 'jurusan')) {
            $table->string('jurusan')->nullable()->after('kelas');
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'kelas')) {
            $table->dropColumn('kelas');
        }
        if (Schema::hasColumn('users', 'jurusan')) {
            $table->dropColumn('jurusan');
        }
    });
}
};
