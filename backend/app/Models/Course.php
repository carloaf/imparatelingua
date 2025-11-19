<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'level',
        'image_url',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Relacionamentos
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function userProgress($userId)
    {
        return $this->lessons()
            ->join('user_lesson_progress', 'lessons.id', '=', 'user_lesson_progress.lesson_id')
            ->where('user_lesson_progress.user_id', $userId);
    }
}
