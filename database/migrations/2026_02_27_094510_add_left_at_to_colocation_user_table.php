<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('colocation_user', 'left_at')) {
            Schema::table('colocation_user', function (Blueprint $table) {
                $table->timestamp('left_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('colocation_user', 'left_at')) {
            Schema::table('colocation_user', function (Blueprint $table) {
                $table->dropColumn('left_at');
            });
        }
    }
};
