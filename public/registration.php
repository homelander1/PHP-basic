<?php

function registrationForm($data)
{
    // Database connection setup
    $dbserver = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dynamicweb";

    $conn = mysqli_connect($dbserver, $username, $password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $first_name = trim($data["first_name"] ?? '');
    $last_name = trim($data["last_name"] ?? '');
    $email = trim($data["email"] ?? '');
    $password = $data["password"] ?? '';
    $confirm_password = $data["confirm_password"] ?? '';

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, registration_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $email, $hashed_password, $first_name, $last_name);

    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    $user_id = $stmt->insert_id;

    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['logged_in'] = true;
    header("Location: user.php");
    exit();

    $stmt->close();
    $conn->close();
}

// Logging into account
function loginUser($data)
{
    $dbserver = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dynamicweb";

    $conn = mysqli_connect($dbserver, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $email = trim($data["login"] ?? '');
    $password = $data["password"] ?? '';

    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        die("No user found with this email.");
    }

    $stmt->bind_result($user_id, $first_name, $last_name, $hashed_password);
    $stmt->fetch();

    if (!password_verify($password, $hashed_password)) {
        die("Invalid password.");
    }
    session_start();
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['logged_in'] = true;
    header("Location: user.php");
    exit();

    $stmt->close();
    $conn->close();
}
