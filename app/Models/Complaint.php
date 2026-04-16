<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    /**
     * Casts raw DB values into typed PHP values on read,
     * and serialises them back on write. Equivalent to a SQLAlchemy
     * TypeDecorator or a Pydantic field validator.
     */
    protected function casts(): array
    {
        return [
            'status'     => ComplaintStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
