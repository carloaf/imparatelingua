<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'answer_text',
        'is_correct',
        'justification',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Questão à qual esta resposta pertence
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Progresso dos usuários que selecionaram esta resposta
     */
    public function userProgress()
    {
        return $this->hasMany(UserProgress::class, 'selected_answer_id');
    }
}
