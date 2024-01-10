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
 * Class CaptchaModel
 *
 * Represents captcha object
 */
class CaptchaModel extends Model
{
    /**
     * Query sum of hash
     *
     * @param string $hash The input hash
     * @return string|bool The found sum or false on failure
     */
    public static function querySum($hash)
    {
        $result = CaptchaModel::where('hash', '=', $hash)->first();
        if (!$result)
            return false;

        return $result->sum;
    }

    /**
     * Create sum for hash
     *
     * @param string $hash The input hash
     * @return array An array containing both summands
     */
    public static function createSum($hash)
    {
        $result = [
            rand(0, 10),
            rand(0, 10)
        ];

        $entry = CaptchaModel::where('hash', '=', $hash)->first();
        if (!$entry) {
            $entry = new \App\CaptchaModel;
        }

        $entry->hash = $hash;
        $entry->sum = strval($result[0] + $result[1]);
        $entry->save();

        return $result;
    }
}
