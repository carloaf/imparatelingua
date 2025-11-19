<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'category_id',
        'question_text',
        'question_type',
        'difficulty',
        'context',
        'explanation',
        'order',
    ];

    protected $casts = [
        'difficulty' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Exame ao qual esta questão pertence
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Categoria desta questão
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Respostas desta questão
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Respostas ordenadas
     */
    public function answersOrdered()
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    /**
     * Resposta correta
     */
    public function correctAnswer()
    {
        return $this->hasOne(Answer::class)->where('is_correct', true);
    }

    /**
     * Progresso dos usuários nesta questão
     */
    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }
}
