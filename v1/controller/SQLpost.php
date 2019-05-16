<?php
error_reporting(E_ALL^E_NOTICE);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
date_default_timezone_set('Asia/Kuala_Lumpur');

class SQLpost
{
    private $img_ext = array("png", "jpeg", "jpg", "gif");
    private $video_ext = array("mp4", "mp4","avi","flv","mov","mpeg");
    private $audio_ext = array("mp3", "flac", "wav", "alac");

    protected $userInfo, $session, $container, $guestbook, $mysqli;
    protected $value = array();

    public function __construct($session, $userInfo, $guestbook)
    {
        $this->userInfo = $userInfo;
        $this->session = $session;
        $this->guestbook = $guestbook;
        $this->mysqli = $this->guestbook->getConn();
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->value[$name];
    }

    private function wrapMessage(){
        $message = strip_tags($_POST['message']);
        #break long strings
        $message = wordwrap($message, 50,"\n", true);
        //$message = nl2br($message);
        return $message;
    }

    public function getPosts(Request $request, Response $response){
        $sql_get_post = "SELECT * FROM guestbook ORDER BY id DESC";
        $result = $this->mysqli->query($sql_get_post);

        if ($result == true){
            while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                $data[] = $row;
                //echo json_encode($data);
            }
            return $response->withJson(200);
        }
        else{
            $data = "No posts found!";
            return $response->withJson($data, 400);
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
        $mysqli = $this->guestbook->getConn();
        $post_file = $mysqli->query($sql_add_image);
        //$this->session->flash('add_file', 'File successfully uploaded!');
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

    public function createPost(Request $request, Response $response){
        if ($this->session->get('logged_in') == true){
            //var_dump($this->session->get('first_name'));
            $name = $this->session->get('first_name');
            $email = $this->session->get('email');
            $post_entry = json_decode($request->getBody())->post_entry;
            $time = date("h:i A");
            $date = date("F d, Y");
            $image = null;
            $type = null;
            $sql_add_post = $this->mysqli->prepare("INSERT INTO guestbook 
                (name, email, message, image, type, time, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $sql_add_post->execute(array($name, $email, $post_entry, $image, $type, $time, $date));

            if ($result == true){
                $message = "Post created!";
                return $response->withJson($message, 201);
            }
            else{
                $message = "Error, post not created!";
                return $response->withJson($message, 400);
            }
        }
        else{
            $message = "Please log in to post to the guestbook";
            return $response->withJson($message, 403);
        }
    }

    public function deletePost(Request $request, Response $response){
        $id = $request->getAttribute('id');
        if ($this->session->get('logged_in') == true){
            if ($this->guestbook->getPostInfoByID($id) == null){
                $data = "Post not found!";
                return $response->withJson($data, 404);
            }
            else{
                $sql_delete_post = $this->mysqli->prepare("DELETE FROM guestbook WHERE id = ?");
                $result = $sql_delete_post->execute(array($id));

                if ($result == true){
                    $data = "Post deleted!";
                    return $response->withJson($data, 200);
                }
                else{
                    $data = "Error, post not deleted!";
                    return $response->withJson($data, 400);
                }
            }
        }
        else{
            $data = "Please log in to delete from the guestbook";
            return $response->withJson($data, 403);
        }

    }

    public function editPost(Request $request, Response $response){
        $id = $request->getAttribute('id');
        $post_entry = json_decode($request->getBody())->post_entry;

        if ($this->session->get('logged_in') == true){
            if ($this->guestbook->getPostInfoByID($id) == null){
                $data = "Post not found!";
                return $response->withJson($data, 404);
            }
            else{
                $sql_edit_post = $this->mysqli->prepare("UPDATE guestbook SET message = ? WHERE id = ?");
                $result = $sql_edit_post->execute(array($post_entry, $id));

                if ($result == true){
                    $message = "Post edited!";
                    return $response->withJson($message, 200);
                }
                else{
                    $message = "Error, post not edited!";
                    return $response->withJson($message, 400);
                }
            }
        }
        else{
            $data = "Please log in to edit posts from the guestbook";
            return $response->withJson($data, 403);
        }
    }
}