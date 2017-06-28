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
			$table->text('short');
			$table->text('description');
			$table->char('url')->unique();
			$table->char('what')->default('');
			$table->float('cost', 10)->default(0.00);
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
			$table->integer('user_id')->unsigned()->index('catalog_user_id_foreign');
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
