<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orders extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'orders_id');
    }
}
