<?php

namespace App\Models;

use App\Enum\EventEnum;
use App\Enum\StatusEnum;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'event',
        'status',
        'details',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'details' => 'array',
        'event' => EventEnum::class,
        'status' => StatusEnum::class,
    ];
}
