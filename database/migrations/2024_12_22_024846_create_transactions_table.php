<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('from_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_id')->constrained('users')->onDelete('cascade'); 
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->foreignId('meeting_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
