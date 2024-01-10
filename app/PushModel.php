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
use App\User;

/**
 * Class PushModel
 * 
 * Represents the push interface
 */
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

        if (env('FIREBASE_ENABLE', false)) {
            $user = User::where('id', '=', $userId)->first();
            if (($user) && (isset($user->device_token)) && (is_string($user->device_token)) && (strlen($user->device_token) > 0)) {
                PushModel::sendCloudNotification($title, $message, $user->device_token);
            }
        }
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

    /**
     * Send cloud notification to Google Firebase
     * 
     * @param $title
     * @param $body
     * @param $device_token
     * @return void
     * @throws \Exception
     */
    private static function sendCloudNotification($title, $body, $device_token)
    {
        try {
            $curl = curl_init();

            $headers = [
                'Content-Type: application/json',
                'Authorization: key=' . env('FIREBASE_KEY')
            ];

            $data = [
                'to' => $device_token,
                env('FIREBASE_PROPNAME', 'data') => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => asset('gfx/logo.png')
                ]
            ];

            curl_setopt($curl, CURLOPT_URL, env('FIREBASE_ENDPOINT'));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

            $result = curl_exec($curl);
            $result_data = json_decode($result);
            if ((!isset($result_data->success)) || (!$result_data->success)) {
                throw new \Exception('Failed to deliver Firebase cloud message: ' . print_r($result_data, true));
            }
            
            curl_close($curl);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
