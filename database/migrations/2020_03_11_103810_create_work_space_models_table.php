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

class CreateWorkSpaceModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_space_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->string('company');
            $table->string('lang');
            $table->boolean('usebgcolor');
            $table->string('bgcolorcode');
            $table->string('welcomemsg', 4096);
            $table->string('formtitle');
            $table->string('ticketcreatedmsg', 512);
            $table->boolean('emailconfirm')->default(false);
            $table->boolean('allowattachments')->default(true);
            $table->boolean('inform_admin_new_ticket')->default(true);
            $table->boolean('formactions')->default(false);
            $table->string('extfilter')->default('');
            $table->boolean('paidforapi')->default(false);
            $table->string('apitoken')->default('');
            $table->string('widgettoken')->default('');
            $table->boolean('mailer_useown')->default(false);
            $table->string('mailer_host_smtp')->nullable()->default(null);
            $table->string('mailer_port_smtp')->nullable()->default('587');
            $table->string('mailer_host_imap')->nullable()->default(null);
            $table->string('mailer_port_imap')->nullable()->default('143');
            $table->string('mailer_inbox')->nullable()->default('INBOX');
            $table->string('mailer_username')->nullable()->default(null);
            $table->string('mailer_password')->nullable()->default(null);
            $table->string('mailer_address')->nullable()->default(null);
            $table->string('mailer_fromname')->nullable()->default(null);
            $table->text('mail_blacklist')->nullable()->default(null);
            $table->text('mail_filtertokens')->nullable()->default(null);
            $table->boolean('enable_widget')->default(false);
            $table->string('widget_server')->nullable();
            $table->boolean('deactivated')->default(false);
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
        Schema::dropIfExists('work_space_models');
    }
}
