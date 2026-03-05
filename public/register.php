<?php
require 'db.php';

if(isset($_POST['submit'])){
    $fullname = $_POST['fullname'] ?? 'Anonymous';
    $email = $_POST['email'] ?? 'Unknown';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO test (fullname, email, password)
VALUES (:fullname, :email, :password);");
    $stmt->execute([
        ':fullname' => $fullname,
        ':email' => $email,
        ':password' => $password       
    ]);
    
    // header("Location: ")
}