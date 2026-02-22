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
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
