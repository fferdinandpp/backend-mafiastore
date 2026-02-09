<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // title -> name
            if (Schema::hasColumn('accounts', 'title') && !Schema::hasColumn('accounts', 'name')) {
                $table->renameColumn('title', 'name');
            }

            if (Schema::hasColumn('accounts', 'game') && !Schema::hasColumn('accounts', 'game_label')) {
                $table->renameColumn('game', 'game_label');
            }

            if (Schema::hasColumn('accounts', 'price') && !Schema::hasColumn('accounts', 'price_original')) {
                $table->renameColumn('price', 'price_original');
            }
        });

        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'code')) {
                $table->string('code', 20)->unique();
            }

            if (!Schema::hasColumn('accounts', 'discount_percent')) {
                $table->unsignedTinyInteger('discount_percent')->default(0);
            }

            if (!Schema::hasColumn('accounts', 'price_final')) {
                $table->unsignedBigInteger('price_final')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('accounts', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
            if (Schema::hasColumn('accounts', 'price_final')) {
                $table->dropColumn('price_final');
            }
        });

        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'name') && !Schema::hasColumn('accounts', 'title')) {
                $table->renameColumn('name', 'title');
            }
            if (Schema::hasColumn('accounts', 'game_label') && !Schema::hasColumn('accounts', 'game')) {
                $table->renameColumn('game_label', 'game');
            }
            if (Schema::hasColumn('accounts', 'price_original') && !Schema::hasColumn('accounts', 'price')) {
                $table->renameColumn('price_original', 'price');
            }
        });
    }
};
