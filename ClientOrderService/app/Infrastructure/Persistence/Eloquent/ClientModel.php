<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ClientModel extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'address',
        'phone_number',
        'is_active',
    ];
}
