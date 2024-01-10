<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\TicketModel;
use App\TicketThreadModel;
use App\CaptchaModel;

/**
 * Class TicketControllerTest
 *
 * Test for TicketController
 */
class TicketControllerTest extends TestCase
{
    /**
     * Set up controller test
     *
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();

        $this->login();
    }

    /**
     * Perform login
     *
     * @return void
     */
    private function login()
    {
        $this->post('/login', [
            'email' => env('DATA_USEREMAIL'),
            'password' => env('DATA_USERPW'),
            '_token' => csrf_token()
        ]);
    }

    /**
     * Perform logout
     *
     * @return void
     */
    private function logout()
    {
        $this->get('/logout');
    }

    /**
     * Test for viewTicketList
     *
     * @return void
     */
    public function testViewTicketList()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/ticket/list');
        $response->assertStatus(200);
        $response->assertViewIs('ticket.list');
    }

    /**
     * Test for viewShowTicketAgent
     *
     * @return void
     */
    public function testViewShowTicketAgent()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/ticket/' . env('DATA_TICKETID') . '/show');
        $response->assertStatus(200);
        $response->assertViewIs('ticket.agent_show');
    }

    /**
     * Test for viewShowTicketClient
     *
     * @return void
     */
    public function testViewShowTicketClient()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/ticket/show/' . env('DATA_TICKETHASH'));
        $response->assertStatus(200);
        $response->assertViewIs('ticket.customer_show');
    }

    /**
     * Test for createTicketAgent
     *
     * @return int
     */
    public function testCreateTicketAgent()
    {
        $token = md5(random_bytes(55));
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/ticket/create/agent', [
            'subject' => $token,
            'text' => $token,
            'name' => $token . ' ' . $token,
            'email' => $token . '@test.de',
            'type' => 1,
            'prio' => 1,
            'group' => env('DATA_GROUPID'),
            'assignee' => 0,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $ticket = TicketModel::where('subject', '=', $token)->first();
        $this->assertIsObject($ticket);
        $this->assertEquals($token . '@test.de', $ticket->email);
        $this->assertEquals('_confirmed', $ticket->confirmation);

        $this->logout();

        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/ticket/show/' . $ticket->hash . '?confirmation=' . $ticket->confirmation);
        $response->assertStatus(200);
        $response->assertViewIs('ticket.customer_show');

        $ticket = TicketModel::where('id', '=', $ticket->id)->first();
        $this->assertEquals('_confirmed', $ticket->confirmation);

        $this->login();

        return $ticket->id;
    }

    /**
     * Test for viewCreateTicket
     *
     * @return void
     */
    public function testViewCreateTicket()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/ticket/create');
        $response->assertStatus(200);
        $response->assertViewIs('ticket.create');
    }

    /**
     * Test for assignToAgent
     *
     * @depends testCreateTicketAgent
     * @param int $id
     * @return int
     */
    public function testAssignToAgent($id)
    {
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/assign/agent/' . env('DATA_USERID'));
        $response->assertStatus(200);

        $ticket = TicketModel::where('id', '=', $id)->first();
        $this->assertIsObject($ticket);
        $this->assertEquals(env('DATA_USERID'), $ticket->assignee);

        return $id;
    }

    /**
     * Test for assignToGroup
     *
     * @depends testAssignToAgent
     * @param int $id
     * @return int
     */
    public function testAssignToGroup($id)
    {
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/assign/group/' . env('DATA_GROUPID'));
        $response->assertStatus(200);

        $ticket = TicketModel::where('id', '=', $id)->first();
        $this->assertIsObject($ticket);
        $this->assertEquals(env('DATA_GROUPID'), $ticket->group);

        return $id;
    }

    /**
     * Test for setStatus
     *
     * @depends testAssignToGroup
     * @param int $id
     * @return int
     */
    public function testSetStatus($id)
    {
        $status = 2;

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/status/' . $status);
        $response->assertStatus(200);

        $ticket = TicketModel::where('id', '=', $id)->first();
        $this->assertIsObject($ticket);
        $this->assertEquals($status, $ticket->status);

        return $id;
    }

    /**
     * Test for setType
     *
     * @depends testSetStatus
     * @param int $id
     * @return int
     */
    public function testSetType($id)
    {
        $type = 2;

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/type/' . $type);
        $response->assertStatus(200);

        $ticket = TicketModel::where('id', '=', $id)->first();
        $this->assertIsObject($ticket);
        $this->assertEquals($type, $ticket->type);

        return $id;
    }

    /**
     * Test for setPriority
     *
     * @depends testSetType
     * @param int $id
     * @return int
     */
    public function testSetPriority($id)
    {
        $priority = 2;

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/prio/' . $priority);
        $response->assertStatus(200);

        $ticket = TicketModel::where('id', '=', $id)->first();
        $this->assertIsObject($ticket);
        $this->assertEquals($priority, $ticket->prio);

        return $id;
    }

    /**
     * Test for adding and editing comments
     *
     * @depends testSetPriority
     * @param int $id
     * @return void
     * @throws \Exception
     */
    public function testAddAndEditComments($id)
    {
        $this->markTestSkipped('Captcha');

        $ticket = TicketModel::where('id', '=', $id)->first();

        $comment = md5(random_bytes(55));
        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/comment/add', [
            'text' => $comment,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        $ticketThread = TicketThreadModel::where('ticket_id', '=', $id)->where('text', '=', $comment)->first();
        $this->assertIsObject($ticketThread);
        $this->assertEquals($comment, $ticketThread->text);

        $comment = md5(random_bytes(55));
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/comment/' . $ticketThread->id . '/edit', [
            'text' => $comment,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        $ticketThread = TicketThreadModel::where('ticket_id', '=', $id)->where('text', '=', $comment)->first();
        $this->assertIsObject($ticketThread);
        $this->assertEquals($comment, $ticketThread->text);

        $this->logout();

        $response = $this->get('/'. env('DATA_WORKSPACENAME') . '/ticket/show/' . $ticket->hash);
        $response->assertStatus(200);
        $response->assertViewIs('ticket.customer_show');
        $comment = md5(random_bytes(55));
        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/comment/add/guest', [
            'text' => $comment,
            'captcha' => intval(CaptchaModel::querySum(session()->getId())),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        $ticketThread = TicketThreadModel::where('ticket_id', '=', $id)->where('text', '=', $comment)->where('user_id', '=', 0)->first();
        $this->assertIsObject($ticketThread);
        $this->assertEquals($comment, $ticketThread->text);

        $comment = md5(random_bytes(55));
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . $id . '/comment/' . $ticketThread->id . '/edit/customer', [
            'text' => $comment,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        $ticketThread = TicketThreadModel::where('ticket_id', '=', $id)->where('text', '=', $comment)->first();
        $this->assertIsObject($ticketThread);
        $this->assertEquals($comment, $ticketThread->text);
    }

    /**
     * Test for viewSearch
     *
     * @return void
     */
    public function testViewSearch()
    {
        $this->login();

        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/ticket/search');
        $response->assertStatus(200);
        $response->assertViewIs('ticket.search');
    }

    /**
     * Test for search
     *
     * @depends testViewSearch
     * @return void
     */
    public function testSearch()
    {
        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/ticket/search', [
            'query' => env('DATA_TICKETID'),
            'type' => 1,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(200);
        $response->assertViewIs('ticket.searchresult');
        $response->assertSee('#' . env('DATA_TICKETID'));

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/ticket/search', [
            'query' => env('DATA_TICKETHASH'),
            'type' => 2,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(200);
        $response->assertViewIs('ticket.searchresult');
        $response->assertSee(env('DATA_TICKETID'));

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/ticket/search', [
            'query' => env('DATA_TICKETSUBJECT'),
            'type' => 3,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(200);
        $response->assertViewIs('ticket.searchresult');
        $response->assertSee(env('DATA_TICKETSUBJECT'));

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/ticket/search', [
            'query' => env('DATA_TICKETTEXT'),
            'type' => 4,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(200);
        $response->assertViewIs('ticket.searchresult');
        $response->assertSee(env('DATA_TICKETSUBJECT'));
    }

    /**
     * Test for saveNotes
     *
     * @return void
     */
    public function testSaveNotes()
    {
        $notes = md5(random_bytes(55));

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/ticket/' . env('DATA_TICKETID') . '/notes/save', [
            'notes' => $notes,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(200);

        $ticket = TicketModel::where('id', '=', env('DATA_TICKETID'))->first();
        $this->assertEquals($notes, $ticket->notes);
    }
}
