<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

     Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\FaqModel;

/**
 * Class ApiControllerTest
 *
 * Test for ApiController
 */
class ApiControllerTest extends TestCase
{
    /**
     * Create ticket test
     *
     * @return void
     */
    public function testCreateTicket()
    {
        $token = md5(random_bytes(55));
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $response = $this->post('/api/' . env('DATA_WORKSPACENAME') . '/ticket/create', [
            'apitoken' => env('DATA_WORKSPACEAPITOKEN'),
            'subject' => $token,
            'text' => $token,
            'name' => $token . ' ' . $token,
            'email' => $token . '@test.de',
            'type' => '1',
            'prio' => '1',
            'attachment' => null
        ]);

        $response->assertStatus(200);
        $operationResult = $response->getOriginalContent();

        $this->assertTrue(isset($operationResult->code));
        $this->assertEquals(201, $operationResult->code);
    }
}
