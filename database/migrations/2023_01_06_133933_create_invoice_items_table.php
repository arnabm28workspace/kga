<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices')->comment("");
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->comment("");
            $table->string('product_title', 250)->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->double('price', 10, 2)->nullable()->default(0.00);
            $table->double('total_price', 10, 2)->nullable()->default(0.00);
            $table->string('tax', 100)->nullable();
            $table->string('hsn_code', 100)->nullable();
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
        Schema::dropIfExists('invoice_items');
    }
}
