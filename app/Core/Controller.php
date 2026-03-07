<?php

namespace app\core;

abstract class Controller
{
    public string $layout = 'main';

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function render($view, $params = [])
    {
        $viewObj = new View();
        $viewObj->layout = $this->layout;
        return $viewObj->renderView($view, $params);
    }
}
