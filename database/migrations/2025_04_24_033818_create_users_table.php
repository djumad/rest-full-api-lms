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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("nama" , 100);
            $table->string("nomor_identitas" , 100);
            $table->string("email" , 100);
            $table->string("foto" , 100)->nullable();
            $table->enum("role" , ['siswa' , 'admin' , 'guru'])->default("siswa");
            $table->string("password");
            $table->string("token")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
