<?php

use Codeception\Stub;
use Codeception\Test\Unit;
require_once "v1/model/User.php";
require_once "v1/model/Guestbook.php";

class ModelTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester, $user, $guestbook;
    
    protected function _before()
    {
        $this->user = new User();
        $this->guestbook = new Guestbook();
    }

    protected function _after()
    {
    }

    /*----------------------------- tests -------------------------------*/
    public function testFirstName(){
        $this->assertEquals($this->user->getUserFName(11), 'M');
    }

    public function testFirstNameStub(){
        $user = Stub::make('User', ['getUserFName' => 'john']);
        $name = $user->getUserFName(11);
        $this->assertEquals($name, 'john');
    }

    public function testLastName(){
        $this->assertEquals($this->user->getUserLName(11), 'S');
    }

    public function testFullName(){
        $this->assertEquals($this->user->getUserFullName(11), 'M S');
    }

    public function testEmail(){
        $this->assertEquals($this->user->getUserEmail(11), 'm@yahoo.com');
    }

    public function testGetEmailVariables(){
        $email_var = $this->user->getEmailVariables(11);
        $this->assertArrayHasKey('full_name', $email_var);
        $this->assertArrayHasKey('email', $email_var);
        $this->assertEquals($email_var['full_name'], 'M S');
        $this->assertEquals($email_var['email'], 'm@yahoo.com');
    }

    public function testGetUserInfoByID(){
        $id = 11;
        $this->assertIsArray($this->user->getUserInfoByID($id));
        //$this->assertInstanceOf(stdClass::class, $this->user->getUserInfoByID($id));
    }

    public function testGetUserInfoByEmail(){
        $email = $this->user->getUserEmail(11);
        //$this->assertInstanceOf(stdClass::class, $this->user->getUserInfoByEmail($email));
        $this->assertInstanceOf(PDOStatement::class, $this->user->getUserInfoByEmail($email));
    }

    public function testGetInfoAssoc(){
        $email = $this->user->getUserEmail(11);
        //$this->assertInstanceOf(PDOStatement::class, $this->user->getInfoAssoc($email));
        $this->assertIsArray($this->user->getInfoAssoc($email));
    }

    /*---------------------------- Guestbook.php ----------------------------------*/

    public function testGetPostInfoByID(){
        $id = 359;
        //$this->assertInstanceOf(stdClass::class, $this->guestbook->getPostInfoByID($id));
        $this->assertIsArray($this->guestbook->getPostInfoByID($id));
    }
}