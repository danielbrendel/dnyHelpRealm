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
use App\GroupsModel;

/**
 * Class GroupsControllerTest
 * 
 * Test for GroupsController
 */
class GroupsControllerTest extends TestCase
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
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/group/list');
        $response->assertStatus(200);
        $response->assertViewIs('groups.list');
        $response->assertSee('#' . env('DATA_GROUPID'));
    }

    /**
     * Test for viewCreateGroup
     *
     * @return void
     */
    public function testViewCreateGroup()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/group/create');
        $response->assertStatus(200);
        $response->assertViewIs('groups.create');
        $response->assertSee(__('app.group_create'));
    }

    /**
     * Test for createGroup
     * 
     * @return int
     */
    public function testCreateGroup()
    {
        $name = md5(random_bytes(55));
        $description = md5(random_bytes(55));

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/group/create', [
            'name' => $name,
            'description' => $description,
            'def' => '0',
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        
        $result = GroupsModel::where('name', '=', $name)->where('description', '=', $description)->where('workspace', '=', env('DATA_WORKSPACE'))->first();
        $this->assertIsObject($result);
        $this->assertEquals($name, $result->name);

        return $result->id;
    }

    /**
     * Test for viewGroup
     *
     * @depends testCreateGroup
     * @param int $id
     * @return int
     */
    public function testViewEdit($id)
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/group/' . $id . '/show');
        $response->assertStatus(200);
        $response->assertViewIs('groups.view');
        $response->assertSee(__('app.group_view'));

        return $id;
    }

    /**
     * Test for editGroup
     * 
     * @depends testViewEdit
     * @param int $id
     * @return int
     */
    public function testEditGroup($id)
    {
        $name = md5(random_bytes(55));
        $description = md5(random_bytes(55));

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/group/' . $id . '/edit', [
            'name' => $name,
            'description' => $description,
            'def' => '1',
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        
        $result = GroupsModel::where('id', '=', $id)->first();
        $this->assertIsObject($result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($description, $result->description);
        $this->assertEquals(1, $result->def);

        return $result->id;
    }

    /**
     * Test for deleteGroup
     * 
     * @depends testEditGroup
     * @param int $id
     * @return void
     */
    public function testDeleteGroup($id)
    {
        $result = GroupsModel::where('id', '=', $id)->first();
        $this->assertIsObject($result);

        $response = $this->delete('/' . env('DATA_WORKSPACENAME') . '/group/' . $id . '/delete');
        $response->assertStatus(302);
        
        $result = GroupsModel::where('id', '=', $id)->first();
        $this->assertTrue($result === null);
    }
}
