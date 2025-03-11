<?php

namespace App\Enums;

enum ProjectStatus: string
{
    use ExtraMethods;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}
