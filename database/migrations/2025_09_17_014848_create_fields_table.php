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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->constrained('content_types')->cascadeOnDelete();
            $table->string('name');
            $table->string('handle');

            /*
             * for reference, the current constraints for this time of
             * migration is that data_types can only take the following
             * values:
             * - string
             * - text
             * - integer
             * - decimal
             * - boolean
             * - datetime
             * - reference
             * - richtext
             * - asset
             *
             * this will not be enforced at the database level to consider
             * the possibility of future data types being added or removed.
             */
            $table->string('data_type', 32);

            $table->boolean('is_required')->default(false);
            $table->boolean('is_translatable')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['content_type_id', 'handle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
