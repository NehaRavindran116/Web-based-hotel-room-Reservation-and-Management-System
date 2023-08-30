<?php
session_start();

// Check if the user is already logged in
if(isset($_SESSION['user_id'])) {
    // User is already logged in, redirect to booking page
    header("Location: reservation.php");
    exit();
}

// Check if the login form is submitted
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the login credentials
    if(validateLogin($email, $password)) {
        // Login successful, set session variables
        $_SESSION['user_id'] = getUserId($email);
        header("Location: reservation.php");
        exit();
    } else {
        $loginError = "Invalid email or password";
    }
}

// Check if the signup form is submitted
if(isset($_POST['signup'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the signup credentials
    if(validateSignup($email, $password)) {
        // Signup successful, create a new user
        createUser($email, $password);
        $_SESSION['user_id'] = getUserId($email);
        header("Location: reservation.php");
        exit();
    } else {
        $signupError = "Invalid email or password";
    }
}

// Check if the forgot password form is submitted
if(isset($_POST['forgot'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    if(emailExists($email)) {
        // Send password reset instructions to the user's email
        sendPasswordResetEmail($email);
        $forgotSuccess = "Password reset instructions sent to your email";
    } else {
        $forgotError = "Email not found";
    }
}

// Function to validate login credentials
function validateLogin($email, $password) {
    // Implement your own validation logic here
    // Check if the email and password match the database records
    $conn = new mysqli("localhost", "username", "password", "hotel");
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);
    $query = "SELECT * FROM login WHERE usname='$email' AND pass='$password'";
    $result = $conn->query($query);
    $conn->close();
    return $result->num_rows > 0;
}

// Function to validate signup credentials
function validateSignup($email, $password) {
    // Implement your own validation logic here
    // Check if the email is not already registered
    $conn = new mysqli("localhost", "username", "password", "hotel");
    $email = $conn->real_escape_string($email);
    $query = "SELECT * FROM login WHERE usname='$email'";
    $result = $conn->query($query);
    $conn->close();
    return $result->num_rows == 0;
}

// Function to create a new user
function createUser($email, $password) {
    // Implement your own logic to create a new user in the database
    $conn = new mysqli("localhost", "username", "password", "hotel");
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);
    $query = "INSERT INTO login (usname, pass) VALUES ('$email', '$password')";
    $conn->query($query);
    $conn->close();
}

// Function to get the user ID from the database
function getUserId($email) {
    // Implement your own logic to retrieve the user ID from the database
    $conn = new mysqli("localhost", "username", "password", "hotel");
    $email = $conn->real_escape_string($email);
    $query = "SELECT id FROM login WHERE usname='$email'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $conn->close();
    return $row['id'];
}

// Function to check if the email exists in the database
function emailExists($email) {
    // Implement your own logic to check if the email exists in the database
    $conn = new mysqli("localhost", "username", "password", "hotel");
    $email = $conn->real_escape_string($email);
    $query = "SELECT * FROM login WHERE usname='$email'";
    $result = $conn->query($query);
    $conn->close();
    return $result->num_rows > 0;
}

// Function to send password reset instructions to the user's email
function sendPasswordResetEmail($email) {
    // Implement your own logic to send the password reset instructions
    // This can include generating a unique token and sending it to the user's email
    // You can use PHP's built-in mail function or a third-party library to send the email
    // Example code:
    $resetToken = generateResetToken();
    $resetLink = "http://example.com/reset-password.php?token=$resetToken";
    $message = "Click the following link to reset your password: $resetLink";
    mail($email, "Password Reset Instructions", $message);
}

// Function to generate a unique reset token
function generateResetToken() {
    // Implement your own logic to generate a unique reset token
    // This can be a random string or a hashed value
    // Example code:
    $token = bin2hex(random_bytes(32));
    return $token;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if(isset($loginError)) { echo "<p>$loginError</p>"; } ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="login" value="Login">
    </form>

    <h1>Signup</h1>
    <?php if(isset($signupError)) { echo "<p>$signupError</p>"; } ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" name="signup" value="Signup">
    </form>

    <h1>Forgot Password</h1>
    <?php if(isset($forgotSuccess)) { echo "<p>$forgotSuccess</p>"; } ?>
    <?php if(isset($forgotError)) { echo "<p>$forgotError</p>"; } ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="submit" name="forgot" value="Reset Password">
    </form>
</body>
</html>
