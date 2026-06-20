<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique()->index();
            $table->enum('type', ['event', 'guest_shift', 'workshop', 'special_night', 'community']);
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->date('event_date')->index();
            $table->time('event_time');
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
