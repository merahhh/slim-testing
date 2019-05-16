<?php
error_reporting(E_ALL^E_NOTICE);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SQLuser
{
    protected $userInfo, $session, $mysqli;
    protected $value = array();

    public function __construct($session, $userInfo)
    {
        //parent::__construct($container);
        $this->userInfo = $userInfo;
        $this->session = $session;
        $this->mysqli = $this->userInfo->getConn();
        $this->session->set('logged_in', false);
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->value[$name];
    }

    public function registerUser(Request $request, Response $response){
        $first_name = json_decode($request->getBody())->first_name;
        $last_name = json_decode($request->getBody())->last_name;
        $email = json_decode($request->getBody())->email;
        $password = json_decode($request->getBody())->password;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false){
            $sql_get_info = $this->mysqli->prepare("SELECT * FROM users WHERE email = ?");
            $sql_get_info->execute(array($email));
            $result = $sql_get_info->fetch(PDO::FETCH_ASSOC);

            #we know user email exists if the rows returned are > 0
            if ($result != null){
                $message = "User with that email exists";
                return $response->withJson($message, 501);
            }
            else{   #email doesn't already exist in DB, proceed
                $password = password_hash($password, PASSWORD_BCRYPT);
                $hash = md5(rand(0,1000));

                $sql_register = $this->mysqli->prepare("INSERT INTO users (first_name, last_name, email, password, hash) 
                    VALUES (?, ?, ?, ?, ?)");
                //'$first_name', '$last_name', '$email', '$password', '$hash'
                $result = $sql_register->execute(array($first_name, $last_name, $email, $password, $hash));
                if($result == true){
                    $this->session->set('active', 1);
                    $message = "User account created successfully";
                    return $response->withJson($message, 201);
                }
                else{
                    $message = "User not registered";
                    return $response->withJson($message, 500);
                }
            }
        }
        else{
            $message = "Invalid email";
            return $response->withJson($message, 400);
        }
    }

    public function loginUser(Request $request, Response $response){
        try{
            $email = json_decode($request->getBody())->email;
            $user = $this->userInfo->getInfoAssoc($email);

            if ($user == null){
                $data = 'User does not exist';
                return $response->withJson($data, 404);
            }
            else{
                if (password_verify(json_decode($request->getBody())->password, $user['password'])){    #if password is correct
                    $this->session->set('id', $user['id']);
                    $this->session->set('email', $user['email']);
                    $this->session->set('first_name', $user['first_name']);
                    $this->session->set('last_name', $user['last_name']);
                    $this->session->set('active', $user['active']);

                    #this is how we'll know the user is logged in
                    $this->session->set('logged_in', true);
                    $data = 'Successfully logged in';
                    return $response->withJson($data, 200);
                }
                else{    #if password is incorrect
                    $data = 'Incorrect password';
                    return $response->withJson($data, 500);
                }
            }
        }
        catch (exception $e){
            $data = 'Oops, something went wrong!';
            return $response->withJson($data, 300);
        }
    }

    public function logoutUser(Request $request, Response $response){
        # Initialize the session.
        # If you are using session_name("something"), don't forget it now!
        session_start();

        # Unset all of the session variables.
        $_SESSION = array();

        # If it's desired to kill the session, also delete the session cookie.
        # Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        # Finally, destroy the session.
        session_destroy();
        $this->session->set('logged_in', false);
        //$this->session->destroySession();
        $message = 'Successfully logged out!';
        return $response->withJson($message, 200);
    }

    public function deleteUser(Request $request, Response $response){
        $id = $request->getAttribute('id');
        if ($this->session->get('logged_in') == true){
            if ($this->userInfo->getUserInfoByID($id) == null){//->num_rows == 0
                $data = "User not found!";
                return $response->withJson($data, 404);
            }
            else{
                $sql_delete_user = $this->mysqli->prepare("DELETE FROM users WHERE id = ?");
                $query_delete_user = $sql_delete_user->execute(array($id));

                if ($query_delete_user == true){
                    $data = "User deleted!";
                    return $response->withJson($data, 200);
                }
                else{
                    $data = "User not deleted!";
                    return $response->withJson($data, 400);
                }
            }
        }
        else{
            $data = "Please log in to delete account";
            return $response->withJson($data, 403);
        }
    }
}