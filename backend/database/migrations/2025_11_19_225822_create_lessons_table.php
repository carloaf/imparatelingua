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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->longText('content_italian'); // Conteúdo em italiano
            $table->longText('content_portuguese'); // Explicações em português
            $table->longText('exercises')->nullable(); // Exercícios da lição em JSON
            $table->enum('lesson_type', ['theory', 'grammar', 'vocabulary', 'pronunciation', 'exercise'])->default('theory');
            $table->integer('difficulty')->default(1); // 1-5
            $table->integer('estimated_time')->default(30); // minutos
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index(['course_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
