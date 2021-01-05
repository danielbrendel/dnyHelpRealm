<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MailTimeoutModel
 *
 * Manage table for mailservice timeouts
 */
class MailTimeoutModel extends Model
{
    public const MAX_TIMEOUT_COUNT = 5;

    /**
     * Add entry for workspace
     * 
     * @param $workspace
     * @return void
     */
    public static function add($workspace)
    {
        $item = MailTimeoutModel::where('workspace', '=', $workspace)->first();
        if (!$item) {
            $item = new MailTimeoutModel();
            $item->workspace = $workspace;
            $item->count = 0;
        }

        $item->count++;
        $item->save();
    }

    /**
     * Check if count is full and if so deactivate and send e-mail
     * 
     * @param $workspace
     * @return void
     */
    public static function handleIfFull($workspace)
    {
        $item = MailTimeoutModel::where('workspace', '=', $workspace)->first();
        if (!$item) {
            return;
        }

        if ($item->count > self::MAX_TIMEOUT_COUNT) {
            $item->count = 0;
            $item->save();

            $ws = WorkspaceModel::where('id', '=', $workspace)->first();
            if ($ws) {
                $ws->mailer_useown = 0;
                $ws->save();

                $agents = AgentModel::where('workspace', '=', $workspace)->where('superadmin', '=', true)->where('active', '=', true)->get();
                foreach ($agents as $agent) {
                    $html = view('mail.mailservice_timeout', ['name' => $agent->surname . ' ' . $agent->lastname, 'workspace' => $ws->name, 'company' => $ws->company, 'hostname' => $ws->mailer_host_imap, 'count' => self::MAX_TIMEOUT_COUNT])->render();
                    MailerModel::sendMail($agent->email, '[' . env('APP_NAME') . '] ' . __('app.mailservice_timeout_subject'), $html);
                }
            }
        }
    }

    /**
     * Clear counter for workspace
     * 
     * @param $workspace 
     * @return void
     */
    public static function clear($workspace)
    {
        $item = MailTimeoutModel::where('workspace', '=', $workspace)->first();
        if (!$item) {
            return;
        }

        $item->count = 0;
        $item->save();
    }
}
