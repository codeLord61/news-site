<?php

/**
 * Generate a dynamic URL relative to the project root.
 * 
 * @param string $path
 * @return string
 */
function url(string $path = ''): string
{
    $baseUrl = \app\core\App::$PROJECT_ROOT_URL;
    // Ensure path starts with /
    $path = '/' . ltrim($path, '/');
    return $baseUrl . $path;
}

/**
 * Get parent categories for the header navigation.
 * 
 * @return array
 */
function get_header_categories(): array
{
    $categoryModel = new \app\models\Category();
    return $categoryModel->getParents();
}

/**
 * Get categories with their children for the overlay menu.
 * 
 * @return array
 */
function get_categories_with_children(): array
{
    $categoryModel = new \app\models\Category();
    $parents = $categoryModel->getParents();
    
    foreach ($parents as &$parent) {
        $parent['children'] = $categoryModel->getChildren($parent['id']);
    }
    
    return $parents;
}
