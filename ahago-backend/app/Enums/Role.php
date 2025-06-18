<?php
namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case DRIVER = 'driver';
    case RESTAURANT = 'restaurant';
}

