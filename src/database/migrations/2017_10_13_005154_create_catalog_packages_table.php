<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCatalogPackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('catalog_packages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('catalog_id')->unsigned()->index('catalog_id_package_foreign');
			$table->text('options');
			$table->float('cost');
		});

        Schema::table('catalog_packages', function(Blueprint $table)
        {
            $table->foreign('catalog_id')->references('id')->on('catalog')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('catalog_packages');
	}
}