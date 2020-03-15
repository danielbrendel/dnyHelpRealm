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
use App\TicketThreadModel;
use App\TicketModel;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;

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
     * @return void
     */
    public function processInbox()
    {
        if ($this->client !== null) {
            $folders = $this->client->getFolders();
            foreach ($folders as $folder) {
                if ($folder->name == env('MAILSERV_INBOXNAME')) {
                    $mailmessages = $folder->messages()->all()->get();

                    foreach($mailmessages as $message){
                        $subject = $message->getSubject();
                        $idPos = strpos($subject, '[ID:');
                        if ($idPos !== false) {
                            $ticketHash = '';
                            for ($i = $idPos + 4; $i < strlen($subject); $i++) {
                                if ($subject[$i] === ']') {
                                    break;
                                }

                                $ticketHash .= $subject[$i];
                            }

                            $ticket = TicketModel::where('hash', '=', $ticketHash)->where('status', '<>', 3)->first();
                            if ($ticket !== null) {
                                $sender = $message->getFrom()[0]->mail;
                                $isAgent = AgentModel::where('email', '=', $sender)->first();

                                if (($isAgent === null) && ($ticket->confirmation !== '_confirmed')) {
                                    $ticket->confirmation = '_confirmed';
                                    $ticket->status = 1;
                                    $ticket->save();
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

                                $attachments = $message->getAttachments();
                                foreach ($attachments as $file) {
                                    if ($file->getSize() <= $this->iniFileSize()) {
                                        $newName = md5(random_bytes(55)) . '.' . $file->getExtension();
                                        $file->save(public_path() . '/uploads', $newName);

                                        $ticketFile = new TicketsHaveFiles();
                                        $ticketFile->ticket_hash = $ticket->hash;
                                        $ticketFile->file = $newName;
                                        $ticketFile->save();
                                    }
                                }

                                $message->delete();

                                $ws = WorkSpaceModel::where('id', '=', $ticket->workspace)->first();
                                
                                if ($ws !== null) {
                                    if ($isAgent !== null) {
                                        $htmlCode = view('mail.ticket_reply_agent', ['workspace' => $ws->name, 'name' => $ticket->name, 'hash' => $ticket->hash, 'agent' => $isAgent->surname . ' ' . $isAgent->lastname, 'message' => $message->getTextBody()])->render();
                                        @mail($ticket->email, '[ID:' . $ticket->hash .  '][' . $ws->company . '] ' . __('app.mail_ticket_agent_replied'), wordwrap($htmlCode, 70), 'Content-type: text/html; charset=utf-8' . "\r\n");
                                    } else {
                                        $assignee = AgentModel::where('id', '=', $ticket->assignee)->first();
                                        if ($assignee !== null) {
                                            $htmlCode = view('mail.ticket_reply_customer', ['workspace' => $ws->name, 'name' => $assignee->surname . ' ' . $assignee->lastname, 'id' => $ticket->id, 'customer' => $ticket->name, 'message' => $message->getTextBody()])->render();
                                            @mail($assignee->email, '[ID:' . $ticketHash . '][' . $ws->company . '] Ticket reply', wordwrap($htmlCode, 70), 'Content-type: text/html; charset=utf-8' . "\r\n");
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
