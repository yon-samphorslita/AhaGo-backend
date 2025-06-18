<?php
namespace App\Enums;

enum OrderType: string 
{
    case DINEIN = 'dine-in';
    case DELIVERY = 'delivery';
}