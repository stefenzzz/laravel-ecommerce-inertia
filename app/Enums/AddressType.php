<?php

namespace App\Enums;

enum AddressType: String
{
    case Shipping = 'Shipping';
    case Billing = 'Billing';
}
