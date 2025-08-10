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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50);
            $table->foreignId('keberangkatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('keberangkatan_class_id')->constrained('class_keberangkatans')->cascadeOnDelete();
            $table->string('nama', 25);
            $table->string('email', 50);
            $table->string('nomor', 15);
            $table->integer('nomor_passenger');
            $table->foreignId('kode_promo_id')->nullable()->constrained('promo_codes')->cascadeOnDelete();
            $table->enum('status_payment', ['pending', 'dibayar', 'gagal'])->default('pending');
            $table->integer('sub_total')->nullable();
            $table->integer('grand_total')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
