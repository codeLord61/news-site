<?php

namespace app\controllers;

use app\core\Controller;
use app\services\HomepageService;

class HomeController extends Controller
{
    private HomepageService $homepageService;

    public function __construct()
    {
        $this->homepageService = new HomepageService();
    }

    public function index()
    {
        $homeData = $this->homepageService->getHomePageData();

        return $this->render('home', [
            'title' => 'Packly News - Home',
            'hero' => $homeData['hero'],
            'sections' => $homeData['sections'],
        ]);
    }
}
