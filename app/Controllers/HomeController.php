<?php

namespace app\controllers;

use app\core\Controller;
use app\services\HomepageService;

class HomeController extends Controller
{
    private HomepageService $homepageservice;

    public function __construct()
    {
        $this->homepageservice = new HomepageService();
    }
    
    public function index()
    {
        $homeData = $this->homepageservice->getHomePageData();
        
        return $this->render('home', [
            'title' => 'The Daily News - Home',
            'hero' => $homeData['hero'],
            'sections' => $homeData['sections']
        ]); 
    }
}