<?php



class SQLUserCest
{
    protected $session, $user, $token = [];

    public function _before(ApiTester $I)
    {
        //$this->client = new GuzzleHttp\Client(['base_uri' => 'http://slim.io']);
        //$this->jar = new \GuzzleHttp\Cookie\CookieJar();
    }

    // tests

    /**
     * @param ApiTester $I
     * @throws Exception
     */
    public function LoginUserViaAPI(ApiTester $I){
        $I->wantTo('login user successfully');
        $I->sendPOST('/v1/users/login', json_encode(['email' => 'm@yahoo.com', 'password' => '123']));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('Success');
    }

    public function LoginUserNotFoundViaAPI(ApiTester $I){
        $I->wantTo('login user with wrong email');
        $I->sendPOST('/v1/users/login', json_encode(['email' => 'ma@yahoo.com', 'password' => '123']));
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * @param ApiTester $I
     * @before LoginUserViaAPI
     * @throws Exception
     */
    public function DeleteUserViaAPI(ApiTester $I){
        //$session_login = $I->grabDataFromResponseByJsonPath('$.session.logged_in');
        $I->wantTo('delete user successfully');
        //$I->amBearerAuthenticated($this->token[0]);
        //$I->amLoggedInAs('m@yahoo.com');
        $I->sendDELETE('/v1/users/delete/22');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('deleted');
    }

    public function DeleteUserPleaseLogInViaAPI(ApiTester $I){
        $I->wantTo('delete user - prompt login message');
        $I->sendDELETE('/v1/users/delete/22');
        $I->seeResponseCodeIs(403);
        $I->seeResponseIsJson();
        $I->seeResponseContains('log in');
    }

//    public function RegisterUserViaAPI(ApiTester $I){
//        $I->wantTo('register user');
//        $I->sendPOST('/v1/users/register', json_encode(['first_name' => 'codeception', 'last_name' => 'api testing',
//            'email' => 'codecept3@yahoo.com', 'password' => '123']));
//        $I->seeResponseCodeIs(201);
//        $I->seeResponseIsJson();
//    }
}
