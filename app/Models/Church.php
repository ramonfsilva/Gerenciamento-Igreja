<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Church extends Model
{
    protected $fillable = [
        "name",
        "cnpj",
        "phone",
        "email",
        "pastor_name",
        "address",
        "city",
        "state",
        "active",
    ];

    protected function casts(): array
    {
        return [
            "active" => "boolean",
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where("active", true);
    }
}
