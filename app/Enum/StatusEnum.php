<?php

namespace App\Enum;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
