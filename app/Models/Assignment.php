<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'residency_id',
        'student_id',
    ];

    /**
     * Get the residency for this assignment.
     */
    public function residency()
    {
        return $this->belongsTo(Residency::class);
    }

    /**
     * Get the student who was assigned.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the company that made the assignment.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
