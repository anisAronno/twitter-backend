<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum React: string
{
    use EnumToArray;

    case LIKE = '&#128077;';
    case LOVE = '&#128525;';
    case HAHA = '&#128513;';
    case SAD = '&#128546;';
    case CARE = '&#129392;';
    case ANGRY = '&#128545;';

    
}
