<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\AgentModel;
use App\User;
use App\WorkSpaceModel;
use App\TicketsHaveTypes;

/**
 * Class SettingsControllerTest
 *
 * Test for SettingsController
 */
class SettingsControllerTest extends TestCase
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
     * Test for showAgent
     *
     * @return void
     */
    public function testShowAgent()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/settings/agent');
        $response->assertStatus(200);
        $response->assertViewIs('settings.agent');
        $response->assertViewHas('location', __('app.settings'));
    }

    /**
     * Test for save
     *
     * @return void
     */
    public function testSave()
    {
        $agent = AgentModel::where('id', '=', env('DATA_USERID'))->first();

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/settings/save', [
            'surname' => $agent->surname,
            'lastname' => $agent->lastname,
            'email' => $agent->email,
            'password' => env('DATA_USERPW'),
            'password_confirm' => env('DATA_USERPW'),
            'mailonticketingroup' => '1',
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $agent2 = AgentModel::where('id', '=', env('DATA_USERID'))->first();
        $this->assertEquals($agent->email, $agent2->email);
    }

    /**
     * Test for saveLocale
     *
     * @return void
     */
    public function testSaveLocale()
    {
        $user = User::where('id', '=', env('DATA_USERID'))->first();
        $user->language = 'de';
        $user->save();

        \App::setLocale('de');

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/settings/locale', [
            'lang' => 'en',
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $this->assertEquals('en', \App::getLocale());

        $user = User::where('id', '=', env('DATA_USERID'))->first();
        $this->assertEquals('en', $user->language);
    }

    /**
     * Test for viewSystemSettings
     *
     * @return void
     */
    public function testViewSystemSettings()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/settings/system');
        $response->assertStatus(200);
        $response->assertViewIs('settings.system');
        $response->assertViewHas('location', __('app.system_settings'));
    }

    /**
     * Test for saveSystemSettings
     *
     * @return void
     */
    public function testSaveSystemSettings()
    {
        $infomessage = md5(random_bytes(55));

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/settings/system', [
            'infomessage' => $infomessage,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $ws = WorkSpaceModel::where('name', '=', env('DATA_WORKSPACENAME'))->first();
        $this->assertEquals($infomessage, $ws->welcomemsg);
    }

    /**
     * Test for addTicketType
     *
     * @return int
     */
    public function testAddTicketType()
    {
        $name = md5(random_bytes(55));

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/tickettype/add', [
            'name' => $name,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $ticketType = TicketsHaveTypes::where('workspace', '=', env('DATA_WORKSPACE'))->where('name', '=', $name)->first();
        $this->assertEquals($name, $ticketType->name);

        return $ticketType->id;
    }

    /**
     * Test for editTicketType
     *
     * @depends testAddTicketType
     * @param int $id
     * @return int
     */
    public function testEditTicketType($id)
    {
        $name = md5(random_bytes(55));

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/tickettype/' . $id . '/edit', [
            'name' => $name,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $ticketType = TicketsHaveTypes::where('workspace', '=', env('DATA_WORKSPACE'))->where('id', '=', $id)->first();
        $this->assertEquals($name, $ticketType->name);

        return $ticketType->id;
    }

    /**
     * Test for deleteTicketType
     *
     * @depends testEditTicketType
     * @param int $id
     * @return void
     */
    public function testDeleteTicketType($id)
    {
        $response = $this->delete('/' . env('DATA_WORKSPACENAME') . '/tickettype/' . $id . '/delete');
        $response->assertStatus(302);

        $ticketType = TicketsHaveTypes::where('workspace', '=', env('DATA_WORKSPACE'))->where('id', '=', $id)->first();
        $this->assertTrue($ticketType === null);
    }

    /**
     * Test for generateApiToken
     *
     * @return void
     */
    public function testGenerateToken()
    {
        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/settings/system/apitoken');
        $response->assertStatus(200);

        $content = $response->getOriginalContent();
        $this->assertEquals(200, $content['code']);
        $this->assertTrue(isset($content['token']));

        $row = WorkSpaceModel::where('name', '=', env('DATA_WORKSPACENAME'))->where('apitoken', '=', $content['token'])->first();
        $this->assertTrue($row !== null);
    }
}
