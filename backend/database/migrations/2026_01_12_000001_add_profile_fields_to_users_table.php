<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // kalau kolomnya belum ada, baru tambah
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('member')->after('password');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('is_active');
            }

            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address', 255)->nullable()->after('phone');
            }
        });

        // Backfill: pastikan user lama tidak "mati" tanpa sengaja
        if (Schema::hasColumn('users', 'is_active')) {
            DB::table('users')->whereNull('is_active')->update(['is_active' => 1]);
            // kalau keburu ada yang 0 padahal harus aktif (opsional, pilih salah satu):
            // DB::table('users')->where('is_active', 0)->update(['is_active' => 1]);
        }

        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->whereNull('role')->update(['role' => 'member']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
