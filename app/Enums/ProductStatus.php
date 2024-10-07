<?php

namespace App\Enums;

enum ProductStatus: String
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Removed = 'removed';
}
