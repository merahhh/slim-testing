<?php
error_reporting(E_ALL^E_NOTICE);

use Psr\Container\ContainerInterface as ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use utility\Session;
require_once "model/User.php";
require_once "library/Session.php";

/*class AppController
{
    protected $userInfo, $session;
    protected $container, $value = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->value[$name];
    }
}*/

class SQLpost
{
    private $img_ext = array("png", "jpeg", "jpg", "gif");
    private $video_ext = array("mp4", "mp4","avi","flv","mov","mpeg");
    private $audio_ext = array("mp3", "flac", "wav", "alac");

    protected $userInfo, $session, $container;
    protected $value = array();

    public function __construct($session)
    {
        //parent::__construct($container);
        //$this->container = $container;
        $this->userInfo = new User();
        $this->session = $session;
        //$this->session = $container->get('Session');
        //$this->session = $this->container->get('User');

    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->value[$name];
    }

    /*public function __invoke(User $userInfo, Session $session)
    {
        //$this->userInfo = $userInfo;
        //$this->session = $session;
    }*/

    private function wrapMessage(){
        $message = strip_tags($_POST['message']);
        #break long strings
        $message = wordwrap($message, 50,"\n", true);
        //$message = nl2br($message);
        return $message;
    }

    public function getPosts(Request $request, Response $response){
        $mysqli = $this->userInfo->getConn();

        $sql_get_post = "SELECT * FROM guestbook ORDER BY id DESC";
        $result = $mysqli->query($sql_get_post);

        if ($result == true){
            while ($row = $result->fetch_assoc()){
                $data[] = $row;
                echo json_encode($data);
                return $response->withJson(200);

            }
        }
        else{
            $message = "No posts found!";
            return $response->withJson($message, 400);
        }
    }

    public function createPost(Request $request, Response $response){
        //$userInfo = $this->container['get_user'];
        //$session = $this->container['session'];
        $mysqli = $this->userInfo->getConn();

        if ($this->session->check('logged_in') == true){
            $name = $this->session->get('first_name');  //"slimTEST"; //check
            $email = $this->session->get('email');    //"slim@yahoo.com";
            $post_entry = json_decode($request->getBody())->post_entry;
            $time = date("h:i A");
            $date = date("F d, Y");
            $image = null;
            $type = null;
            $sql_add_post = "INSERT INTO guestbook (name, email, message, image, type, time, date) VALUES 
                          ('$name', '$email', '$post_entry', '$image', '$type', '$time', '$date')";
            $result = $mysqli->query($sql_add_post);

            if ($result == true){
                $message = "Post created";
                return $response->withJson($message, 201);
            }
            else{
                $message = "Post not created";
                return $response->withJson($message, 400);
            }
        }
        else{
            $message = "Please log in to post to the guestbook";
            return $response->withJson($message, 500);
        }
    }

    public function postFile($file_name, $sql_type){
        $name = $this->userInfo->getUserFName($this->session->get('id'));
        $email = $this->userInfo->getUserEmail($this->session->get('id'));
        $message = $this->wrapMessage();
        $time = date("h:i A");
        $date = date("F d, Y");
        $sql_add_image = "INSERT into guestbook (name, email, message, image, type, time, date) VALUES 
                        ('$name', '$email', '$message', '" . $file_name . "', '$sql_type', '$time', '$date')";
        $conn = $this->userInfo->getConn();
        mysqli_query($conn, $sql_add_image);
        $this->session->flash('add_file', 'File successfully uploaded!');
    }

    public function deletePost(Request $request, Response $response){
        $userInfo = $this->container['get_user'];
        $session = $this->container['session'];
        $mysqli = $userInfo->getConn();

        $id = $request->getAttribute('id');
        $sql_delete_post = "DELETE FROM guestbook WHERE id = $id";
        $result = $mysqli->query($sql_delete_post);

        if ($result == true){
            $message = "Post deleted!";
            return $response->withJson($message, 200);
        }
        else{
            $message = "Error, post not deleted!";
            return $response->withJson($message, 400);
        }
    }

