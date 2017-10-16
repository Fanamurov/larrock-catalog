<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOptionParamLinkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('option_param_link', function(Blueprint $table)
		{
			$table->foreign('catalog_id')->references('id')->on('catalog')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('param_id')->references('id')->on('option_param')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('option_param_link', function(Blueprint $table)
		{
			$table->dropForeign('option_param_link_catalog_id_foreign');
			$table->dropForeign('option_param_link_param_id_foreign');
		});
	}

}
