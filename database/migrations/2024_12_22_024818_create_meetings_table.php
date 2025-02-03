<?php

use App\Enums\MeetingStatusEnum;
use App\Helpers\EnumHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->datetime('scheduled_at');
            $table->enum('status', EnumHelper::toArray(MeetingStatusEnum::class))->default(MeetingStatusEnum::SCHEDULED);
            $table->text('description')->nullable();
            $table->foreignId('host_id')->constrained('users');
            $table->foreignId('guest_id')->constrained('users');
            
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('meetings');
    }

};
