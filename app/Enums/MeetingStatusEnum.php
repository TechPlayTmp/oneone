<?php

namespace App\Enums;

enum MeetingStatusEnum: string
{
    case SCHEDULED = 'scheduled';
    case FINISHED = 'finished';
    case CANCELED = 'canceled';

    public function getColor(): string
    {
        return match ($this) {
            self::SCHEDULED => 'primary',
            self::FINISHED => 'success',
            self::CANCELED => 'danger',
        };
    }
}