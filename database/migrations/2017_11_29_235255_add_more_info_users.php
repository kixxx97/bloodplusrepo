<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreInfoUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //single,married
            $table->string('civil_status')->nullable();

            //religion
            $table->string('religion')->nullable();

            //college whatever
            $table->string('education')->nullable();

            //student, etc...
            $table->string('occupation')->nullable();

            //json {schoolid: companyid: prcid: driverid: sssid: othersid: }
            $table->text('identifications')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('civil_status');
            $table->dropColumn('religion');
            $table->dropColumn('education');
            $table->dropColumn('occupation');
            $table->dropColumn('identifications');     
        });
    }
}
