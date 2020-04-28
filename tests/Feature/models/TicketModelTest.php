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
use App\TicketModel;

/**
 * Class TicketModelTest
 * 
 * Test for TicketModel
 */
class TicketModelTest extends TestCase
{
    /**
     * Test for queryAgentTickets
     *
     * @return void
     */
    public function testQueryAgentTickets()
    {
        $result = TicketModel::queryAgentTickets(env('DATA_USERID'));
        $this->assertIsObject($result);
        $this->assertTrue(count($result) > 0);
        $this->assertTrue(isset($result[0]->subject));
    }
}
