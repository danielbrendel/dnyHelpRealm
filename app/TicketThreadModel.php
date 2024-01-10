<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketThreadModel
 * 
 * Represents the relationship between tickets and threads
 */
class TicketThreadModel extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'text'];
}
