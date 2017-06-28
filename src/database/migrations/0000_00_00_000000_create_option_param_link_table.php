<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOptionParamLinkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('option_param_link', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('catalog_id')->unsigned()->index('option_param_link_catalog_id_foreign');
			$table->integer('param_id')->unsigned()->index('option_param_link_param_id_foreign');
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
		Schema::dropIfExists('option_param_link');
	}

}
