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
        Schema::create('seqencecounters', function (Blueprint $table) {
            $table->id();            
            $table->string('sequence_name');
            $table->integer('start_num');
            $table->integer('end_num'); 
            $table->integer('current_num');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seqencecounters');
    }
};
