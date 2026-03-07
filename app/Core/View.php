<?php

namespace app\core;

class View
{
    public string $layout = 'main';

    public function renderView($view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);

        // Pass the rendered view content to the layout as $content
        $layoutParams = array_merge($params, ['content' => $viewContent]);
        return $this->layoutContent($layoutParams);
    }

    protected function layoutContent($params = [])
    {
        $layout = $this->layout;
        // make params available to layout too, like $title
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        $layoutPath = App::$ROOT_DIR . "/app/Views/layouts/$layout.php";
        if (file_exists($layoutPath)) {
            include $layoutPath;
        }
        else {
            return "{{content}}"; // Fallback placeholder
        }
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        $viewPath = App::$ROOT_DIR . "/app/Views/$view.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        }
        else {
            echo "View $view not found In " . $viewPath;
        }
        return ob_get_clean();
    }
}
