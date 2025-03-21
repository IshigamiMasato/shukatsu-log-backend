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
        Schema::create('files', function (Blueprint $table) {
            $table->id('file_id');
            $table->foreignId('document_id')
                    ->constrained('documents', 'document_id')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('name');
            $table->string('path');
            $table->dateTime('created_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
