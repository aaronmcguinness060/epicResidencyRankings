<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'residency_id',
        'status',
    ];

    /**
     * Relationship: Offer belongs to a Student.
     * Assumes student_id is the foreign key linking to students.student_id.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship: Offer belongs to a Residency.
     */
    public function residency()
    {
        return $this->belongsTo(Residency::class);
    }
}
