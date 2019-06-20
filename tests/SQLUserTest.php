<?php
error_reporting(E_ALL^E_NOTICE);
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use utility\Session;

require_once "v1/controller/SQLpost.php";
require_once "v1/controller/SQLuser.php";
require_once "v1/library/Session.php";
require_once "v1/model/User.php";
require_once "v1/model/Guestbook.php";

class SQLUserTest extends TestCase
{
    protected $sql_post, $session, $user, $app, $sql_user, $guestbook;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->session = new Session();
        $this->user = new User();
        //$this->guestbook = new Guestbook();
        //$this->sql_post = new SQLpost($this->session, $this->user, $this->guestbook);
        $this->sql_user = new SQLuser($this->session, $this->user);
    }

    private function requestHTTP($method, $url, $request_data){
        $request = Request::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI'    => $url,
        ]));
        if ($method == 'POST'){
            $request->getBody()->write(json_encode($request_data));
            return $request;
        }
        elseif ($method == 'DELETE'){
            return $request;
        }
    }

    private function responseHTTP(){
        return $response = new Response();
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testLoginIsSuccessful(){
        $request_data = array(
            "email" => "m@yahoo.com",
            "password" => "123"
        );
        $request = $this->requestHTTP('POST', '/v1/users/login', $request_data);
        $response = $this->responseHTTP();
        $result = $this->sql_user->loginUser($request, $response);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testLoginIsNotSuccessful_UserNotExist(){
        $request_data = array(
            "email" => "ma@yahoo.com",
            "password" => "123"
        );
        $request = $this->requestHTTP('POST', '/v1/user/login', $request_data);
        $response = $this->responseHTTP();
        $result = $this->sql_user->loginUser($request, $response);
        $this->assertEquals(404, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testLoginIsNotSuccessful_WrongPassword(){
        $request_data = array(
            "email" => "m@yahoo.com",
            "password" => "1234"
        );
        $request = $this->requestHTTP('POST', '/v1/users/login', $request_data);
        $response = $this->responseHTTP();
        $result = $this->sql_user->loginUser($request, $response);
        $this->assertEquals(500, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function RegisterUserIsSuccessful(){
        $request_data = array(
            "first_name" => "phpunit",
            "last_name" => "test",
            "email" => "phpunit3@yahoo.com",
            "password" => "1234"
        );
        $request = $this->requestHTTP('POST', '/v1/users/register', $request_data);
        $response = $this->responseHTTP();
        $result = $this->sql_user->registerUser($request, $response);
        $this->assertEquals(201, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testRegisterUserIsNotSuccessful_UserExist(){
        $request_data = array(
            "first_name" => "phpunit",
            "last_name" => "test",
            "email" => "phpunit@yahoo.com",
            "password" => "1234"
        );
        $request = $this->requestHTTP('POST', '/v1/users/register', $request_data);
        $response = $this->responseHTTP();
        $result = $this->sql_user->registerUser($request, $response);
        $this->assertEquals(501, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testRegisterUserIsNotSuccessful_InvalidEmail(){
        $request_data = array(
            "first_name" => "phpunit",
            "last_name" => "test",
            "email" => "phpunit@yahoo",
            "password" => "1234"
        );
        $request = $this->requestHTTP('POST', '/v1/users/register', $request_data);
        $response = $this->responseHTTP();
        $result = $this->sql_user->registerUser($request, $response);
        $this->assertEquals(400, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     * supposed to fail
     */
    public function RegisterUserIsNotSuccessful_Error(){
        $request_data = array(
            "first_name" => "phpunit",
            "last_name" => "test",
            "email" => "phpunit@yahoo.com",
            "password" => "1234"
        );
        $request = $this->requestHTTP('POST', '/v1/users/register', $request_data);
        $response = $this->responseHTTP();
        $result = $this->sql_user->registerUser($request, $response);
        $this->assertEquals(500, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function DeleteUserIsSuccessful(){
        $request_data = array(
            "email" => "m@yahoo.com",
            "password" => "123"
        );
        $request = $this->requestHTTP('POST', '/v1/user/login', $request_data);
        $response = $this->responseHTTP();
        $this->sql_user->loginUser($request, $response);
        $request = $this->requestHTTP('DELETE', '/v1/users/delete/{id}', $request_data = null);
        $request = $request->withAttribute('id', 17);
        $response = $this->responseHTTP();
        $result = $this->sql_user->deleteUser($request, $response);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testDeleteUserIsNotSuccessful_UserNotFound(){
        $request_data = array(
            "email" => "m@yahoo.com",
            "password" => "123"
        );
        $request = $this->requestHTTP('POST', '/v1/user/login', $request_data);
        $response = $this->responseHTTP();
        $this->sql_user->loginUser($request, $response);
        $request = $this->requestHTTP('DELETE', '/v1/users/delete/{id}', $request_data = null);
        $request = $request->withAttribute('id', 16);
        $response = $this->responseHTTP();
        $result = $this->sql_user->deleteUser($request, $response);
        $this->assertEquals(404, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testDeleteUserIsNotSuccessful_PleaseLogIn(){
        $request = $this->requestHTTP('DELETE', '/v1/users/delete/{id}', $request_data = null);
        $request = $request->withAttribute('id', 16);
        $response = $this->responseHTTP();
        $result = $this->sql_user->deleteUser($request, $response);
        $this->assertEquals(403, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     * supposed to fail
     */
    public function DeleteUserIsNotSuccessful_Error(){
        $request = $this->requestHTTP('DELETE', '/v1/users/delete/15', $request_data = null);
        $response = $this->responseHTTP();
        $result = $this->sql_user->deleteUser($request, $response);
        $this->assertEquals(400, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     */
    public function testLogoutUserIsSuccessful(){
        $request = $this->requestHTTP('POST', '/v1/users/logout', $request_data = null);
        $response = $this->responseHTTP();
        $result = $this->sql_user->logoutUser($request, $response);
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @backupGlobals disabled
     * @runInSeparateProcess
     * supposed to fail
     */
    public function LogoutUserIsNotSuccessful_Error(){
        $request = $this->requestHTTP('POST', '/v1/users/logout', $request_data = null);
        $response = $this->responseHTTP();
        $result = $this->sql_user->logoutUser($request, $response);
        $this->assertEquals(300, $result->getStatusCode());
    }
}