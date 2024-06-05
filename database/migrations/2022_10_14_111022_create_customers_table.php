<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable()->default('');
            $table->string('email', 100)->nullable()->default('');
            $table->string('phone', 100)->nullable()->default('');
            $table->string('address', 250)->nullable();
            $table->string('pincode', 100)->nullable();
            $table->string('gst_no', 200)->nullable();
            $table->string('pan_no', 200)->nullable();
            $table->string('license_no', 250)->nullable()->comment('trade license number');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('customers');
    }
}
