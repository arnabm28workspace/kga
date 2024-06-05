<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cat_id');
            $table->foreign('cat_id')->references('id')->on('categories')->comment("category");
            $table->unsignedBigInteger('subcat_id');
            $table->foreign('subcat_id')->references('id')->on('categories')->comment("subcategory");
            $table->string('title', 250)->nullable()->default('');
            $table->text('description')->nullable();
            $table->string('public_name', 250)->nullable()->default('');
            $table->tinyInteger('is_title_public_name_same')->default(0);
            $table->string('image', 200)->nullable();
            $table->string('unique_id', 100)->nullable()->default('');
            $table->integer('set_of_pcs')->nullable()->default(0);
            $table->string('warrant_status', 100)->nullable();
            $table->string('service_level', 100)->nullable()->default('customer_level')->comment('customer_level or dealer_level');
            $table->tinyInteger('is_installable')->default(0)->comment('0 1');
            $table->tinyInteger('is_amc_applicable')->default(0)->comment('0 1');
            $table->enum('type', ['fg', 'sp'])->default('fg')->comment('fg: finished goods; sp: spare parts');
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
        Schema::dropIfExists('products');
    }
}
