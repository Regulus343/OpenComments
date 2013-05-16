<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('content_id');
			$table->string('content_type', 64);
			$table->integer('parent_id');
			$table->integer('order_id');

			$table->text('comment');

			$table->boolean('approved');
			$table->dateTime('approved_at');

			$table->boolean('deleted');
			$table->dateTime('deleted_at');

			$table->string('ip_address', 36);

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
		Schema::drop('comments');
	}

}