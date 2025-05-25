<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $fillable = [
        'student_id',
        'residency_id',
        'position',
    ];

    // A ranking belongs to a student (via student_id)
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    // A ranking belongs to a residency
    public function residency()
    {
        return $this->belongsTo(Residency::class);
    }
}
