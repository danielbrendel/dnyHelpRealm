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
    const INBOX_NAME = 'INBOX';

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
     * Process inbox. Create thread from message and then delete the message
     * 
     * @return void
     */
    public function processInbox()
    {
        if ($this->client !== null) {
            $folders = $this->client->getFolders();
            foreach ($folders as $folder) {
                if ($folder->name === self::INBOX_NAME) {
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

                                $message->delete();

                                $ws = WorkSpaceModel::where('id', '=', $ticket->workspace)->first();
                                
                                if ($ws !== null) {
                                    if ($isAgent !== null) {
                                        $htmlCode = view('mail.ticket_reply_agent', ['workspace' => $ws->name, 'name' => $ticket->name, 'hash' => $ticket->hash, 'agent' => $isAgent->surname . ' ' . $isAgent->lastname, 'message' => $message->getTextBody()])->render();
                                        @mail($ticket->email, '[ID:' . $ticket->hash .  '][' . $ws->company . '] ' . __('app.mail_ticket_agent_replied'), wordwrap($htmlCode, 70), 'Content-type: text/html; charset=utf-8' . "\r\n");
                                    } else {
                                        $assignee = AgentModel::where('id', '=', $ticket->assignee)->first();
                                        if ($assignee !== null) {
                                            $htmlCode = view('mail.ticket_reply_customer', ['workspace' => $ws->name, 'name' => $assignee->surname . ' ' . $assignee->lastname, 'id' => $ticket->id, 'customer' => $ticket->text, 'message' => $message->getTextBody()])->render();
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
