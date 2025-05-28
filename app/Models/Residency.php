<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Residency extends Model
{
    protected $table = 'residencies';

    protected $fillable = [
        'company_id',
        'salary',
        'description',
    ];

    /**
     * Residency belongs to a company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
