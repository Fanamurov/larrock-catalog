<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCategoryCatalogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('category_catalog', function(Blueprint $table)
		{
			$table->foreign('catalog_id')->references('id')->on('catalog')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('category_id')->references('id')->on('category')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('category_catalog', function(Blueprint $table)
		{
			$table->dropForeign('category_catalog_catalog_id_foreign');
			$table->dropForeign('category_catalog_category_id_foreign');
		});
	}

}
