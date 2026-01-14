<?php

function getBadgeClass($status)
{
    return match ($status) {
        'LUNAS', 'CONFIRMED' => 'success',
        'WAITING_APPROVAL', 'PENDING' => 'warning',
        'CANCELLED', 'DIBATALKAN' => 'danger',
        default => 'info'
    };
}
