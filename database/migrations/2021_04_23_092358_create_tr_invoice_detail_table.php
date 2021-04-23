<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrInvoiceDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_invoice_detail', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('parent_id')->unsigned();
            $table->integer('item_index');
            $table->bigInteger('product_id')->unsigned();
            $table->integer('weight');
            $table->integer('qty');
            $table->decimal('price', 13, 4);
            $table->decimal('total', 13, 4);
        });

        Schema::table('tr_invoice_detail', function($table) {
            $table->foreign('parent_id')->references('id')->on('tr_invoice');
            $table->foreign('product_id')->references('id')->on('ms_product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_invoice_detail');
    }
}
