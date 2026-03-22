<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use app\core\App;
use app\core\Database;

$app = new App(dirname(__DIR__, 2));
$db = new Database();

// Seed Roles
$roles = [
    ['Admin', 'Full system access'],
    ['Editor', 'Content moderation, scheduling news and approve/reject news'],
    ['Reporter', 'Create and edit own articles, submit news for approval'],
    ['Reader', 'Bookmark articles, comment on articles, edit or delete own comments, update profile'],
    ['Guest', 'Read-only access']
];

foreach ($roles as $role) {
    $stmt = $db->pdo->prepare("INSERT IGNORE INTO roles (name, description) VALUES (?, ?)");
    $stmt->execute([$role[0], $role[1]]);
}

//  Seed Admin User
$stmt = $db->pdo->prepare("SELECT id FROM roles WHERE name = 'Admin'");
$stmt->execute();
$adminRole = $stmt->fetchColumn();

if ($adminRole) {
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->pdo->prepare("INSERT IGNORE INTO users (email, name, password, pass, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin@packlynews.com', 'System Admin', $password, 'admin123', $adminRole]);
}

echo "Roles and Admin user seeded successfully!\n";