<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\MailserviceModel;

/**
 * Class MailserviceModelTest
 * 
 * Test for MailserviceModel
 */
class MailserviceModelTest extends TestCase
{
    /**
     * Test for processInput
     *
     * @return void
     */
    public function testProcessInbox()
    {
        $ms = new MailserviceModel;
        $ms->processInbox();
        $this->addToAssertionCount(1);
    }

    /**
     * Test for iniFileSize
     * 
     * @return void
     */
    public function testIniFileSize()
    {
        $ms = new MailserviceModel;
        $result = $ms->iniFileSize();
        $this->assertEquals(env('DATA_INIFILESIZE'), $result);
    }
}