    public function checkExtension($file_type, $file_name){
        if (in_array($file_type, $this->img_ext)){    #if file is an image
            $sql_type = "image";
            $this->postFile($file_name, $sql_type);
        }
        elseif (in_array($file_type, $this->video_ext)) {     #if file is a video
            $sql_type = "video";
            $this->postFile($file_name, $sql_type);
        }
        elseif (in_array($file_type, $this->audio_ext)){      #if file is audio
            $sql_type = "audio";
            $this->postFile($file_name, $sql_type);
        }
        else{       #if file has other unsupported extensions
            $this->session->flash('add_file_fail_ext', 'File not supported, try again!');
        }
    }

    public function editPost(Request $request, Response $response){
        $mysqli = $this->userInfo->getConn();

        $id = $request->getAttribute('id');
        $post_entry = json_decode($request->getBody())->post_entry;
        $sql_edit_post = "UPDATE guestbook SET message = '$post_entry' WHERE id = $id";
        $result = $mysqli->query($sql_edit_post);

        if ($result == true){
            $message = "Post edited!";
            return $response->withJson($message, 200);
        }
        else{
            $message = "Error, post not edited!";
            return $response->withJson($message, 400);
        }
    }

    public function registerUser(Request $request, Response $response){
        $mysqli = $this->userInfo->getConn();

        #escape all $_POST variables to protect against SQL injections
        $first_name = $this->userInfo->getPostObject(json_decode($request->getBody())->first_name);
        $last_name = $this->userInfo->getPostObject(json_decode($request->getBody())->last_name);
        $email = $this->userInfo->getPostObject(json_decode($request->getBody())->email);
        $password = $this->userInfo->getPostObject(json_decode($request->getBody())->password);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false){
            $sql_get_info = "SELECT * FROM users WHERE email='$email'";
            $result = $mysqli->query($sql_get_info);

            #we know user email exists if the rows returned are > 0
            if ($result->num_rows > 0){
                $message = "User with that email exists";
            }
            else{   #email doesn't already exist in DB, proceed
                $password = password_hash($password, PASSWORD_BCRYPT);
                $hash = md5(rand(0,1000));

                $sql = "INSERT INTO users (first_name, last_name, email, password, hash) " .
                    "VALUES ('$first_name', '$last_name', '$email', '$password', '$hash')";
                $result = $mysqli->query($sql);
                if($result == true){
                    $this->session->set('active', 1);
                    $message = "User registered successfully";
                    return $response->withJson($message, 201);
                }
                else{
                    $message = "Error, user not registered";
                    return $response->withJson($message, 400);
                }
            }
        }
        else{
            $message = "Error: Invalid email";
            return $response->withJson($message, 500);
        }
        echo json_encode($message);
    }

    public function loginUser(Request $request, Response $response){
        //$this->session = new Session();
        //$userInfo = $this->container->get('user');
        //$this->session = $this->container['Session'];
        try{
            $email = json_decode($request->getBody())->email;
            $user = $this->userInfo->getInfoAssoc($email);

            if ($this->userInfo->getUserInfoByEmail($email)->num_rows == 0){
                $data['error'] = 'User does not exist';
                return $response->withJson($data, 400);
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
                    $data['success'] = 'Successfully logged in';
                    //$response->withStatus(200, $message);
                    //var_dump($request->getBody());
                    return $response->withJson($data, 201);
                }
                else{    #if password is incorrect
                    $data['error'] = 'Incorrect password';
                    return $response->withJson($data, 500);
                }
            }
        }
        catch (exception $e){
            $data['error'] = 'Oops, something went wrong!';
            return $response->withJson($data, 300);
        }
    }

    public function logoutUser(Request $request, Response $response){
        // Initialize the session.
        // If you are using session_name("something"), don't forget it now!
        session_start();

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        //$this->session->destroySession();
        $message = 'Successfully logged out!';
        return $response->withJson($message, 200);
    }

    public function deleteUser(Request $request, Response $response){
        $mysqli = $this->userInfo->getConn();

        $id = $request->getAttribute('id');
        $sql_get_email = "SELECT email FROM users WHERE id = $id";
        $email = implode("", $mysqli->query($sql_get_email)->fetch_assoc());
        $sql_delete_user = "DELETE FROM users WHERE id = $id";
        $result = $mysqli->query($sql_delete_user);

        if ($result == true){
            $message = "User with email: $email deleted!";
            return $response->withJson($message, 200);
        }
        else{
            $message = "Error: User not deleted!";
            return $response->withJson($message, 400);
        }
    }
}