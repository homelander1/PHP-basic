<?php

function processForm($data)
{
    $dbserver = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dynamicweb";

    $conn = mysqli_connect($dbserver, $username, $password, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $name = trim($data["name"] ?? '');
    $email = trim($data["email"] ?? '');

    $stmt = $conn->prepare("INSERT INTO subscribers (name, email, subscription_date) VALUES (?, ?, NOW())");
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ss", $name, $email);

    if (!$stmt->execute()) {
        die("Execute failed: " . htmlspecialchars($stmt->error));
    }
    $stmt->close();
    $conn->close();
}
