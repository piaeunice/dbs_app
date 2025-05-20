<?php

class database {

    function opencon() {
        return new PDO(
            'mysql:host=localhost;dbname=dbs_app',
            'root',
            ''
        );
    }

    function signupUser($username, $password, $firstname, $lastname, $email) {
        $con = $this->opencon();

        try {
            $con->beginTransaction();

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $con->prepare("INSERT INTO Admin (admin_FN, admin_LN, admin_username, admin_email, admin_password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $username, $email, $hashedPassword]);

            $userID = $con->lastInsertId();
            $con->commit();

            return $userID;
        } catch (PDOException $e) {
            $con->rollBack();
            return false;
        }
    }

    function isUsernameExists($username) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM Admin WHERE admin_username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    function isEmailExists($email) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM Admin WHERE admin_email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    function loginUser($username, $password) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM Admin WHERE admin_username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['admin_password'])) {
            return $user;
        } else {
            return false;
        }
    }
}
