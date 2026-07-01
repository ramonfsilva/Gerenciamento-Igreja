<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = "financial_categories";

    protected $fillable = ["church_id", "name", "type", "active"];

    protected function casts(): array
    {
        return ["active" => "boolean"];
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }
}
