<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'year',
        'description',
        'is_official',
        'session',
        'exam_code',
        'source_url',
    ];

    protected $casts = [
        'year' => 'integer',
        'is_official' => 'boolean',
    ];

    /**
     * Questões deste exame
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Questões ordenadas
     */
    public function questionsOrdered()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}
