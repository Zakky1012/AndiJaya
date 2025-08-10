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
        Schema::create('kursi_keberangkatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keberangkatan_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->integer('row');
            $table->integer('column');
            $table->enum('tipe_kelas', ['ekonomi', 'premium']);
            $table->boolean('is_available')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kursi_keberangkatans');
    }
};
