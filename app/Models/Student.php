<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'score',
        'student_id',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rankings()
    {
        return $this->hasMany(Ranking::class, 'student_id', 'user_id');
    }
}
