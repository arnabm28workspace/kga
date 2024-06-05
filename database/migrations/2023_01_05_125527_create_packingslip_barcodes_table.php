<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePackingslipBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packingslip_barcodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('packingslip_id');
            $table->foreign('packingslip_id')->references('id')->on('packingslips')->comment("");
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->comment("");
            $table->string('barcode_no', 100)->nullable();
            $table->text('code_html')->nullable();
            $table->text('code_base64_img')->nullable();
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
        Schema::dropIfExists('packingslip_barcodes');
    }
}
