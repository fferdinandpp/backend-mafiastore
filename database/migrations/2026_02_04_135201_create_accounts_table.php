<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('game', ['efootball', 'fc_mobile']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->bigInteger('price');
            $table->enum('status', ['draft', 'approved', 'sold'])->default('draft');
            $table->foreignId('created_by')
                  ->constrained('admins');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
