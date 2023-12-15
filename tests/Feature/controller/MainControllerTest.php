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
use App\User;
use App\CaptchaModel;
use App\AgentModel;
use App\GroupsModel;
use App\WorkSpaceModel;
use App\AgentsHaveGroups;

/**
 * Class MainControllerTest
 *
 * Test for MainController
 */
class MainControllerTest extends TestCase
{
    /**
     * Perform login
     *
     * @return void
     */
    private function login()
    {
        $response = $this->post('/login', [
            'email' => env('DATA_USEREMAIL'),
            'password' => env('DATA_USERPW'),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
    }

    /**
     * Perform logout
     *
     * @return void
     */
    public function logout()
    {
        $response = $this->get('/logout');
        $response->assertStatus(302);
    }

    /**
     * Test for workspaceIndex
     *
     * @return void
     */
    public function testWorkspaceIndex()
    {
        $this->logout();
        $response = $this->get('/' . env('DATA_WORKSPACENAME'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard_customer');
        $response->assertSee(__('app.ticket_create'));

        $this->login();
        $response = $this->get('/' . env('DATA_WORKSPACENAME'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard_agent');
        $response->assertSee(__('app.welcome'));
        $this->logout();
    }

    /**
     * Test for index
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /**
     * Test for news
     *
     * @return void
     */
    public function testNews()
    {
        $response = $this->get('/news');
        $response->assertStatus(200);
        $response->assertViewIs('news');
    }

    /**
     * Test for faq
     *
     * @return void
     */
    public function testFaq()
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
        $response->assertViewIs('faq');
    }

    /**
     * Test for imprint
     *
     * @return void
     */
    public function testImprint()
    {
        $response = $this->get('/imprint');
        $response->assertStatus(200);
        $response->assertViewIs('imprint');
    }

    /**
     * Test for tac
     *
     * @return void
     */
    public function testTac()
    {
        $response = $this->get('/tac');
        $response->assertStatus(200);
        $response->assertViewIs('tac');
    }

    /**
     * Test for login
     *
     * @return void
     */
    public function testLogin()
    {
        $this->login();
    }

    /**
     * Test for login
     *
     * @return void
     */
    public function testLogout()
    {
        $this->logout();
    }

    /**
     * Test for recovering password
     *
     * @return void
     */
    public function testRecoverPassword()
    {
        $response = $this->post('/recover', [
            'email' => env('DATA_USEREMAIL')
        ]);
        $response->assertStatus(302);

        $user = User::where('id', '=', env('DATA_USERID'))->first();
        $this->assertIsObject($user);
        $this->assertTrue(strlen($user->password_reset) > 0);

        $response = $this->get('/reset?hash=' . $user->password_reset);
        $response->assertStatus(200);
        $response->assertViewIs('resetpw');

        $password = env('DATA_USERPW');
        $response = $this->post('/reset?hash=' . $user->password_reset, [
            'password' => $password,
            'password_confirm' => $password,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $user = User::where('id', '=', env('DATA_USERID'))->first();
        $this->assertIsObject($user);
        $this->assertTrue(strlen($user->password_reset) === 0);
    }

    /**
     * Test for registering process
     *
     * @return void
     */
    public function testRegistration()
    {
        $this->markTestSkipped('Session ID');

        $response = $this->get('/');
        $response->assertStatus(200);

        $token = md5(random_bytes(55));

        $response = $this->post('/register', [
            'company' => $token,
            'fullname' => $token . ' ' . $token,
            'email' => $token . '@test.de',
            'password' => env('DATA_USERPW'),
            'password_confirmation' => env('DATA_USERPW'),
            'captcha' => intval(CaptchaModel::querySum(session()->getId())),
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);

        $user = User::where('email', '=', $token . '@test.de')->first();
        $this->assertIsObject($user);
        $this->assertNotEquals('_confirmed', $user->account_confirm);

        $agent = AgentModel::where('id', '=', $user->user_id)->first();
        $this->assertIsObject($agent);
        $this->assertEquals($token . '@test.de', $agent->email);

        $ws = WorkSpaceModel::where('company', '=', $token)->first();
        $this->assertIsObject($ws);

        $group = GroupsModel::where('workspace', '=', $ws->id)->first();
        $this->assertIsObject($group);

        $ingroup = AgentsHaveGroups::where('group_id', '=', $group->id)->where('agent_id', '=', $agent->id)->first();
        $this->assertIsObject($ingroup);
    }

    /**
     * Test for e-mail cronjob
     *
     * @return void
     */
    public function testMailservice()
    {
        $response = $this->get('/mailservice/' . env('MAILSERV_CRONPW'));
        $response->assertStatus(200);
        $content = $response->decodeResponseJson();
        $this->assertEquals(200, $content['code']);
    }

    /**
     * Test for client endpoint index view
     *
     * @return void
     */
    public function testClepIndex()
    {
        $response = $this->get('/clep/index');
        $response->assertStatus(200);
        $response->assertViewIs('clep.index');
    }

    /**
     * Test for client endpoint notifications
     *
     * @return void
     */
    public function testClepNotifications()
    {
        $this->login();

        $response = $this->get('/clep/notifications');
        $response->assertStatus(200);
        $content = $response->getOriginalContent();
        $this->assertEquals(200, $content['code']);
        $this->assertTrue(isset($content['data']));

        $this->logout();
    }

    /**
     * Test for client endpoint statistics
     *
     * @return void
     */
    public function testClepStatistics()
    {
        $this->login();

        $response = $this->get('/clep/statistics');
        $response->assertStatus(200);
        $content = $response->getOriginalContent();
        $this->assertEquals(200, $content['code']);
        $this->assertTrue(isset($content['data']));

        $this->logout();
    }
}
