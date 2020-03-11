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
use App\User;

class PushModel extends Model
{
    /**
     * Add a notification to the list
     * 
     * @param string $title The title of the notification
     * @param string $message The message content
     * @param int $userId The user ID
     * @return void
     */
    public static function addNotification($title, $message, $userId)
    {
        $entry = new PushModel();
        $entry->title = $title;
        $entry->message = $message;
        $entry->seen = false;
        $entry->user_id = $userId;
        $entry->save();
    }

    /**
     * Get all unseen notifications and mark them as seen
     * 
     * @param int $userId The ID of the user
     * @return mixed Items or null if non exist
     */
    public static function getUnseenNotifications($userId)
    {
        $items = PushModel::where('user_id', '=', $userId)->where('seen', '=', false)->get();
        foreach ($items as $item) {
            $item->seen = true;
            $item->save();
        }

        return $items;
    }
}
