<?php 

class SQLUserCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(WebGuy $I)
    {
        $I->wantTo('login user successfully');
        $I->sendPOST('/v1/users/login', json_encode(['email' => 'm@yahoo.com', 'password' => '123']));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Success');
    }
}
