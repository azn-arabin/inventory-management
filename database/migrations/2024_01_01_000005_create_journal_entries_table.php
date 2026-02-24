<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // 'sale', 'purchase', 'adjustment', etc.
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->timestamp('entry_date');
            $table->text('description');
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->string('reference_number')->nullable();
            $table->timestamps();

            $table->index(['transaction_type', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
