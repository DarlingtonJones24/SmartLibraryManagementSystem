<?php
namespace App\Enum;

enum ReservationStatus: string
{
    case PENDING = 'waiting';
    case APPROVED = 'ready';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
    case COMPLETED = 'fulfilled';
}