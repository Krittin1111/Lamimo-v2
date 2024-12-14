<?php
session_start();

// Include the database connection
include('connect.php');  // Assuming connect.php uses MySQLi

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format. Please try again.'); window.history.back();</script>";
        exit;
    }

    // Check if email and password are not empty
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in both email and password.'); window.history.back();</script>";
        exit;
    }

    // Query the database for the user by email
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    $stmt->bind_param('s', $email); // 's' is for string type

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify the password using password_verify() (for hashed passwords)
        if (password_verify($password, $user['password'])) {
            // If password is correct, start the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];

            // Redirect to the home page or dashboard after successful login
            header("Location: home.html");  // Change home.php to your desired landing page
            exit;
        } else {
            echo "<script>alert('Invalid email or password. Please try again.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Invalid email or password. Please try again.'); window.history.back();</script>";
        exit;
    }
}
?>
