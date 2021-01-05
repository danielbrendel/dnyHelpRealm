<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\AgentsHaveGroups;

/**
 * Class AgentsHaveGroupsTest
 * 
 * Test for AgentsHaveGroups
 */
class AgentsHaveGroupsTest extends TestCase
{
    /**
     * Test for putAgentInGroup
     *
     * @return void
     */
    public function testPutAgentInGroup()
    {
        $result = AgentsHaveGroups::putAgentInGroup(env('DATA_USERID'), env('DATA_GROUPID'));
        $this->assertTrue($result);
    }

    /**
     * Test for removeAgentFromGroup
     *
     * @return void
     */
    public function testRemoveAgentFromGroup()
    {
        $result = AgentsHaveGroups::removeAgentFromGroup(env('DATA_USERID'), env('DATA_GROUPID'));
        $this->assertTrue($result);
    }
}
