<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_positions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('symbol', 50);
            $table->string('series', 10);
            $table->bigInteger('traded_quantity');
            $table->bigInteger('deliverable_quantity')->nullable($value = true);
            $table->double('deliverable_quantity_percentage', 8, 2)->nullable($value = true);
            $table->double('open_price', 8, 2);
            $table->double('high_price', 8, 2);
            $table->double('low_price', 8, 2);
            $table->double('close_price', 8, 2);
            $table->double('last_price', 8, 2);
            $table->double('prev_close_price', 8, 2);
            $table->double('traded_value', 20, 2);
            $table->bigInteger('total_trades');
            $table->string('isin', 50);
            $table->date('traded_at');
            $table->index('symbol');
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
        Schema::dropIfExists('delivery_positions');
    }
}
