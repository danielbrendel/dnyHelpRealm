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
 * Class HomeFaqModel
 * 
 * Represents the FAQ of the home
 */
class HomeFaqModel extends Model
{
    /**
     * Get all FAQ items
     */
    public static function getAll()
    {
        return HomeFaqModel::all();
    }
}
