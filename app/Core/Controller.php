<?php

namespace App\Core;

class Controller
{
    /**
     * Helper to load view files.
     * 
     * @param string $view  View filename (e.g., 'home' loads 'home.php')
     * @param array $data   Data to extract into the view
     */
    protected function view(string $view, array $data = [])
    {
        // Extract array keys to variables
        extract($data);

        // Start output buffering to capture the nested view content
        ob_start();
        $viewFile = __DIR__ . "/../Views/{$view}.php";

        if (file_exists($viewFile)) {
            require $viewFile;
        }
        else {
            die("View {$view} not found!");
        }

        $content = ob_get_clean();

        // Load the main layout file automatically
        require __DIR__ . "/../Views/layouts/main.php";
    }
}
