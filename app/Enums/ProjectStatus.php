<?php

namespace App\Enums;

use App\Traits\EnumExtraMethods;

enum ProjectStatus: string
{
    use EnumExtraMethods;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}
