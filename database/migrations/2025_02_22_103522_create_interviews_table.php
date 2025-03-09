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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id('interview_id');
            $table->foreignId('apply_id')
                    ->constrained('applies', 'apply_id')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->date('interview_date');
            $table->text('interviewer_info')->nullable();
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
        Schema::dropIfExists('interviews');
    }
};
