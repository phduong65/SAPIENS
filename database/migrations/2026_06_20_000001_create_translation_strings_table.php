<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translation_strings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50);
            $table->string('key', 200);
            $table->string('locale', 10);
            $table->text('value');
            $table->timestamps();
            $table->unique(['group', 'key', 'locale']);
            $table->index(['group', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_strings');
    }
};
