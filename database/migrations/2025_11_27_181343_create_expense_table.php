<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('expense_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('budget_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();

            $table->string('name', 255);
            $table->bigInteger('amount');
            $table->date('transaction_date');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->foreign('budget_id')->references('budget_id')->on('budgets')->nullOnDelete();
            $table->foreign('category_id')->references('category_id')->on('categories')->nullOnDelete();

            $table->index('user_id');
            $table->index('account_id');
            $table->index('budget_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
