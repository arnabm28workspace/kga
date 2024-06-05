<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 100)->nullable();
            $table->unsignedBigInteger('sales_order_id');
            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->comment("");
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->comment("");
            $table->unsignedBigInteger('packingslip_id');
            $table->foreign('packingslip_id')->references('id')->on('packingslips')->comment("");
            $table->double('total_amount', 10, 2)->nullable()->default(0.00);
            $table->double('paid_amount', 10, 2)->nullable()->default(0.00);
            $table->longText('customer_details')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
