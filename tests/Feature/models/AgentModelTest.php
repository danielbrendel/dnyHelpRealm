<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2024 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\AgentModel;

/**
 * Class AgentModelTest
 * 
 * Test for AgentModel
 */
class AgentModelTest extends TestCase
{
    /**
     * Test queryAgent
     *
     * @return void
     */
    public function testQueryAgent()
    {
        $result = AgentModel::queryAgent(0);
        $this->assertEquals(null, $result);

        $result = AgentModel::queryAgent(env('DATA_USERID'));
        $this->assertIsObject($result);
        $this->assertTrue(isset($result->email));
    }

    /**
     * Test isSuperAdmin
     * 
     * @return void
     */
    public function testIsSuperAgent()
    {
        $result = AgentModel::isSuperAdmin(0);
        $this->assertFalse((bool)$result);
        $result = AgentModel::isSuperAdmin(env('DATA_USERID'));
        $this->assertTrue((bool)$result);
    }
}
