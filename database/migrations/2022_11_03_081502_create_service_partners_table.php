<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateServicePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100);
            $table->unique('phone');
            $table->text('about')->nullable();
            $table->string('photo', 250)->nullable();
            $table->text('address')->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin', 100)->nullable();
            $table->string('aadhaar_no', 100)->nullable();
            $table->string('pan_no', 100)->nullable();
            $table->string('gst_no', 100)->nullable();
            $table->string('license_no', 100)->nullable();
            $table->double('salary', 10, 2)->nullable()->default(0.00);
            $table->double('repair_charge', 10, 2)->nullable()->default(0.00)->comment("incentive per repair as per product");
            $table->double('travelling_allowance', 10, 2)->nullable()->default(0.00);
            $table->tinyInteger('type')->default(1)->comment("1:24*7; 2:inhouse_technician; 3:local_vendors");
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
        Schema::dropIfExists('service_partners');
    }
}
