<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

   /**
    * Define custom actions here
    */

   public function amLoggedInAs($email){
       $I = $this;
       $I->wantTo('login into api');
       $I->amGoingTo('try to log in using email and password');
       $I->sendPOST('/v1/users/login', json_encode(['email' => $email, 'password' => '123']));
   }
}
