<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketsHaveTypes extends Model
{
    const UNKNOWN_TICKET_TYPE_IDENTIFIER = 'unknown_ticket_type';

    protected $fillable = ['workspace', 'name'];

    /**
     * Get ticket type
     * @param $wsId
     * @param $type
     * @return mixed
     */
    public static function getTicketType($wsId, $type)
    {
        try {
            $dataObj = TicketsHaveTypes::where('workspace', '=', $wsId)->where('id', '=', $type)->first();
            if (!$dataObj) {
                throw new \Exception(self::UNKNOWN_TICKET_TYPE_IDENTIFIER);
            }
        } catch (\Exception $e) {
            $dataObj = new \stdClass();
            $dataObj->id = 0;
            $dataObj->workspace = $wsId;
            $dataObj->name = self::UNKNOWN_TICKET_TYPE_IDENTIFIER;
        }

        return $dataObj;
    }
}
