<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\WorkSpaceModel;
use Auth;

/**
 * Class WorkSpaceModelTest
 * 
 * Test for WorkSpaceModel
 */
class WorkSpaceModelTest extends TestCase
{
    /**
     * Test for get
     *
     * @return void
     */
    public function testGet()
    {
        $result = WorkSpaceModel::get(env('DATA_WORKSPACENAME'));
        $this->assertIsObject($result);
        $this->assertTrue(isset($result->name));
    }

    /**
     * Test for isLoggedIn
     * 
     * @return void
     */
    public function testIsLoggedIn()
    {
        $result = Auth::attempt([
            'email' => env('DATA_USEREMAIL'),
            'password' => env('DATA_USERPW')
        ]);

        $this->assertTrue($result);

        $result = WorkSpaceModel::isLoggedIn(env('DATA_WORKSPACENAME'));
        $this->assertTrue($result);
    }
}
