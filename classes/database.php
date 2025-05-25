<?php

class database {

    // Function to open a PDO connection to the MySQL database
    function opencon() {
        return new PDO(
            'mysql:host=localhost;dbname=dbs_app',
            'root',
            ''
        );
    }

    // Function to handle user signup and save data into the Admin table
    function signupUser($username, $password, $firstname, $lastname, $email) {
        $con = $this->opencon();

        try {
            // Begin database transaction
            $con->beginTransaction(); 

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and execute SQL insert query
            $stmt = $con->prepare("INSERT INTO Admin (admin_FN, admin_LN, admin_username, admin_email, admin_password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$firstname, $lastname, $username, $email, $hashedPassword]);

            // Get the ID of the newly inserted user
            $userID = $con->lastInsertId();

            // Commit transaction
            $con->commit();

            // Return new user ID if successful
            return $userID;

        } catch (PDOException $e) {
            // Roll back the transaction if an error occurs
            $con->rollBack();
            return false;
        }
    }

    // Function to check if a username already exists in the Admin table
    function isUsernameExists($username) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM Admin WHERE admin_username = ?");
        $stmt->execute([$username]);

        // Return true if count is greater than 0, indicating the username exists
        return $stmt->fetchColumn() > 0;
    }

    // Function to check if an email already exists in the Admin table
    function isEmailExists($email) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT COUNT(*) FROM Admin WHERE admin_email = ?");
        $stmt->execute([$email]);

        // Return true if count is greater than 0, indicating the username exists
        return $stmt->fetchColumn() > 0;
    }

    // Function to handle login by verifying the username and password
    function loginUser($username, $password) {
        $con = $this->opencon();
        $stmt = $con->prepare("SELECT * FROM Admin WHERE admin_username = ?");
        $stmt->execute([$username]);
        // Fetch user data as an associative array
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user exists and password matches, return user data; otherwise return false
        if ($user && password_verify($password, $user['admin_password'])) {
            return $user;
        } else {
            return false;
        }
    }
}
