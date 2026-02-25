<?php
namespace App\Enum;

/**
 * Reservation status values stored in the database.
 * Note: DB uses 'canceled' (one "l").
 */
enum ReservationStatus: string
{
    case PENDING = 'waiting';
    case APPROVED = 'ready';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
    case COMPLETED = 'fulfilled';
}