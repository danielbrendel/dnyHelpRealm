<?php

/*
    HelpRealm (dnyHelpRealm) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

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
 * Class FaqControllerTest
 * 
 * Test for FaqController
 */
class FaqControllerTest extends TestCase
{
    /**
     * Set up controller test
     * 
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();

        $this->post('/login', [
            'email' => env('DATA_USEREMAIL'),
            'password' => env('DATA_USERPW'),
            '_token' => csrf_token()
        ]);
    }

    /**
     * Test for viewList
     *
     * @return void
     */
    public function testViewList()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/faq/list');
        $response->assertStatus(200);
        $response->assertViewIs('faq.list');
        $response->assertSee('#' . env('DATA_FAQID'));
    }

    /**
     * Test for viewCreate
     *
     * @return void
     */
    public function testViewCreate()
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/faq/create');
        $response->assertStatus(200);
        $response->assertViewIs('faq.create');
        $response->assertSee(__('app.faq_create'));
    }

    /**
     * Test for create
     * 
     * @return int
     */
    public function testCreate()
    {
        $question = md5(random_bytes(55));
        $answer = md5(random_bytes(55));

        $response = $this->post('/' . env('DATA_WORKSPACENAME') . '/faq/create', [
            'question' => $question,
            'answer' => $answer,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        
        $result = FaqModel::where('question', '=', $question)->where('answer', '=', $answer)->where('workspace', '=', env('DATA_WORKSPACE'))->first();
        $this->assertIsObject($result);
        $this->assertEquals($question, $result->question);

        return $result->id;
    }

    /**
     * Test for viewEdit
     *
     * @depends testCreate
     * @param int $id
     * @return int
     */
    public function testViewEdit($id)
    {
        $response = $this->get('/' . env('DATA_WORKSPACENAME') . '/faq/' . $id . '/edit');
        $response->assertStatus(200);
        $response->assertViewIs('faq.edit');
        $response->assertSee(__('app.faq_edit'));

        return $id;
    }

    /**
     * Test for edit
     * 
     * @depends testViewEdit
     * @param int $id
     * @return int
     */
    public function testEdit($id)
    {
        $question = md5(random_bytes(55));
        $answer = md5(random_bytes(55));

        $response = $this->patch('/' . env('DATA_WORKSPACENAME') . '/faq/' . $id . '/edit', [
            'question' => $question,
            'answer' => $answer,
            '_token' => csrf_token()
        ]);
        $response->assertStatus(302);
        
        $result = FaqModel::where('question', '=', $question)->where('answer', '=', $answer)->where('workspace', '=', env('DATA_WORKSPACE'))->first();
        $this->assertIsObject($result);
        $this->assertEquals($question, $result->question);

        return $result->id;
    }

    /**
     * Test for delete
     * 
     * @depends testEdit
     * @param int $id
     * @return void
     */
    public function testDelete($id)
    {
        $result = FaqModel::where('id', '=', $id)->first();
        $this->assertIsObject($result);

        $response = $this->delete('/' . env('DATA_WORKSPACENAME') . '/faq/' . $id . '/delete');
        $response->assertStatus(302);
        
        $result = FaqModel::where('id', '=', $id)->first();
        $this->assertTrue($result === null);
    }
}
