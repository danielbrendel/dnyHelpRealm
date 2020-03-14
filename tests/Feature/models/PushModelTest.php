<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 0.1
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\PushModel;

/**
 * Class PushModelTest
 * 
 * Test for PushModel
 */
class PushModelTest extends TestCase
{
    /**
     * Test for addNotification
     *
     * @return void
     */
    public function testAddNotification()
    {
        $random_ident = 'TestCase_' . md5(random_bytes(55));

        PushModel::addNotification($random_ident, $random_ident, env('DATA_USERID'));
        $this->addToAssertionCount(1);

        $result = PushModel::where('title', '=', $random_ident)->where('message', '=', $random_ident)->first();
        $this->assertTrue($result !== null);
        $this->assertEquals($random_ident, $result->title);
        $this->assertEquals($random_ident, $result->message);
        $this->assertEquals(0, $result->seen);
    }

    /**
     * Test for getUnseenNotifications
     * 
     * @return void
     */
    public function testGetUnseenNotifications()
    {
        $result = PushModel::getUnseenNotifications(env('DATA_USERID'));
        $this->assertIsObject($result);
        $this->assertTrue(count($result) > 0);
        $this->assertTrue(isset($result[0]->title));
    }
}
