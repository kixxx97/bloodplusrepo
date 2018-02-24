<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDonateRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_forms', function (Blueprint $table) {
            $table->string('id')->primary();


            $table->string('donate_request_id');
            //json array { 0 : { question: answers: remarks: } 1 : ... }
            $table->text('medical_history');
            
            $table->string('remarks');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donate_request_forms');
    }
}
