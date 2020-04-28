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
use App\User;

/**
 * Class UserTest
 * 
 * Test for User
 */
class UserTest extends TestCase
{
    /**
     * Test for getAgent
     *
     * @return void
     */
    public function testGetAgent()
    {
        $result = User::getAgent(env('DATA_USERID'));
        $this->assertIsObject($result);
        $this->assertTrue(isset($result->email));
    }

    /**
     * Test for get
     * 
     * @return void
     */
    public function testGet()
    {
        $result = User::get(env('DATA_USERID'));
        $this->assertIsObject($result);
        $this->assertTrue(isset($result->email));
    }

    /**
     * Test for getByEmail
     * 
     * @return void
     */
    public function testGetByEmail()
    {
        $result = User::getByEmail(env('DATA_USEREMAIL'));
        $this->assertIsObject($result);
        $this->assertTrue(isset($result->email));
    }
}
