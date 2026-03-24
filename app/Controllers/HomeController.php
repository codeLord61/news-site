<?php

namespace app\controllers;

use app\core\Controller;
use app\services\HomepageService;

class HomeController extends Controller
{
    private HomepageService $homepageService;

    /**
     * Initialize homepage data service.
     */
    public function __construct()
    {
        $this->homepageService = new HomepageService();
    }

    /**
     * Render homepage with hero + section blocks.
     *
     * Input: none (data pulled from HomepageService).
     * Output: HTML string for home view.
     */
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
