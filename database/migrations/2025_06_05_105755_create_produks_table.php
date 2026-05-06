<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->string('harga');
            $table->integer('total_stok')->default(0);
            $table->integer('rop')->default(10)->comment('Re Order Point');
            $table->decimal('rata_rata_penjualan_harian', 8, 2)->default(0)->comment('Rata-rata penjualan per hari');
            $table->integer('lead_time')->default(7)->comment('Lead time dalam hari');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
