<?php
// Include the database class file and create a new instance of the database connection
require_once('classes/database.php');
$con = new database();

// Initialize a variable to store SweetAlert configuration script
$sweetAlertConfig = "";

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Retrieve form data from the POST request
  $username = $_POST['username'];
  // Hash the password using BCRYPT for secure storage
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $firstname = $_POST['first_name'];
  $lastname = $_POST['last_name'];
  $email = $_POST['email'];

  // Call the signupUser method from the database class to register the user
  $userID = $con->signupUser($username, $password, $firstname, $lastname, $email);

  // Check if user registration was successful
  if ($userID) {
    // Configure SweetAlert to show a success message and redirect to login page
    $sweetAlertConfig = "
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'success',
          title: 'Registration Successful',
          text: 'You have successfully registered as an admin.',
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.href = 'login.php';
        });
      });
    </script>";
  } else {
    // Configure SweetAlert to show an error message if registration fails
    $sweetAlertConfig = "
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
          icon: 'error',
          title: 'Registration Failed',
          text: 'An error occurred during registration. Please try again.',
          confirmButtonText: 'OK'
        });
      });
    </script>";
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration</title>
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.css">
  <link rel="stylesheet" href="./package/dist/sweetalert2.css">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4 text-center">Admin Registration</h2>
    <form id="registrationForm" method="POST" action="" class="bg-white p-4 rounded shadow-sm">
      <div class="mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter your first name" required>
        <div class="invalid-feedback">First name is required.</div>
      </div>
      <div class="mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter your last name" required>
        <div class="invalid-feedback">Last name is required.</div>
      </div>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
        <div class="invalid-feedback">Username is required.</div>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" name="email" id="email" class="form-control" placeholder="Enter your email" required>
        <div class="invalid-feedback">Email is required.</div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        <div class="invalid-feedback">Password must be at least 6 characters long, include an uppercase letter, a number, and a special character.</div>      
      </div>
      <button type="submit" name='register' class="btn btn-primary w-100">Register</button>
      <div class="my-3 text-end">
        <span>Have an account?</span>
        <a href="login.php" class="btn btn-link p-0 align-baseline">Login</a>
      </div>
    </form>
  </div>
  
  <script src="./bootstrap-5.3.3-dist/js/bootstrap.js"></script>
  <script src="./package/dist/sweetalert2.js"></script>
  <?php echo $sweetAlertConfig; ?>

  <script>
    
  // Function to validate individual fields
  function validateField(field, validationFn) {
    field.addEventListener('input', () => {
      if (validationFn(field.value)) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
      }
    });
  }

  // Validation functions for each field
  const isNotEmpty = (value) => value.trim() !== '';
  const isPasswordValid = (value) => {
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
    return passwordRegex.test(value);
  };

  // Real-time username validation using AJAX
  const checkUsernameAvailability = (usernameField) => {
    usernameField.addEventListener('input', () => {
      const username = usernameField.value.trim();

      if (username === '') {
        usernameField.classList.remove('is-valid');
        usernameField.classList.add('is-invalid');
        usernameField.nextElementSibling.textContent = 'Username is required.';
        return;
      }

      // Send AJAX request to check username availability
      fetch('ajax/check_username.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${encodeURIComponent(username)}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.exists) {
            usernameField.classList.remove('is-valid');
            usernameField.classList.add('is-invalid');
            usernameField.nextElementSibling.textContent = 'Username is already taken.';
          } else {
            usernameField.classList.remove('is-invalid');
            usernameField.classList.add('is-valid');
            usernameField.nextElementSibling.textContent = '';
          }
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    });
  };

  // Real-time Email validation using AJAX
  const checkEmailAvailability = (emailField) => {
    emailField.addEventListener('input', () => {
      const email = emailField.value.trim();

      if (email === '') {
        emailField.classList.remove('is-valid');
        emailField.classList.add('is-invalid');
        emailField.nextElementSibling.textContent = 'Email is required.';
        return;
      }

      // Send AJAX request to check email availability
      fetch('ajax/check_email.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.exists) {
            emailField.classList.remove('is-valid');
            emailField.classList.add('is-invalid');
            emailField.nextElementSibling.textContent = 'Email is already taken.';
          } else {
            emailField.classList.remove('is-invalid');
            emailField.classList.add('is-valid');
            emailField.nextElementSibling.textContent = '';
          }
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    });
  };

  // Get form fields
  const firstName = document.getElementById('first_name');
  const lastName = document.getElementById('last_name');
  const username = document.getElementById('username');
  const password = document.getElementById('password');
  const email = document.getElementById('email');

  // Attach real-time validation to each field
  validateField(firstName, isNotEmpty);
  validateField(lastName, isNotEmpty);
  validateField(password, isPasswordValid);
  checkUsernameAvailability(username);
  checkEmailAvailability(email);

  // Form submission validation
  document.getElementById('registrationForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent form submission for validation

    let isValid = true;

    // Validate all fields on submit
    [firstName, lastName, username, email, password].forEach((field) => {
      if (!field.classList.contains('is-valid')) {
        field.classList.add('is-invalid');
        isValid = false;
      }
    });

    // If all fields are valid, submit the form
    if (isValid) {
      this.submit();
    }
  });
</script>


</body>
</html>