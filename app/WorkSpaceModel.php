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
use Auth;

class WorkSpaceModel extends Model
{
    protected $fillable = [
        'name', 'slug', 'company', 'lang', 'usebgcolor', 'bgcolorcode', 'welcomemsg', 'formtitle', 'ticketcreatedmsg', 'apitoken'
    ];

    /**
     * Get workspace by name
     *
     * @param string $name
     * @return mixed
     */
    public static function get($name)
    {
        $result = WorkSpaceModel::where('name', '=', $name)->first();
        return $result;
    }

    /**
     * Check if current user is logged in into their belonging workspace
     *
     * @param string $workspace
     * @return bool
     */
    public static function isLoggedIn($workspace)
    {
        if (Auth::guest()) {
            return false;
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->first();
        if ($ws === null) {
            return false;
        }

        $data = User::where('id', '=', auth()->id())->where('workspace', '=', $ws->id)->first();
        if ($data === null) {
            return false;
        }

        return true;
    }

    /**
     * Check if the given sender is blacklisted
     * 
     * @param int $workspace
     * @param string $sender
     * @return bool
     */
    public static function isBlacklisted($workspace, $sender)
    {
        try {
            $mail_blacklist = static::where('id', '=', $workspace)->first()->mail_blacklist;
            
            $blacklist = explode("\r\n", $mail_blacklist);
            foreach ($blacklist as $list_item) {
                if (((strlen($list_item) > 0) && strpos($sender, $list_item) !== false)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
