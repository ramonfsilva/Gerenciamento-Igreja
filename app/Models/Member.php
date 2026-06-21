<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "church_id", "name", "phone", "whatsapp", "email",
        "birth_date", "gender", "marital_status", "role_function",
        "status", "conversion_date", "baptism_date", "notes", "active",
    ];

    protected function casts(): array
    {
        return [
            "birth_date" => "date",
            "conversion_date" => "date",
            "baptism_date" => "date",
            "active" => "boolean",
        ];
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }
}
