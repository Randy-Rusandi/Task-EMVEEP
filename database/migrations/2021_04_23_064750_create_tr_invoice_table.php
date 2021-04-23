<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_invoice', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('invoice_date');
            $table->text('customer');
            $table->text('shipment');
            $table->bigInteger('sales_id')->unsigned();
            $table->bigInteger('payment_type_id')->unsigned();
            $table->bigInteger('courier_id')->unsigned();
            $table->decimal('sub_total', 13, 4);
            $table->decimal('grand_total', 13, 4);
            $table->decimal('courier_fee', 13, 4);
        });

        Schema::table('tr_invoice', function($table) {
            $table->foreign('sales_id')->references('id')->on('ms_sales');
            $table->foreign('payment_type_id')->references('id')->on('ms_payment_type');
            $table->foreign('courier_id')->references('id')->on('ms_courier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_invoice');
    }
}
