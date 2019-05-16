<?php

class Guestbook
{
    public function getConn()
    {
        $conn = new PDO('mysql:host=localhost;dbname=guestbook;charset=utf8mb4', 'root', 'root');
        return $conn;
    }

    public function getPostInfoByID($postID){
        $conn = $this->getConn();
        $sql_get_post = $conn->prepare("SELECT * FROM guestbook WHERE id = ?");
        $sql_get_post->execute(array($postID));
        $get_post = $sql_get_post->fetch(PDO::FETCH_ASSOC);

        return $get_post;
    }
}