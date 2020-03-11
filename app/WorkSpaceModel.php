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

class WorkSpaceModel extends Model
{
    protected $fillable = [
        'name', 'company', 'lang', 'usebgcolor', 'bgcolorcode', 'welcomemsg'
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
}
