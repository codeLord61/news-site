<?php

namespace app\controllers;

use app\core\controller;

class HomeController extends Controller
{
    public function index()
    {
        $articles = [
            'hero' => [
                [
                    'title' => "Tarique Rahman at Jamaat Ameer's residence",
                    'summary' => "BNP chairman Tarique Rahman has reached the residence of Shafiqur Rahman, the Ameer of Jamaat-e-Islami.",
                    'image' => "https://media.prothomalo.com/prothomalo-english%2F2026-02-15%2Fwgu9qknf%2FTarique-at-Jamaat-ameer-residence.avif?rect=1%2C0%2C621%2C414&w=622&auto=format%2Ccompress&fmt=avif",
                    'url' => "article.html",
                    'time_ago' => "2 hrs ago"
                ],
                [
                    'title' => "Foreign ministers of SAARC countries invited to oath-taking ceremony: Law Adviser",
                    'summary' => "The oath-taking ceremony for the winning Members of Parliament will be held next Tuesday at 10:00 am.",
                    'image' => "https://media.prothomalo.com/prothomalo-english%2F2025-12-22%2Fk8hajh2v%2Fprothomalo-bangla2025-10-30udyf63ohAsifNazrul01.avif?rect=0%2C0%2C542%2C361&w=622&auto=format%2Ccompress&fmt=avif",
                    'url' => "article.html",
                    'time_ago' => "1 hr ago"
                ]
            ],
            'top_stories' => [
                [
                    'category' => "Politics",
                    'category_color' => "blue-600",
                    'title' => "Senate Passes Historic Infrastructure Bill After Marathon Session",
                    'summary' => "The comprehensive bill targets bridges, roads, and broadband expansion, marks a significant bipartisan achievement in the current legislative session.",
                    'image' => "https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "4 hrs ago"
                ],
                [
                    'category' => "Environment",
                    'category_color' => "green-600",
                    'title' => "New Climate Report Urges Immediate Action on Carbon Emissions",
                    'summary' => "Scientists warn that global temperatures are rising faster than predicted, urging nations to implement drastic carbon reduction policies immediately.",
                    'image' => "https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "5 hrs ago"
                ],
                [
                    'category' => "Science",
                    'category_color' => "purple-600",
                    'title' => "Breakthrough in Clean Energy Fusion Reactor Prototype",
                    'summary' => "Researchers have successfully sustained a fusion reaction for record time, bringing the dream of limitless clean energy one step closer to reality.",
                    'image' => "https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "6 hrs ago"
                ]
            ],
            'bangladesh' => [
                [
                    'title' => "Tarique Rahman meets NCP convener Nahid Islam",
                    'summary' => "The BNP chairman went to the residence of Nahid Islam in the city's Bailey Road area tonight, according to the BNP media cell.",
                    'image' => "https://media.prothomalo.com/prothomalo-english%2F2026-02-15%2F8a5gpfeb%2FTarique-Nahid.webp?rect=28%2C0%2C675%2C450&w=622&auto=format%2Ccompress&fmt=avif",
                    'url' => "article.html",
                    'time_ago' => "1 hr ago"
                ],
                [
                    'title' => "Cox's Bazar Sees Record Tourist Numbers This Season",
                    'summary' => "Hotels are fully booked as tourists flock to the world's longest sea beach for the winter holidays, boosting the local economy.",
                    'image' => "https://speedholidays.com.bd/wp-content/uploads/2019/11/Coxs-Bazar-3.jpg",
                    'url' => "article.html",
                    'time_ago' => "3 hrs ago"
                ],
                [
                    'title' => "Badsha Mia: 20 years of tree planting for 'A Handful of Oxygen'",
                    'summary' => "People in Pirganj of Rangpur enjoy the fruits of the trees planted by day labourer Badsha Mia, while travellers find comfort in their shade.",
                    'image' => "https://media.prothomalo.com/prothomalo-english%2F2025-10-25%2Fx68ik2nj%2Fprothomalo-bangla2025-10-24y5vj0gxs24102025-cm-1.avif?rect=1%2C0%2C593%2C395&w=400&auto=format%2Ccompress&fmt=avif",
                    'url' => "article.html",
                    'time_ago' => "5 hrs ago"
                ],
                [
                    'title' => "bKash, Nagad and Rocket: Restrictions on sending money lifted",
                    'summary' => "Mobile financial services and internet banking services had been restricted to prevent the misuse of funds during the 13th national parliamentery elections.",
                    'image' => "https://media.prothomalo.com/prothomalo-english%2F2026-02-08%2Fq5eaplbh%2Fprothomalo-bangla2026-02-01raxih7vuUntitled-1k.avif?rect=0%2C0%2C776%2C517&w=400&auto=format%2Ccompress&fmt=avif",
                    'url' => "article.html",
                    'time_ago' => "8 hrs ago"
                ]
            ],
            'international' => [
                [
                    'title' => "UN Summit Focuses on Global Peace Building",
                    'summary' => "World leaders gathered in New York to discuss strategies for resolving conflicts and fostering long-term stability in volatile regions.",
                    'image' => "https://images.unsplash.com/photo-1524850011238-e3d235c7d4c9?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "2 hrs ago"
                ],
                [
                    'title' => "New Mars Rover Sends Back Stunning Panorama",
                    'summary' => "The latest rover has captured high-resolution images of the Martian surface, revealing potential signs of ancient riverbeds.",
                    'image' => "https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "4 hrs ago"
                ],
                [
                    'title' => "Global Economic Outlook Improves for 2026",
                    'summary' => "Economists predict a steady recovery in global markets as inflation stabilizes and supply chains return to normal.",
                    'image' => "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "7 hrs ago"
                ],
                [
                    'title' => "EU Proposes Stricter Regulations for Tech Giants",
                    'summary' => "New legislation aims to curb monopoly power and ensure data privacy rights for citizens across the European Union.",
                    'image' => "https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "9 hrs ago"
                ]
            ],
            'smart_living' => [
                [
                    'title' => "The Future of AI in Daily Life",
                    'summary' => "From smart assistants to autonomous vehicles, artificial intelligence is reshaping how we live and work.",
                    'image' => "https://images.unsplash.com/photo-1526738549149-8e07eca6c147?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "12 hrs ago"
                ],
                [
                    'title' => "Smart Home Gadgets Review",
                    'summary' => "We tested the latest smart home devices to find out which ones truly add value to your connected household.",
                    'image' => "https://images.unsplash.com/photo-1535223289827-42f1e9919769?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "1 day ago"
                ],
                [
                    'title' => "Remote Work Trends 2026",
                    'summary' => "As remote work becomes the norm, companies are adopting new strategies to maintain team cohesion and productivity.",
                    'image' => "https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "1 day ago"
                ],
                [
                    'title' => "Cybersecurity Tips for All",
                    'summary' => "Essential tips to protect your personal data and digital identity in an increasingly connected world.",
                    'image' => "https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?q=80&w=600&auto=format&fit=crop",
                    'url' => "article.html",
                    'time_ago' => "2 days ago"
                ]
            ]
        ];

        $data = [
            'title' => 'The Daily News - Home',
            'articles' => $articles
        ];

        // Renders app/views/home.php inside the main.php layout
        $this->view('home', $data);
    }

    public function about(){
        
    }
}