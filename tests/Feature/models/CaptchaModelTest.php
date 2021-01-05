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
use App\CaptchaModel;

/**
 * Class CaptchaModelTest
 * 
 * Test for CaptchaModel
 */
class CaptchaModelTest extends TestCase
{
    /**
     * Test for createSum and querySum
     *
     * @return void
     */
    public function testCreateAndQuerySum()
    {
        $result = CaptchaModel::createSum('TEST_HASH');
        $this->assertIsArray($result);
        $this->assertTrue(count($result) === 2);

        $result2 = CaptchaModel::querySum('TEST_HASH');
        $this->assertTrue($result2 == $result[0] + $result[1]);
    }
}
