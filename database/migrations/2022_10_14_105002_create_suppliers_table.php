<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('public_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->tinyInteger('is_inside')->default(1)->comment("1:inside;0:outside"); /* inside india or outside india */
            $table->text('address')->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('pin', 100)->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
