<?php
require 'db.php';

if (isset($_POST['submit'])){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Get row of that user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM test WHERE email= :email AND password= :password");
    $stmt->execute([
        ':email' => $email,
        ':password' => $password
    ]);
    $result = $stmt->fetch();
    var_dump($result);
    // Error message if user not found
    // if ($result === 0){
    //     echo "User no exist";
    // } else {
    //     echo "User exists";
    // }
}