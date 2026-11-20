
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
        // Pastikan tabel users sudah tersedia
        if (Schema::hasTable('users')) {

            // Cek apakah kolom role belum tersedia
            if (!Schema::hasColumn('users', 'role')) {

                Schema::table('users', function (Blueprint $table) {
                    $table->enum('role', ['admin', 'nasabah'])
                          ->default('nasabah')
                          ->after('password');
                });

            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pastikan tabel users tersedia
        if (Schema::hasTable('users')) {

            // Cek apakah kolom role tersedia
            if (Schema::hasColumn('users', 'role')) {

                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('role');
                });

            }
        }
    }
};
