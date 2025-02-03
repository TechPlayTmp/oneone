<?php

namespace App\Actions;

use App\Models\Meeting;
use App\Models\Transaction;

class TransferAction
{
   
    public static function run(Meeting $meeting, int $payer_id, float $amount, string $description = null) : Transaction
    {
        $to_id = ($payer_id != $meeting->host_id) ? $meeting->host_id: $meeting->guest_id;

        return Transaction::new(
            $meeting->id,
            $payer_id,
            $to_id,
            $amount,
            $description
        );
    }

}
