<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\Vehicle;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status'
    ];

    public function order_items() {
        return $this->hasMany(OrderItem::class)
        ->join('items', 'items.id', 'order_items.item_id');
    }

    public function vehicle() {
        return $this->hasOne(Vehicle::class);
    }
}
