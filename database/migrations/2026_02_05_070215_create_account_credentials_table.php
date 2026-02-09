<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_credentials', function (Blueprint $table) {
            if (!Schema::hasColumn('account_credentials', 'account_id')) {
                $table->foreignId('account_id')
                    ->after('id')
                    ->constrained('accounts')
                    ->cascadeOnDelete()
                    ->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('account_credentials', function (Blueprint $table) {
            if (Schema::hasColumn('account_credentials', 'account_id')) {
                $table->dropForeign(['account_id']);
                $table->dropColumn('account_id');
            }
        });
    }
};
