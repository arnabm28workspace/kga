<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStockBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_barcodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->foreign('stock_id')->references('id')->on('stock')->comment("");
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->comment("");
            $table->string('barcode_no', 100)->nullable()->default('');
            $table->longText('code_html')->nullable();
            $table->longText('code_base64_img')->nullable();
            $table->tinyInteger('is_scanned')->default(0);
            $table->tinyInteger('is_bulk_scanned')->default(0);
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
        Schema::dropIfExists('stock_barcodes');
    }
}
