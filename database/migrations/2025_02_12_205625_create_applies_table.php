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
        Schema::create('applies', function (Blueprint $table) {
            $table->id('apply_id');
            $table->foreignId('user_id')
                    ->constrained('users', 'user_id')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreignId('company_id')
                    ->constrained('companies', 'company_id')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->unsignedTinyInteger('status')->default(0)->comment('選考ステータス');
            $table->string('occupation')->comment('職種');
            $table->string('apply_route')->nullable()->comment('応募経路');
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
        Schema::dropIfExists('applies');
    }
};
