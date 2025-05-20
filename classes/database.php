<?php

class database{

    function opencon(){
        return new PDO( 
        'mysql:host=localhost; dbname=dbs_app',   
        username: 'root', 
        password: '');
    }

    function signupUser($username, $password, $firstname, $lastname, $email){
        $con = $this->opencon();

        try{
            $con->beginTransaction();

            $stmt = $con->prepare("INSERT INTO Admin (admin_FN, admin_LN, admin_username, admin_email, admin_password) VALUES (?,?,?,?,?)");
            $stmt->execute([$firstname, $lastname, $username, $email, $password]);

            $userID = $con->lastInsertId();
            $con->commit();

            return $userID;   
        }catch (PDOException $e){
            $con->rollBack();
            return false;
        }

    }

    // Check if username exists
    function isUsernameExists($username){
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM Admin WHERE admin_username = ?");
        $stmt->execute([$username]);

        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    // Check if email exists
    function isEmailExists($email){
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM Admin WHERE admin_email = ?");
        $stmt->execute([$email]);

        $count = $stmt->fetchColumn();

        return $count > 0;
    }

}