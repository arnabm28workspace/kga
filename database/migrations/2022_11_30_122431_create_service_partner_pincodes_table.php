<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateServicePartnerPincodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_partner_pincodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_partner_id');
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->comment("");
            $table->unsignedBigInteger('pincode_id');
            $table->foreign('pincode_id')->references('id')->on('pincodes')->comment("");
            $table->bigInteger('number')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_partner_pincodes');
    }
}
