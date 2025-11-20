<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'content_italian',
        'content_portuguese',
        'exercises',
        'lesson_type',
        'difficulty',
        'estimated_time',
        'order'
    ];

    protected $casts = [
        'exercises' => 'json',
        'difficulty' => 'integer',
        'estimated_time' => 'integer',
        'order' => 'integer'
    ];
    
    // Accessor to ensure exercises is always an array
    public function getExercisesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return $value ?: [];
    }

    // Relacionamentos
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserLessonProgress::class);
    }

    public function progressForUser($userId)
    {
        return $this->userProgress()->where('user_id', $userId)->first();
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }
}
