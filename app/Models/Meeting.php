<?php

namespace App\Models;

use App\Enums\MeetingStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingFactory> */
    use HasFactory;

    protected $casts = [
        'status' => MeetingStatusEnum::class,
        'scheduled_at' => 'date',
    ];

    public function userHost()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function userGuest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function cancel() : void
    {
        $this->status = MeetingStatusEnum::CANCELED;
        $this->save();
    }
    
}
