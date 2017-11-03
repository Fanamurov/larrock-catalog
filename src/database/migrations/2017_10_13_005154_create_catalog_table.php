<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCatalogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('catalog', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('title');
			$table->text('short')->nullable();
			$table->text('description')->nullable();
			$table->char('url', 191)->unique();
			$table->char('description_link')->nullable();
			$table->char('what')->nullable();
			$table->float('cost', 10)->nullable();
			$table->float('cost_old', 10)->nullable();
			$table->char('manufacture')->nullable();
			$table->integer('position')->default(0);
			$table->char('articul')->nullable();
			$table->integer('active')->default(1);
			$table->integer('nalichie')->unsigned()->default(99999);
			$table->integer('sales')->unsigned()->default(0);
			$table->integer('label_sale')->nullable();
			$table->integer('label_new')->nullable();
			$table->integer('label_popular')->nullable();
			$table->integer('user_id')->unsigned()->index('catalog_user_id_foreign')->nullable();
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
		Schema::dropIfExists('catalog');
	}

}
