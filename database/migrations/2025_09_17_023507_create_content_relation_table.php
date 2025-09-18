<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_relation', function (Blueprint $table) {
            $table->foreignId('from_content_id')->constrained('contents')->cascadeOnDelete();
            $table->foreignId('to_content_id')->constrained('contents')->cascadeOnDelete();
            $table->foreignId('relation_type_id')->constrained('relation_types')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->primary(['from_content_id', 'to_content_id', 'relation_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_relation');
    }
};
