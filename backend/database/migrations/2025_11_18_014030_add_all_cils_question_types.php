<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adiciona todos os tipos de questão usados nos exames CILS
        DB::statement("ALTER TABLE questions MODIFY COLUMN question_type ENUM(
            'multiple_choice', 
            'fill_in_blank', 
            'true_false', 
            'multiple_selection', 
            'matching', 
            'ordering',
            'fill_in_the_blanks',
            'multiple_choice_cloze',
            'reorder_text'
        ) DEFAULT 'multiple_choice'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN question_type ENUM('multiple_choice', 'fill_in_blank', 'true_false') DEFAULT 'multiple_choice'");
    }
};
