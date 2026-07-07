<?php

namespace App\Models;

use Database\Factories\ChurchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Church extends Model
{
    /** @use HasFactory<ChurchFactory> */
    use HasFactory;

    protected $fillable = [
        "name", "cnpj", "phone", "email",
        "pastor_name", "address", "city", "state", "active",
    ];

    protected function casts(): array
    {
        return [
            "active" => "boolean",
        ];
    }

    public function scopeActive($query)
    {
        return $query->where("active", true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
