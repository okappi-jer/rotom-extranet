<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->text('unique_id');
            $table->text('supplier_code');
            $table->text('user_id');
            $table->text('user_name');
            $table->text('BTPLArtikelCode')->nullable();
            $table->string('BTPLTekst')->nullable();
            $table->text('BTPLVerpakkingsCode')->nullable();
            $table->text('BTPLKaliber')->nullable();
            $table->text('BTPLOrderDeliveryDate')->nullable();
            $table->text('BTPLOrderDeliveryAt')->nullable();
            $table->text('BTPLArticleRemark')->nullable();
            $table->text('BTPLOrderReference')->nullable();
            $table->text('BTPLArticleCollie')->nullable();
            $table->text('BTPLArticleWeight')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
