<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->index();
            $table->string('full_name', 200);
            $table->string('phone', 20);
            $table->string('email', 200)->index();
            $table->date('reservation_date')->index();
            $table->time('reservation_time');
            $table->unsignedInteger('guest_count');
            $table->enum('seating_area', ['indoor', 'outdoor', 'bar'])->nullable();
            $table->text('note')->nullable();
            $table->text('food_allergy')->nullable();
            $table->boolean('is_birthday')->default(false);
            $table->text('special_request')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending')->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
