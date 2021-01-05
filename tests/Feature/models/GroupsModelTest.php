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
use App\GroupsModel;

/**
 * Class GroupsModelTest
 * 
 * Test for GroupsModel
 */
class GroupsModelTest extends TestCase
{
    /**
     * Test for getPrimaryGroup
     *
     * @return void
     */
    public function testGetPrimaryGroup()
    {
        $result = GroupsModel::getPrimaryGroup(env('DATA_WORKSPACE'));
        $this->assertTrue($result !== null);
        $this->assertTrue(isset($result->name));
    }

    /**
     * Test for get
     * 
     * @return void
     */
    public function testGet()
    {
        $result = GroupsModel::get(0);
        $this->assertTrue($result === null);

        $result = GroupsModel::get(env('DATA_GROUPID'));
        $this->assertTrue($result !== null);
        $this->assertTrue(isset($result->name));
    }
}
