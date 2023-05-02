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

/**
 * Class FaqModel
 * 
 * Represents the personal workspace FAQ
 */
class FaqModel extends Model
{
    protected $fillable = ['workspace', 'question', 'answer'];
}
