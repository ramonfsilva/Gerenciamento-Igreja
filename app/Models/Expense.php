<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "church_id", "category_id", "user_id",
        "date", "amount", "payment_method", "description", "active",
    ];

    protected function casts(): array
    {
        return ["date" => "date", "amount" => "decimal:2", "active" => "boolean"];
    }

    public function church(): BelongsTo { return $this->belongsTo(Church::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
