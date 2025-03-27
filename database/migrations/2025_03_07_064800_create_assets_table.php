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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_asset', 100);
            $table->string('deskripsi', 100);
            $table->enum('kategori', ['Software', 'Hosting', 'Hardware']);
            $table->integer('harga');
            $table->date('tanggal_pembelian');
            $table->date('tanggal_perpanjangan') ->nullable();
            $table->enum('Tipe', ['Berlangganan', 'Lifetime']);
            $table->enum('Status', ['Aktif', 'Perpanjangan', 'Tidak Aktif']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
