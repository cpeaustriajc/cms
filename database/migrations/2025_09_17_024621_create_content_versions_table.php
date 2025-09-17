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
        Schema::create('content_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->json('snapshot');
            $table->timestamp('creatred_at')->useCurrent();

            $table->unique(['content_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_versions');
    }
};
