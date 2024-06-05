<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 100)->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->comment("customer  id");
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->comment("order placed by whom");
            $table->double('order_amount', 10, 2)->nullable()->default(0.00)->comment("order total amount");
            $table->enum('status', ['pending', 'completed', 'cancelled'])->nullable()->default('pending');
            $table->string('address', 100)->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->text('note')->nullable()->comment("comments on order");
            $table->longText('signature')->nullable()->comment('e signature');
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
        Schema::dropIfExists('sales_orders');
    }
}
