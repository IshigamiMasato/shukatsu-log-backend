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
        Schema::create('companies', function (Blueprint $table) {
            $table->id('company_id');
            $table->foreignId('user_id')
                    ->constrained('users', 'user_id')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('president')->nullable();
            $table->string('address')->nullable();
            $table->date('establish_date')->nullable();
            $table->unsignedInteger('employee_number')->nullable();
            $table->string('listing_class')->nullable();
            $table->text('benefit')->nullable();
            $table->text('memo')->nullable();
            $table->dateTime('created_at')->useCurrent()->nullable();
            $table->dateTime('updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
