<?php

class User
{
    public function getConn()
    {
        return $conn = new PDO('mysql:host=localhost;dbname=guestbook;charset=utf8mb4', 'root', 'root');
    }

    public function getUserFName($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $first_name = $userInfo['first_name'];
        return $first_name;
    }

    public function getUserLName($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $last_name = $userInfo['last_name'];

        return $last_name;
    }

    public function getUserFullName($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $full_name = $userInfo['first_name'] . ' ' . $userInfo['last_name'];

        return $full_name;
    }

    public function getUserEmail($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $email = $userInfo['email'];

        return $email;
    }

    public function getEmailVariables($userID){
        return[
          'full_name' => $this->getUserFullName($userID),
            'email' => $this->getUserEmail($userID)
        ];
    }

    public function getInfoAssoc($email){
        $stmt = $this->getUserInfoByEmail($email);
        $result_assoc = $stmt->fetch(PDO::FETCH_ASSOC);     #returns array

        return $result_assoc;
    }

    public function getUserInfoByID($userID){
        $conn = $this->getConn();
        $sql_get_info = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $sql_get_info->execute(array($userID));     #return PDOStatement
        $getUserInfo =  $sql_get_info->fetch(PDO::FETCH_ASSOC);     #returns array

        return $getUserInfo;
    }

    public function getUserInfoByEmail($email){
        $conn = $this->getConn();
        $sql_get_info = $conn->prepare("SELECT * FROM users WHERE email= ?");
        $sql_get_info->execute(array($email));      #return PDOStatement

        return $sql_get_info;
    }
}