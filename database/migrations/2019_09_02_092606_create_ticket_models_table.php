<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('workspace');
            $table->string('hash');
            $table->string('address');
            $table->string('subject');
            $table->text('text');
            $table->string('name');
            $table->string('email');
            $table->string('confirmation');
            $table->integer('type')->unsigned();
            $table->integer('status')->unsigned(); //1 = open, 2 = waiting, 3 = closed
            $table->integer('prio')->unsigned(); //1 = low, 2 = medium, 3 = high
            $table->integer('group')->unsigned();
            $table->integer('assignee')->unsigned();
            $table->text('notes')->default('');
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
        Schema::dropIfExists('ticket_models');
    }
}
