<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
Use App\User;
use App\AgentModel;
use App\AgentsHaveGroups;

/**
 * Class AgentControllerTest
 * 
 * Test for agent controller
 */
class AgentControllerTest extends TestCase
{
    /**
     * Set up controller test
     * 
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();

        $this->post('/login', [
            'email' => env('DATA_USEREMAIL'),
            'password' => env('DATA_USERPW'),
            '_token' => csrf_token()
        ]);
    }

    /**
     * Test for viewList
     *
     * @return void
     */
    public function testViewList()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/agent/list');
        $response->assertStatus(200);
        $response->assertViewIs('agents.list');
        $response->assertSee(env('DATA_USEREMAIL'));
    }

    /**
     * Test for viewShow
     *
     * @return void
     */
    public function testViewShow()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/agent/' . env('DATA_USERID') . '/show');
        $response->assertStatus(200);
        $response->assertViewIs('agents.show');
        $response->assertSee(env('DATA_USEREMAIL'));
        $response->assertSee(env('DATA_GROUPNAME'));
    }

     /**
     * Test for viewCreate
     *
     * @return void
     */
    public function testViewCreate()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/agent/create');
        $response->assertStatus(200);
        $response->assertViewIs('agents.create');
        $response->assertSee('Set as superadmin');
    }

    /**
     * Test for createAgent
     *
     * @return int
     */
    public function testCreateAgent()
    {
        $surname = md5(random_bytes(55));
        $lastname = md5(random_bytes(55));
        $email = md5(random_bytes(55)) . '@test.de';

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/agent/create', [
            'surname' => $surname,
            'lastname' => $lastname,
            'email' => $email,
            'position' => 'Administrator',
            'superadmin' => '1',
            'password' => 'password',
            'password_confirm' => 'password',
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        
        $result = User::where('email', '=', $email)->first();
        $this->assertIsObject($result);
        $this->assertTrue(isset($result->account_confirm));
        
        $result = AgentModel::where('user_id', '=', $result->id)->first();
        $this->assertIsObject($result);
        $this->assertTrue(isset($result->superadmin));

        return $result->id;
    }

    /**
     * Test for editAgent
     *
     * @depends testCreateAgent
     * @param int $id
     * @return int
     */
    public function testEditAgent($id)
    {
        $newSurname = md5(random_bytes(55));
        $newLastname = md5(random_bytes(55));
        $newEmail = md5(random_bytes(55)) . '@test.de';
        $newPosition = md5(random_bytes(55));
        $newPassword = md5(random_bytes(55));
        $newPwConfirm = $newPassword;

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/agent/' . $id .  '/edit', [
            'surname' => $newSurname,
            'lastname' => $newLastname,
            'email' => $newEmail,
            'position' => $newPosition,
            'password' => $newPassword,
            'password_confirm' => $newPwConfirm,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        
        $result = AgentModel::where('id', '=', $id)->first();
        $this->assertIsObject($result);
        $this->assertEquals($newSurname, $result->surname);
        $this->assertEquals($newLastname, $result->lastname);
        $this->assertEquals($newEmail, $result->email);
        $this->assertEquals($newPosition, $result->position);
        $result = User::where('id', '=', $result->user_id)->first();
        $this->assertIsObject($result);
        $this->assertTrue($result->password !== '');

        return $result->user_id;
    }

    /**
     * Test for setActiveStatus
     *
     * @depends testEditAgent
     * @param int $id
     * @return int
     */
    public function testSetActiveStatus($id)
    {
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/agent/' . $id .  '/active/0');
        $response->assertStatus(302);
        
        $result = AgentModel::where('id', '=', $id)->first();
        $this->assertIsObject($result);
        $this->assertEquals(0, $result->active);

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/agent/' . $id .  '/active/1');
        $response->assertStatus(302);
        
        $result = AgentModel::where('id', '=', $id)->first();
        $this->assertIsObject($result);
        $this->assertEquals(1, $result->active);

        return $result->id;
    }

    /**
     * Test for assignToGroup
     *
     * @depends testSetActiveStatus
     * @param int $id
     * @return int
     */
    public function testAssignToGroup($id)
    {
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/agent/' . $id .  '/group/' . env('DATA_GROUPID') . '/add');
        $response->assertStatus(302);
        
        $result = AgentsHaveGroups::where('agent_id', '=', $id)->where('group_id', '=', env('DATA_GROUPID'))->first();
        $this->assertIsObject($result);

        return $id;
    }

    /**
     * Test for removeFromGroup
     *
     * @depends testAssignToGroup
     * @param int $id
     * @return int
     */
    public function testRemoveFromGroup($id)
    {
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/agent/' . $id .  '/group/' . env('DATA_GROUPID') . '/remove');
        $response->assertStatus(302);
        
        $result = AgentsHaveGroups::where('agent_id', '=', $id)->where('group_id', '=', env('DATA_GROUPID'))->first();
        $this->assertTrue($result === null);

        return $id;
    }

    /**
     * Test for deleteAgent
     *
     * @depends testRemoveFromGroup
     * @param int $id
     * @return void
     */
    public function testDeleteAgent($id)
    {
        $response = $this->delete('/' . env('DATA_WORKSPACENAME') . '/agent/' . $id .  '/delete');
        $response->assertStatus(302);
        
        $result = AgentModel::where('id', '=', $id)->first();
        $this->assertTrue($result === null);

        $result = User::where('user_id', '=', $id)->first();
        $this->assertTrue($result === null);
    }
}
