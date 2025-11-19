<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'user_lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'time_spent',
        'completion_percentage',
        'exercises_completed',
        'exercises_correct',
        'started_at',
        'completed_at',
        'last_accessed_at'
    ];

    protected $casts = [
        'time_spent' => 'integer',
        'completion_percentage' => 'integer',
        'exercises_completed' => 'integer',
        'exercises_correct' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
