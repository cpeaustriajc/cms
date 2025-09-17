<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->foreignId('locale_id')->nullable()->constrained('locales');
            $table->unsignedInteger('sort_order')->default(0);

            $table->string('value_string')->nullable();
            $table->text('value_text')->nullable();
            $table->bigInteger('value_integer')->nullable();
            $table->decimal('value_decimal', 20, 6)->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->timestamp('value_datetime')->nullable();

            $table->timestamps();

            $table->unique(['content_id', 'field_id', 'locale_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_field_values');
    }
};
