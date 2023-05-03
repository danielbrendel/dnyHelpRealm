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
 * Class TicketModel
 *
 * Represents tickets
 */
class TicketModel extends Model
{
    protected $fillable = ['workspace', 'hash', 'address', 'name', 'email', 'confirmation', 'subject', 'text', 'group', 'assignee', 'attachments', 'prio', 'status', 'type'];

    /**
     * Query tickets of agent
     *
     * @param int $ag The assignee agent ID
     * @param bool $filter_closed If closed tickets shall not be included
     * @return mixed
     */
    public static function queryAgentTickets($ag, $filter_closed = false)
    {
        $tickets = TicketModel::where('assignee', '=', $ag);

        if ($filter_closed) {
            $tickets->where('status', '<>', 3);
        }

        return $tickets->orderBy('updated_at', 'desc')->orderBy('status', 'asc')->get();
    }

    /**
     * Get first created ticket of workspace
     *
     * @param $ws
     * @return mixed
     */
    public static function getFirstTicket($ws)
    {
        $ticket = TicketModel::where('workspace', '=', $ws)->orderBy('created_at', 'asc')->first();

        return $ticket;
    }

    /**
     * Get export tickets
     *
     * @param $ws
     * @param $date_from
     * @param $date_to
     * @return mixed
     */
    private static function getPreparedExportTickets($ws, $date_from, $date_to)
    {
        $tickets = TicketModel::where('workspace', '=', $ws)->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($date_from)))->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($date_to)))->orderBy('created_at', 'asc')->get();
        foreach ($tickets as &$ticket) {
            switch ($ticket->status) {
                case 0:
                    $ticket->status = __('app.ticket_status_confirmation');
                    break;
                case 1:
                    $ticket->status = __('app.ticket_status_open');
                    break;
                case 2:
                    $ticket->status = __('app.ticket_status_waiting');
                    break;
                case 3:
                    $ticket->status = __('app.ticket_status_closed');
                    break;
            }

            switch ($ticket->prio) {
                case 1:
                    $ticket->prio = __('app.prio_low');
                    break;
                case 2:
                    $ticket->prio = __('app.prio_med');
                    break;
                case 3:
                    $ticket->prio = __('app.prio_high');
                    break;
            }

            $ticket->type = TicketsHaveTypes::where('id', '=', $ticket->type)->where('workspace', '=', $ws)->first()->name;
            $ticket->group = GroupsModel::where('id', '=', $ticket->group)->where('workspace', '=', $ws)->first()->name;

            $assignee = AgentModel::queryAgent($ticket->assignee);
            if ($assignee) {
                $ticket->assignee = $assignee->surname . ' ' . $assignee->lastname . ' / ' . $assignee->email;
            }
        }

        return $tickets;
    }

    /**
     * Export tickets as CSV
     *
     * @param $ws
     * @param $date_from
     * @param $date_to
     */
    public static function exportTicketsAsCsv($ws, $date_from, $date_to)
    {
        $tickets = static::getPreparedExportTickets($ws, $date_from, $date_to)->toArray();

        $heading = array(
            'id',
            'workspace',
            'hash',
            'address',
            'subject',
            'text',
            'name',
            'email',
            'confirmation',
            'type',
            'status',
            'prio',
            'group',
            'assignee',
            'notes',
            'created_at',
            'updated_at'
        );

        $handle = fopen('php://memory', 'w+');
        fputcsv($handle, $heading);
        foreach ($tickets as $ticket) {
            fputcsv($handle, $ticket);
        }
        rewind($handle);
        $streamContent = stream_get_contents($handle);
        fclose($handle);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=ticket_export.csv');
        echo $streamContent;
        exit();
    }

    /**
     * Export tickets as JSON
     *
     * @param $ws
     * @param $date_from
     * @param $date_to
     * @return mixed
     */
    public static function exportTicketsAsJson($ws, $date_from, $date_to)
    {
        $tickets = static::getPreparedExportTickets($ws, $date_from, $date_to)->toJson();

        $handle = fopen('php://memory', 'w+');
        fwrite($handle, $tickets);
        rewind($handle);
        $streamContent = stream_get_contents($handle);
        fclose($handle);

        header('Content-Type: text/json');
        header('Content-Disposition: attachment; filename=ticket_export.json');
        echo $streamContent;
        exit();
    }

    /**
     * Delete a ticket and its associated data
     * 
     * @param $id
     * @return bool
     */
    public static function deleteTicket($id)
    {
        $ticket = TicketModel::where('id', '=', $id)->where('status', '=', 3)->first();
        if (!$ticket) {
            return false;
        }

        $ticket_files = TicketsHaveFiles::where('ticket_hash', '=', $ticket->hash)->get();
        foreach ($ticket_files as $file) {
            unlink(public_path() . '/uploads/' . $file->file);
            $file->delete();
        }

        $ticket_posts = TicketThreadModel::where('ticket_id', '=', $ticket->id)->get();
        foreach ($ticket_posts as $post) {
            $post->delete();
        }

        $ticket->delete();

        return true;
    }
}
