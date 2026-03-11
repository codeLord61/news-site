<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use app\core\App;
use app\core\Database;

$app = new App(dirname(__DIR__, 2));
$db = new Database();

// Seed parent categories
$categories = [
    ['name' => 'Bangladesh', 'slug' => 'bangladesh', 'description' => 'News from Bangladesh'],
    ['name' => 'International', 'slug' => 'international', 'description' => 'Global news and world affairs'],
    ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports coverage and match updates'],
    ['name' => 'Opinion', 'slug' => 'opinion', 'description' => 'Editorials and columns'],
    ['name' => 'Business', 'slug' => 'business', 'description' => 'Business, economy, and market news'],
    ['name' => 'Youth', 'slug' => 'youth', 'description' => 'Youth news and updates'],
    ['name' => 'Technology', 'slug' => 'technology', 'description' => 'Tech industry, gadgets, and innovation'],
    ['name' => 'Entertainment', 'slug' => 'entertainment', 'description' => 'Movies, music, TV, and celebrity news'],
    ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'description' => 'Lifestyle, fashion, and travel news'],
];

$stmt = $db->pdo->prepare("INSERT IGNORE INTO categories (name, slug, description) VALUES (?, ?, ?)");
foreach ($categories as $cat) {
    $stmt->execute([$cat['name'], $cat['slug'], $cat['description']]);
}

// Seed child categories
$parentStmt = $db->pdo->prepare("SELECT id FROM categories WHERE slug = ?");

$parentStmt->execute(['sports']);
$sportsId = $parentStmt->fetchColumn();

$parentStmt->execute(['technology']);
$techId = $parentStmt->fetchColumn();

$childCategories = [];
if ($sportsId) {
    $childCategories[] = ['name' => 'Football', 'slug' => 'football', 'description' => 'Football news and match highlights', 'parent_id' => $sportsId];
    $childCategories[] = ['name' => 'Cricket', 'slug' => 'cricket', 'description' => 'Cricket scores, analysis, and news', 'parent_id' => $sportsId];
}
if ($techId) {
    $childCategories[] = ['name' => 'Gadgets', 'slug' => 'gadgets', 'description' => 'Gadget reviews and launches', 'parent_id' => $techId];
    
}

$childStmt = $db->pdo->prepare("INSERT IGNORE INTO categories (name, slug, description, parent_id) VALUES (?, ?, ?, ?)");
foreach ($childCategories as $child) {
    $childStmt->execute([$child['name'], $child['slug'], $child['description'], $child['parent_id']]);
}

echo "Categories seeded successfully! (" . (count($categories) + count($childCategories)) . " categories)\n";