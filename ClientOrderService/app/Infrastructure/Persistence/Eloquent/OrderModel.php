<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'client_id',
        'description',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
    ];
}
