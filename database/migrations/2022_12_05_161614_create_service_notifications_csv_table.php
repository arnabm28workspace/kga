<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateServiceNotificationsCsvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_notifications_csv', function (Blueprint $table) {
            $table->id();
            $table->string('csv_file_name', 250)->nullable();
            $table->unsignedBigInteger('service_partner_id');
            $table->foreign('service_partner_id')->references('id')->on('service_partners')->comment("");
            $table->string('pincode', 100)->nullable();
            $table->tinyInteger('mail_send')->default(0);
            $table->string('branch', 200)->nullable();
            $table->string('entry_date', 200)->nullable();
            // $table->date('entry_date')->nullable();
            $table->string('bill_no', 250)->nullable();
            $table->string('customer_name', 250)->nullable();
            $table->text('address')->nullable();    
            $table->string('district', 250)->nullable();
            $table->string('mobile_no', 250)->nullable();
            $table->string('phone_no', 250)->nullable();
            $table->string('delivery_date', 200)->nullable();
            // $table->date('delivery_date')->nullable();
            $table->string('brand', 250)->nullable();
            $table->string('class', 250)->nullable();
            $table->string('salesman', 250)->nullable();
            $table->string('salesman_mobile_no', 250)->nullable();
            $table->string('product_value', 250)->nullable();
            $table->string('product_sl_no', 100)->nullable();
            $table->string('product_name', 250)->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('service_notifications_csv');
    }
}
