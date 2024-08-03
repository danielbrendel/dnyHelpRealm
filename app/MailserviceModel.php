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

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\TicketThreadModel;
use App\TicketModel;
use App\MailerModel;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Illuminate\Support\Carbon;
use App\MailTimeoutModel;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

/**
 * Class MailserviceModel
 *
 * Perform ticket emailing operations
 */
class MailserviceModel extends Model
{
    private $clientMgr = null;
    private $client = null;

    /**
     * Construct and connect
     *
     * @return void
     */
    public function __construct()
    {
        $this->clientMgr = new ClientManager(__DIR__ . '/../config/imapconfig.php');
        $this->client = $this->clientMgr->account('default');
        $this->client->connect();
    }

    /**
     * Convert upload_max_filesize to byte value
     */
    public function iniFileSize()
    {
        $value = ini_get('upload_max_filesize');

        if (is_numeric($value)) {
            return $value;
        }

        $lastChar = strtolower(substr($value, -1));
        $actValue = intval(substr($value, 0, strlen($value)-1));

        if ($lastChar === 'k') {
            return $actValue * 1024;
        } else if ($lastChar === 'm') {
            return $actValue * 1024 * 1024;
        } else if ($lastChar === 'g') {
            return $actValue * 1024 * 1024 * 1024;
        }

        return $actValue;
    }

