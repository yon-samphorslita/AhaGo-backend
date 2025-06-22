<?php
namespace App\Enums;

enum PaymentStatus: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case REFUNDED = 'refunded';
}