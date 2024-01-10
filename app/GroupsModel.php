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
 * Class GroupsModel
 * 
 * Represents groups
 */
class GroupsModel extends Model
{
    protected $fillable = ['workspace', 'name', 'description', 'def'];

    /**
     * Get primary group
     * 
     * @return mixed
     */
    public static function getPrimaryGroup($ws)
    {
        $group = GroupsModel::where('def', '=', '1')->where('workspace', '=', $ws)->first();

        return $group;
    }

    /**
     * Get group by ID
     * 
     * @param int $id The group ID
     * @return mixed
     */
    public static function get($id)
    {
        $group = GroupsModel::where('id', '=', $id)->first();
        
        return $group;
    }
}
