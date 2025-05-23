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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->foreignId('user_id')
                    ->constrained('users', 'user_id')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('title');
            $table->unsignedTinyInteger('type');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
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
        Schema::dropIfExists('events');
    }
};
