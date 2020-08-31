<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use App\AgentModel;
use App\AgentsHaveGroups;
use App\GroupsModel;
use App\MailerModel;
use App\PushModel;
use App\TicketModel;
use App\TicketsHaveFiles;
use App\TicketsHaveTypes;
use App\WorkSpaceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Class ApiController
 *
 * Interface to ticket API
 */
class ApiController extends Controller
{
    /**
     * Create a ticket
     * @param $workspace
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function createTicket($workspace)
    {
        $invalidFields = array();

        if (!isset($_POST['apitoken'])) {
            $invalidFields[] = array('name' => 'apitoken', 'value' => null);
        }

        if ((!isset($_POST['subject'])) || (strlen($_POST['subject']) < 5)) {
            $invalidFields[] = array('name' => 'subject', 'value' => (isset($_POST['subject'])) ? $_POST['subject'] : null);
        }

        if ((!isset($_POST['text'])) || (strlen($_POST['text']) > 4096)) {
            $invalidFields[] = array('name' => 'text', 'value' => (isset($_POST['text'])) ? $_POST['text'] : null);
        }

        if (!isset($_POST['name'])) {
            $invalidFields[] = array('name' => 'name', 'value' => null);
        }

        if ((!isset($_POST['email'])) || (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
            $invalidFields[] = array('name' => 'email', 'value' => (isset($_POST['email'])) ? $_POST['email'] : null);
        }

        if (!isset($_POST['type'])) {
            $invalidFields[] = array('name' => 'type', 'value' => null);
        }

        if ((!isset($_POST['prio'])) || (($_POST['prio'] < 1) || ($_POST['prio'] > 3))) {
            $invalidFields[] = array('name' => 'prio', 'value' => (isset($_POST['prio'])) ? $_POST['prio'] : null);
        }

        if (count($invalidFields) > 0) {
            return response()->json(array('code' => 500, 'workspace' => $workspace, 'invalid_fields' => $invalidFields));
        }

        $ws = WorkSpaceModel::where('name', '=', $workspace)->where('deactivated', '=', false)->first();
        if ($ws === null) {
            return response()->json(array('code' => 404, 'workspace' => $workspace));
        }

        if (!$ws->paidforapi) {
            return response()->json(array('code' => 403, 'workspace' => $workspace, 'paidforapi' => false));
        }

        putenv('TEMP_WORKSPACE=' . $ws->id);

        if ($ws->apitoken !== $_POST['apitoken']) {
            return response()->json(array('code' => 403, 'workspace' => $workspace, 'apitoken' => $_POST['apitoken']));
        }

        $hasType = TicketsHaveTypes::where('workspace', '=', $ws->id)->where('id', '=', $_POST['type'])->first();
        if ($hasType === null) {
            return response()->json(array('code' => 404, 'workspace' => $workspace, 'ticket_type' => $_POST['type']));
        }

        $attr = [
            'subject' => $_POST['subject'],
            'text' => $_POST['text'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'type' => $_POST['type'],
            'prio' => $_POST['prio'],
        ];

        $attr['workspace'] = $ws->id;

        $attr['assignee'] = 0;
        $attr['group'] = GroupsModel::getPrimaryGroup($ws->id)->id;

        $attr['hash'] = md5($attr['name'] . $attr['email'] . date('Y-m-d h:i:s') . random_bytes(55));
        $attr['address'] = $_SERVER['REMOTE_ADDR'];

        if ($ws->emailconfirm) {
            $attr['confirmation'] = md5($attr['hash'] . random_bytes(55));
            $attr['status'] = 0;
        } else {
            $attr['confirmation'] = '_confirmed';
            $attr['status'] = 1;
        }

        $ticketOfAddress = TicketModel::where('address', '=', $attr['address'])->orderBy('created_at', 'desc')->first();
        if ($ticketOfAddress !== null) {
            $tmNow = Carbon::now();
            $tmLast = Carbon::createFromFormat('Y-m-d H:i:s', $ticketOfAddress->created_at);
            $diff = $tmLast->diffInSeconds($tmNow);
            if ($diff < env('APP_TICKET_CREATION_WAITTIME')) {
                return response()->json(array('code' => 429, 'workspace' => $workspace, 'ticket_wait_time' => $diff));
            }
        }

        $data = TicketModel::create($attr);
        if ($data) {
            $att = request()->file('attachment');
            if ($att != null && $ws->allowattachments) {
                $fname = $att->getClientOriginalName() . '_' . uniqid('', true) . '_' . md5(random_bytes(55));
                $fext = $att->getClientOriginalExtension();

                if (strlen($ws->extfilter) > 0) {
                    foreach (explode(' ', $ws->extfilter) as $fileext) {
                        $fileext = str_replace('.', '', trim($fileext));
                        if ($fext === $fileext) {
                            return response()->json(array('code' => 500, 'workspace' => $workspace, 'invalid_fields' => array('name' => 'attachment', 'value' => $fext)));
                        }
                    }
                }

                $att->move(public_path() . '/uploads', $fname . '.' . $fext);

                $dbstor = new TicketsHaveFiles();
                $dbstor->ticket_hash = $attr['hash'];
                $dbstor->file = $fname . '.' . $fext;
                $dbstor->save();
            }

            if ($ws->emailconfirm) {
                $htmlCode = view('mail.ticket_create_confirm', ['workspace' => $ws->name, 'name' => $attr['name'], 'hash' => $data->hash, 'subject' => $data->subject, 'text' => $data->text, 'confirmation' => $attr['confirmation']])->render();
            } else {
                $htmlCode = view('mail.ticket_create_notconfirm', ['workspace' => $ws->name, 'name' => $attr['name'], 'subject' => $data->subject, 'text' => $data->text, 'hash' => $data->hash])->render();
            }

            MailerModel::sendMail($attr['email'], '[ID:' . $data->hash .  '][' . $ws->company . '] ' . __('app.mail_ticket_creation'), $htmlCode);

            $agentInGroupIds = array();
            $agentsInGroup = AgentsHaveGroups::where('group_id', '=', $attr['group'])->get();
            foreach ($agentsInGroup as $entry) {
                $agentOfGroup = AgentModel::where('id', '=', $entry->agent_id)->where('workspace', '=', $ws->id)->where('mailonticketingroup', '=', true)->first();
                if ($agentOfGroup !== null) {
                    $htmlCode = view('mail.ticket_in_group', ['workspace' => $ws->name, 'name' => $agentOfGroup->surname . ' ' . $agentOfGroup->lastname, 'ticketid' => $data->id, 'subject' => $data->subject, 'text' => $data->text])->render();
                    MailerModel::sendMail($agentOfGroup->email, '[' . $ws->company . '] ' . __('app.mail_ticket_in_group'), $htmlCode);

                    PushModel::addNotification(__('app.mail_ticket_in_group'), $data->subject, $agentOfGroup->user_id);

                    $agentInGroupIds[] = $agentOfGroup->id;
                }
            }

            if ($ws->inform_admin_new_ticket) {
                $admins = AgentModel::where('workspace', '=', $ws->id)->where('superadmin', '=', true)->get();
                foreach ($admins as $adminUser) {
                    if (!in_array($adminUser->id, $agentInGroupIds)) {
                        $htmlCode = view('mail.new_ticket_admin', ['workspace' => $ws->name, 'name' => $adminUser->surname . ' ' . $adminUser->lastname, 'ticketid' => $data->id, 'subject' => $data->subject, 'text' => $data->text])->render();
                        MailerModel::sendMail($adminUser->email, '[' . $ws->company . '] ' . __('app.mail_ticket_in_group'), $htmlCode);
                    }
                }
            }

            return response()->json(array('code' => 201, 'workspace' => $workspace, 'data' => $attr));
        } else {
            return response()->json(array('code' => 500, 'workspace' => $workspace, 'data' => $attr));
        }
    }
}