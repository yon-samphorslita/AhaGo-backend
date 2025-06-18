<?php
namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case DELIVERING = 'delivering';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}