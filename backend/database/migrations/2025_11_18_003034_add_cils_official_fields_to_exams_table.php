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
        Schema::table('exams', function (Blueprint $table) {
            // Indica se é um exame oficial CILS
            $table->boolean('is_official')->default(false)->after('year');
            
            // Sessão do exame (Giugno, Dicembre, etc)
            $table->string('session')->nullable()->after('is_official');
            
            // Identificador único do exame (ex: CILS_B1_DIC_2022)
            $table->string('exam_code')->nullable()->unique()->after('session');
            
            // URL ou referência para o exame original (opcional)
            $table->string('source_url')->nullable()->after('exam_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['is_official', 'session', 'exam_code', 'source_url']);
        });
    }
};