    /**
     * Process inbox. Create thread from message and then delete the message
     *
     * @return array The result of processed items
     */
    public function processInbox()
    {
        $resultArray = array();
        if ($this->client !== null) {
            $folders = $this->client->getFolders();
            foreach ($folders as $folder) {
                if ($folder->name == env('MAILSERV_INBOXNAME')) {
                    $mailmessages = $folder->messages()->all()->get();

                    foreach($mailmessages as $message){
                        if ((WorkSpaceModel::isBlacklisted($_ENV['TEMP_WORKSPACE'], $message->getAttributes()['from'][0]->mail)) 
                            || (WorkSpaceModel::hasBlacklistedTokens($_ENV['TEMP_WORKSPACE'], $message->getSubject()))
                            || (WorkSpaceModel::hasBlacklistedTokens($_ENV['TEMP_WORKSPACE'], $message->getTextBody()))) {
                            $message->delete();

                            continue;
                        }

                        $subject = $message->getSubject();
                        $idPos = strpos($subject, '[ID:');
                        if ($idPos !== false) {
                            $ticketHash = '';
                            for ($i = $idPos + 4; $i < strlen($subject); $i++) {
                                if (substr($subject, $i, 1) === ']') {
                                    break;
                                }

                                $ticketHash .= substr($subject, $i, 1);
                            }
                            
                            $ticket = TicketModel::where('hash', '=', $ticketHash)->where('status', '<>', 3)->first();
                            if ($ticket !== null) {
                                $resultArrItem = array();
                                $resultArrItem['ticket'] = $ticket->id;

                                $sender = $message->getFrom()[0]->mail;
                                $ws = WorkSpaceModel::where('id', '=', $ticket->workspace)->first();
                                $isAgent = AgentModel::where('email', '=', $sender)->where('workspace', '=', $ws->id)->first();

                                $resultArrItem['workspace'] = $ws->id;
                                $resultArrItem['sender'] = $sender;
                                $resultArrItem['subject'] = $subject;

                                if (($isAgent === null) && ($ticket->confirmation !== '_confirmed')) {
                                    $ticket->confirmation = '_confirmed';
                                    $ticket->status = 1;
                                    $ticket->save();
                                    $message->delete();
                                    $resultArrItem['_confirm'] = true;
                                    if ($ws !== null) {
                                        $htmlCode = view('mail.ticket_confirmed_email')->render();
                                        MailerModel::sendMail($ticket->email, '[' . $ws->company . '] ' . substr(__('app.ticket_customer_confirm_success') . ' [ID:' . $ticket->hash .  ']', 0, 15), $htmlCode);
                                    }
                                    $resultArray[] = $resultArrItem;
                                    continue;
                                }

                                if ($isAgent !== null) {
                                    $ticket->status = 2;
                                    $ticket->save();
                                } else {
                                    $ticket->status = 1;
                                    $ticket->save();
                                }

                                $thread = new TicketThreadModel;
                                $thread->user_id = ($isAgent !== null) ? $isAgent->user_id : 0;
                                $thread->ticket_id = $ticket->id;
                                $thread->text = $message->getTextBody();
                                $thread->save();

                                $resultArrItem['message'] = $thread->text;
                                $resultArrItem['user_id'] = $thread->user_id;
                                $resultArrItem['attachments'] = array();

                                $bContinue = true;

                                $attachments = $message->getAttachments();
                                if (count($attachments) > env('APP_ATTACHMENTS_MAX')) {
                                    $resultArrItem['error'] = 'Attachment count';
                                    $resultArrItem['data'] = count($attachments);
                                    $resultArray[] = $resultArrItem;
                                    $bContinue = false;
                                    $message->delete();
                                }

                                if ($bContinue) {
                                    $attachments = $message->getAttachments();
                                    foreach ($attachments as $file) {
                                        if ($file->getSize() <= $this->iniFileSize()) {
                                            $bIgnoreFile = false;

                                            if (strlen($ws->extfilter) > 0) {
                                                foreach (explode(' ', $ws->extfilter) as $fileext) {
                                                    $fileext = str_replace('.', '', trim($fileext));
                                                    if ($file->getExtension() === $fileext) {
                                                        $bIgnoreFile = true;
                                                        break;
                                                    }
                                                }
                                            }

                                            if ($bIgnoreFile) {
                                                continue;
                                            }

                                            $newName = $file->getName() . md5(random_bytes(55)) . '.' . $file->getExtension();
                                            $file->save(public_path() . '/uploads', $newName);

                                            $ticketFile = new TicketsHaveFiles();
                                            $ticketFile->ticket_hash = $ticket->hash;
                                            $ticketFile->file = $newName;
                                            $ticketFile->save();

                                            $resultArrItem['attachments'][] = $newName;
                                        }
                                    }

                                    $message->delete();

                                    $resultArray[] = $resultArrItem;

                                    if ($ws !== null) {
                                        if ($isAgent !== null) {
                                            $htmlCode = view('mail.ticket_reply_agent', ['workspace' => $ws->name, 'name' => $ticket->name, 'hash' => $ticket->hash, 'agent' => $isAgent->surname . ' ' . $isAgent->lastname, 'message' => $message->getTextBody()])->render();
                                            MailerModel::sendMail($ticket->email, '[' . $ws->company . '] ' . __('app.mail_ticket_agent_replied') . ' [ID:' . $ticket->hash .  ']', $htmlCode);
                                        } else {
                                            $assignee = AgentModel::where('id', '=', $ticket->assignee)->first();
                                            if ($assignee !== null) {
                                                $htmlCode = view('mail.ticket_reply_customer', ['workspace' => $ws->name, 'name' => $assignee->surname . ' ' . $assignee->lastname, 'id' => $ticket->id, 'customer' => $ticket->name, 'message' => $message->getTextBody()])->render();
                                                MailerModel::sendMail($assignee->email, '[' . $ws->company . '] ' . __('app.mail_ticket_customer_replied') . ' [ID:' . $ticketHash . ']', $htmlCode);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $ws = WorkSpaceModel::where('id', '=', $_ENV['TEMP_WORKSPACE'])->first();
                            if ($ws !== null) {
                                $attr['subject'] = $message->getSubject();
                                $attr['text'] = $message->getTextBody();
                                $sender = $message->getSender();
                                $attr['email'] = $message->getAttributes()['from'][0]->mail;
                                $attr['name'] = $message->getAttributes()['from'][0]->personal;
                                $attr['workspace'] = $ws->id;
                                $attr['assignee'] = 0;
                                $attr['group'] = GroupsModel::getPrimaryGroup($ws->id)->id;
                                $attr['hash'] = md5($attr['name'] . $attr['email'] . date('Y-m-d h:i:s') . random_bytes(55));
                                $attr['address'] = md5($_SERVER['REMOTE_ADDR']);
                                $attr['type'] = TicketsHaveTypes::where('workspace', '=', $ws->id)->first()->id;
                                $attr['prio'] = 1;
                                $attr['status'] = 1;

                                $resultArrItem['workspace'] = $ws->id;
                                $resultArrItem['sender'] = $attr['email'];
                                $resultArrItem['subject'] = $attr['subject'];
                                $resultArrItem['message'] = $attr['text'];
                                $resultArrItem['user_id'] = 0;
                                $resultArrItem['attachments'] = array();

                                if ($ws->emailconfirm) {
                                    $attr['confirmation'] = md5($attr['hash'] . random_bytes(55));
                                    $attr['status'] = 0;
                                } else {
                                    $attr['confirmation'] = '_confirmed';
                                    $attr['status'] = 1;
                                }

                                $bContinue = true;

                                $attachments = $message->getAttachments();
                                if (count($attachments) > env('APP_ATTACHMENTS_MAX')) {
                                    $resultArrItem['error'] = 'Attachment count';
                                    $resultArrItem['data'] = count($attachments);
                                    $resultArray[] = $resultArrItem;
                                    $bContinue = false;
                                    $message->delete();
                                }

                                if ($bContinue) {
                                    foreach ($attachments as $file) {
                                        if ($file->getSize() <= $this->iniFileSize()) {
                                            $bIgnoreFile = false;

                                            if (strlen($ws->extfilter) > 0) {
                                                foreach (explode(' ', $ws->extfilter) as $fileext) {
                                                    $fileext = str_replace('.', '', trim($fileext));
                                                    if ($file->getExtension() === $fileext) {
                                                        $bIgnoreFile = true;
                                                        break;
                                                    }
                                                }
                                            }

                                            if ($bIgnoreFile) {
                                                continue;
                                            }

                                            $newName = $file->getName() . md5(random_bytes(55)) . '.' . $file->getExtension();
                                            $file->save(public_path() . '/uploads', $newName);

                                            $ticketFile = new TicketsHaveFiles();
                                            $ticketFile->ticket_hash = $attr['hash'];
                                            $ticketFile->file = $newName;
                                            $ticketFile->save();

                                            $resultArrItem['attachments'][] = $newName;
                                        }
                                    }
                                }

                                $ticketOfAddress = TicketModel::where('address', '=', $attr['address'])->orderBy('created_at', 'desc')->first();
                                if ($ticketOfAddress !== null) {
                                    $tmNow = Carbon::now();
                                    $tmLast = Carbon::createFromFormat('Y-m-d H:i:s', $ticketOfAddress->created_at);
                                    $diff = $tmLast->diffInSeconds($tmNow);
                                    if ($diff < env('APP_TICKET_CREATION_WAITTIME')) {
                                        $resultArrItem['error'] = 'Spam protection';
                                        $resultArrItem['data'] = $diff;
                                        $resultArray[] = $resultArrItem;
                                        $bContinue = false;
                                        $message->delete();
                                    }
                                }

                                if ($bContinue) {
                                    $data = TicketModel::create($attr);
                                    if ($data) {
                                        $resultArray[] = $resultArrItem;
                                        $message->delete();

                                        if ($ws->emailconfirm) {
                                            $htmlCode = view('mail.ticket_create_confirm', ['workspace' => $ws->name, 'name' => $attr['name'], 'email' => $attr['email'], 'hash' => $data->hash, 'subject' => $data->subject, 'text' => $data->text, 'confirmation' => $attr['confirmation']])->render();
                                        } else {
                                            $htmlCode = view('mail.ticket_create_notconfirm', ['workspace' => $ws->name, 'name' => $attr['name'], 'email' => $attr['email'], 'subject' => $data->subject, 'text' => $data->text, 'hash' => $data->hash])->render();
                                        }

                                        MailerModel::sendMail($attr['email'], '[' . $ws->company . '] ' . __('app.mail_ticket_creation') . ' [ID:' . $data->hash .  ']', $htmlCode);

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
                                                    $htmlCode = view('mail.new_ticket_admin', ['workspace' => $ws->name, 'name' => $adminUser->surname . ' ' . $adminUser->lastname, 'ticketid' => $data->id, 'custname' => $attr['name'], 'email' => $attr['email'], 'subject' => $data->subject, 'text' => $data->text])->render();
                                                    MailerModel::sendMail($adminUser->email, '[' . $ws->company . '] ' . __('app.mail_ticket_in_group'), $htmlCode);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $resultArray;
    }

    /**
     * Process workspace inboxes.
     *
     * @return array The result of processed items
     */
    public static function processWorkspaceInboxes()
    {
        $resultArr = array();

        $workspaces = WorkSpaceModel::where('mailer_useown', '=', true)->get();
        foreach ($workspaces as $workspace) {
            try {
                $_ENV['TEMP_WORKSPACE'] = $workspace->id;
                $_ENV['SMTP_HOST'] = $workspace->mailer_host_smtp;
                $_ENV['SMTP_PORT'] = $workspace->mailer_port_smtp;
                $_ENV['MAILSERV_HOST'] = $workspace->mailer_host_imap;
                $_ENV['MAILSERV_PORT'] = $workspace->mailer_port_imap;
                $_ENV['MAILSERV_INBOXNAME'] = $workspace->mailer_inbox;
                $_ENV['SMTP_FROMADDRESS'] = $workspace->mailer_address;
                $_ENV['MAILSERV_EMAILADDR'] = $workspace->mailer_address;
                $_ENV['SMTP_FROMNAME'] = $workspace->mailer_fromname;
                $_ENV['SMTP_USERNAME'] = $workspace->mailer_username;
                $_ENV['MAILSERV_USERNAME'] = $workspace->mailer_username;
                $_ENV['SMTP_PASSWORD'] = $workspace->mailer_password;
                $_ENV['MAILSERV_PASSWORD'] = $workspace->mailer_password;
                $_ENV['APP_NAME'] = $workspace->company;

                $mailer = new self();
                $data = $mailer->processInbox();

                MailTimeoutModel::clear($_ENV['TEMP_WORKSPACE']);

                $resultArr[] = array('workspace' => $workspace->id, 'data' => $data);
            } catch (ConnectionFailedException $e) {
                $_ENV['APP_NAME'] = 'HelpRealm';

                MailTimeoutModel::add($_ENV['TEMP_WORKSPACE']);
                MailTimeoutModel::handleIfFull($_ENV['TEMP_WORKSPACE']);

                $resultArr[] = array('workspace' => $workspace->id, 'error' => $e->getCode(), 'type' => 'ConnectionFailedException', 'data' => $e->getMessage());
            } catch (\Exception $e) {
                $resultArr[] = array('workspace' => $workspace->id, 'error' => $e->getCode(), 'type' => 'Exception', 'data' => $e->getMessage());
            }
        }

        return $resultArr;
    }
}
