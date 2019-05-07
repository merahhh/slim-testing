<?php
require_once 'ApiTestCase.php';

class SQLTest extends ApiTestCase
{
    public function test_if_successful(){
        /*$request_data = array(
            "email" => "m@yahoo.com",
            "password" => "123"
        );*/
        $this->request('POST', '/v1/users/login', ['email' => 'm@yahoo.com', 'password' => '123']);
        $this->assertThatResponseHasStatus(200);
    }
}