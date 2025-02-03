<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    public function meeting()
    {
        return $this->hasOne(Meeting::class);
    }

    public static function new (int $meeting_id, int $from_id, int $to_id, float $amount, string $description = null): Transaction
    {
        $transaction = Transaction::updateOrCreate([
                'meeting_id' => $meeting_id,
                'from_id' => $from_id,
            ], [
                'to_id' => $to_id,
                'description' => $description,
                'amount' => $amount,
            ]
        );

        return $transaction;
    }
}
