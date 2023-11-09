<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum React: string
{
    use EnumToArray;

    case LIKE = 'Like';
    case LOVE = 'Love';
    case SAD = 'Sad';
    case CARE = 'Care';
    case ANGRY = 'Angry';
}
