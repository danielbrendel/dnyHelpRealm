<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TicketModel
 * 
 * Represents tickets
 */
class TicketModel extends Model
{
    protected $fillable = ['workspace', 'hash', 'address', 'name', 'email', 'confirmation', 'subject', 'text', 'group', 'assignee', 'attachments', 'prio', 'status', 'type'];

    /**
     * Query tickets of agent
     * 
     * @param int $ag The assignee agent ID
     * @return mixed
     */
    public static function queryAgentTickets($ag)
    {
        $tickets = TicketModel::where('assignee', '=', $ag)->orderBy('updated_at', 'desc')->get();

        return $tickets;
    }
}
