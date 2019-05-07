<?php

class User
{
    public function getConn()
    {
        $conn = new mysqli("localhost", "root", "root", "guestbook");
        return $conn;
    }

    public function getUserFName($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $first_name = trim($userInfo->first_name);
        return $first_name;
    }

    public function getUserLName($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $last_name = trim($userInfo->last_name);

        return $last_name;
    }

    public function getUserFullName($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $full_name = $userInfo->first_name . ' ' . $userInfo->last_name;

        return $full_name;
    }

    public function getUserEmail($userID){
        $userInfo = $this->getUserInfoByID($userID);
        $email = $userInfo->email;

        return $email;
    }

    public function getEmailVariables($userID){
        return[
          'full_name' => $this->getUserFullName($userID),
            'email' => $this->getUserEmail($userID)
        ];
    }

    public function getUserInfoByID($userID){
        $connAcc = $this->getConn();
        $sql_get_info = "SELECT first_name, last_name, email FROM users WHERE id = $userID";
        $userInfo = $connAcc->query($sql_get_info);
        $getUserInfo =  $userInfo->fetch_object();      #return stdClass object

        return $getUserInfo;
    }

    /**
     * escape email to protect against SQL injections
     * @param $post
     * @return string
     */
    public function getPostObject($post){
        $conn = $this->getConn();
        $object = $conn->escape_string($post);

        return $object;
    }

    public function getUserInfoByEmail($post){
        $email = $this->getPostObject($post);
        $conn = $this->getConn();
        $sql_get_info = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql_get_info);      #return mysqli_result object

        return $result;
    }

    public function getInfoAssoc($post){
        $email = $this->getPostObject($post);
        $result_assoc = mysqli_fetch_assoc($this->getUserInfoByEmail($email));

        return $result_assoc;
    }
}