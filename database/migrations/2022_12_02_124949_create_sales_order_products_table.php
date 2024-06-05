<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSalesOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_orders_id');
            $table->foreign('sales_orders_id')->references('id')->on('sales_orders')->comment("sales order id");
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->comment("product id");
            $table->integer('quantity')->nullable()->default(0);
            $table->double('product_price', 10, 2)->nullable()->default(0.00)->comment("product current price");
            $table->double('product_total_price', 10, 2)->nullable()->default(0.00)->comment("count product price");
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
        Schema::dropIfExists('sales_order_products');
    }
}
