<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use app\core\App;
use app\core\Database;

$app = new App(dirname(__DIR__, 2));
$db = new Database();

// ----- Seed Tags -----
$tags = [
    ['name' => 'Breaking News', 'slug' => 'breaking-news'],
    ['name' => 'Analysis', 'slug' => 'analysis'],
    ['name' => 'Investigation', 'slug' => 'investigation'],
    ['name' => 'Climate', 'slug' => 'climate'],
    ['name' => 'Economy', 'slug' => 'economy'],
    ['name' => 'Startup', 'slug' => 'startup'],
    ['name' => 'Elections', 'slug' => 'elections'],
    ['name' => 'Iran', 'slug' => 'iran'],
    ['name' => 'War', 'slug' => 'war'],
];

$tagStmt = $db->pdo->prepare("INSERT IGNORE INTO tags (name, slug) VALUES (?, ?)");
foreach ($tags as $tag) {
    $tagStmt->execute([$tag['name'], $tag['slug']]);
}
echo "Tags seeded! (" . count($tags) . " tags)\n";

// ----- Get reporter user (admin for now) -----
$stmt = $db->pdo->prepare("SELECT id FROM users LIMIT 1");
$stmt->execute();
$reporterId = $stmt->fetchColumn();

if (!$reporterId) {
    echo "ERROR: No users found. Run seeder 001 first.\n";
    exit(1);
}

// ----- Fetch category IDs -----
$catStmt = $db->pdo->prepare("SELECT id, slug FROM categories");
$catStmt->execute();
$categoryMap = [];
while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
    $categoryMap[$row['slug']] = $row['id'];
}

// ----- Fetch tag IDs -----
$tagFetchStmt = $db->pdo->prepare("SELECT id, slug FROM tags");
$tagFetchStmt->execute();
$tagMap = [];
while ($row = $tagFetchStmt->fetch(PDO::FETCH_ASSOC)) {
    $tagMap[$row['slug']] = $row['id'];
}

// ----- Articles Data -----
// Available categories: bangladesh, international, sports, opinion, business, youth, technology, entertainment, lifestyle, football, cricket, gadgets
// Available tags: breaking-news, analysis, investigation, climate, economy, startup, elections, iran, war
$articles = [
    [
        'title' => 'Government Unveils New Infrastructure Plan Worth $50 Billion',
        'slug' => 'government-infrastructure-plan-50-billion',
        'excerpt' => 'A massive infrastructure overhaul spanning highways, bridges, and digital connectivity has been announced.',
        'content' => 'The government today announced an ambitious $50 billion infrastructure development plan that aims to transform the nation\'s transportation and digital networks over the next decade. The plan includes construction of 5,000 km of new highways, renovation of 800 bridges, and deployment of high-speed internet to rural areas. Officials say the project will generate millions of jobs and significantly boost economic growth. Critics, however, question the funding sources and environmental impact assessments.',
        'status' => 'published',
        'published_at' => '2026-03-10 08:00:00',
        'view_count' => 4520,
        'categories' => ['bangladesh', 'business'],
        'tags' => ['breaking-news', 'economy'],
        'thumbnail' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Tech Giants Report Record Quarterly Earnings',
        'slug' => 'tech-giants-record-quarterly-earnings',
        'excerpt' => 'Major technology companies exceeded analyst expectations with strong revenue growth.',
        'content' => 'Several of the world\'s largest technology companies reported record-breaking earnings this quarter, driven by surging demand for cloud computing, AI services, and digital advertising. Combined revenues topped $300 billion, exceeding Wall Street expectations by a significant margin. Analysts attribute the growth to increased enterprise adoption of artificial intelligence tools and continued expansion in emerging markets.',
        'status' => 'published',
        'published_at' => '2026-03-09 14:30:00',
        'view_count' => 3870,
        'categories' => ['technology', 'business'],
        'tags' => ['analysis', 'economy'],
        'thumbnail' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'National Football Team Secures Historic World Cup Qualification',
        'slug' => 'national-football-world-cup-qualification',
        'excerpt' => 'A dramatic last-minute goal sealed the team\'s place in next year\'s World Cup.',
        'content' => 'In what will be remembered as one of the greatest moments in the nation\'s sporting history, the national football team secured World Cup qualification with a stunning 2-1 victory. The winning goal came in the 93rd minute from a free kick that curled past the goalkeeper. Thousands of fans flooded the streets in celebration as the team qualified for only the second time in history.',
        'status' => 'published',
        'published_at' => '2026-03-09 22:00:00',
        'view_count' => 8920,
        'categories' => ['sports', 'football'],
        'tags' => ['breaking-news'],
        'thumbnail' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Breakthrough AI Model Can Predict Natural Disasters 72 Hours Early',
        'slug' => 'ai-model-predict-natural-disasters',
        'excerpt' => 'Researchers developed an AI system that significantly improves early warning capabilities.',
        'content' => 'A team of international researchers has unveiled a groundbreaking AI model capable of predicting natural disasters up to 72 hours in advance with 94% accuracy. The model analyzes satellite imagery, seismic data, and atmospheric patterns using deep learning algorithms. Early testing successfully predicted two earthquakes and a tropical storm. The team plans to make the technology available to governments worldwide for disaster preparedness.',
        'status' => 'published',
        'published_at' => '2026-03-08 10:00:00',
        'view_count' => 6340,
        'categories' => ['technology', 'international'],
        'tags' => ['analysis', 'breaking-news'],
        'thumbnail' => 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Global Climate Summit Reaches Historic Carbon Reduction Agreement',
        'slug' => 'global-climate-summit-carbon-agreement',
        'excerpt' => 'Over 150 nations commit to ambitious new targets for reducing greenhouse emissions.',
        'content' => 'In a landmark moment for global climate action, representatives from over 150 nations signed a binding agreement to reduce carbon emissions by 60% before 2040. The agreement, reached after two weeks of intense negotiations, includes financial commitments from developed nations to support green transitions in developing countries. Environmental groups cautiously welcomed the deal while warning that implementation timelines need to be accelerated.',
        'status' => 'published',
        'published_at' => '2026-03-08 16:00:00',
        'view_count' => 5180,
        'categories' => ['international'],
        'tags' => ['breaking-news', 'climate'],
        'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Central Bank Holds Interest Rates Steady Amid Inflation Concerns',
        'slug' => 'central-bank-interest-rates-inflation',
        'excerpt' => 'The central bank decided to maintain current rates while monitoring inflationary pressures.',
        'content' => 'The central bank announced today that interest rates will remain unchanged at 5.25%, citing the need to balance economic growth with persistent inflationary pressures. Governor stated that while inflation has shown signs of moderating, it remains above the target band. The decision was widely expected by markets, though some economists had pushed for a rate cut to stimulate sluggish consumer spending.',
        'status' => 'published',
        'published_at' => '2026-03-07 12:00:00',
        'view_count' => 2890,
        'categories' => ['business', 'bangladesh'],
        'tags' => ['analysis', 'economy'],
        'thumbnail' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'New Study Links Sleep Quality to Long-Term Heart Health',
        'slug' => 'sleep-quality-heart-health-study',
        'excerpt' => 'Researchers found that consistent sleep patterns reduce cardiovascular risk by 35%.',
        'content' => 'A comprehensive 10-year study involving 50,000 participants has revealed a strong connection between consistent sleep quality and cardiovascular health. Participants who maintained 7-8 hours of regular sleep showed a 35% lower risk of heart disease compared to irregular sleepers. The study, published in a leading medical journal, also found that sleep disruptions were as harmful to heart health as smoking. Doctors now recommend sleep hygiene as a key component of preventive healthcare.',
        'status' => 'published',
        'published_at' => '2026-03-07 09:00:00',
        'view_count' => 3210,
        'categories' => ['lifestyle', 'international'],
        'tags' => ['analysis'],
        'thumbnail' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Award-Winning Director Announces New Film Based on True Events',
        'slug' => 'award-winning-director-new-film-true-events',
        'excerpt' => 'The Oscar-winning filmmaker reveals details about an upcoming biographical drama.',
        'content' => 'Acclaimed filmmaker and two-time Oscar winner has announced their next project — a biographical drama based on the incredible true story of a whistleblower who exposed corporate corruption. The film has already attracted A-list talent and is expected to begin production next month. Industry insiders predict it could be a major contender during awards season, with the source material described as both timely and deeply compelling.',
        'status' => 'published',
        'published_at' => '2026-03-06 18:00:00',
        'view_count' => 4100,
        'categories' => ['entertainment'],
        'tags' => ['investigation'],
        'thumbnail' => 'https://images.unsplash.com/photo-1524850011238-e3d235c7d4c9?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Youth Entrepreneurs Leading the Next Wave of Bangladeshi Startups',
        'slug' => 'youth-entrepreneurs-bangladeshi-startups',
        'excerpt' => 'Young founders are transforming the startup ecosystem with innovative tech solutions.',
        'content' => 'A new generation of young entrepreneurs in Bangladesh is redefining the startup landscape. From fintech platforms to edtech solutions, these founders — many still in their twenties — are attracting international investors and building products that solve real problems. Industry experts say the youth-led startup boom is being fueled by improved digital infrastructure, growing venture capital interest, and a culture that increasingly celebrates entrepreneurship.',
        'status' => 'published',
        'published_at' => '2026-03-06 11:00:00',
        'view_count' => 2450,
        'categories' => ['youth', 'technology', 'bangladesh'],
        'tags' => ['startup', 'economy'],
        'thumbnail' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Opposition Party Demands Parliamentary Inquiry into Budget Spending',
        'slug' => 'opposition-demands-parliamentary-inquiry-budget',
        'excerpt' => 'Allegations of misallocated funds spark calls for a formal investigation.',
        'content' => 'The main opposition party has formally demanded a parliamentary inquiry into alleged irregularities in budget allocation for the current fiscal year. Party leaders presented documents suggesting that significant funds earmarked for public welfare were redirected to other projects without proper authorization. The ruling party dismissed the allegations as politically motivated, but several independent lawmakers have expressed support for an investigation.',
        'status' => 'published',
        'published_at' => '2026-03-05 15:00:00',
        'view_count' => 3680,
        'categories' => ['bangladesh', 'opinion'],
        'tags' => ['investigation', 'breaking-news'],
        'thumbnail' => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Cricket World Cup Semi-Final Preview: Key Matchups to Watch',
        'slug' => 'cricket-world-cup-semi-final-preview',
        'excerpt' => 'An in-depth look at the battles that could decide the fate of the tournament.',
        'content' => 'As the Cricket World Cup enters the semi-final stage, all eyes are on the crucial matchups that could determine which teams advance to the final. The pace attack versus the explosive batting lineup promises a thrilling contest, while the spin battle in the other semi-final could be decisive on a turning pitch. Our analysts break down the key player battles, pitch conditions, and tactical approaches each team is likely to employ.',
        'status' => 'published',
        'published_at' => '2026-03-05 20:00:00',
        'view_count' => 7500,
        'categories' => ['sports', 'cricket'],
        'tags' => ['analysis'],
        'thumbnail' => 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'SpaceX Successfully Launches Largest Satellite Constellation Ever',
        'slug' => 'spacex-largest-satellite-constellation',
        'excerpt' => 'A record 180 satellites deployed in a single mission to expand global internet coverage.',
        'content' => 'SpaceX has achieved another milestone by successfully deploying 180 satellites in a single Falcon Heavy launch, the largest satellite constellation deployment in history. The satellites are part of the next-generation Starlink network, designed to provide high-speed internet access to underserved regions worldwide. The mission was flawlessly executed, with all satellites reaching their intended orbits within hours of launch.',
        'status' => 'published',
        'published_at' => '2026-03-04 07:00:00',
        'view_count' => 5640,
        'categories' => ['technology', 'international'],
        'tags' => ['breaking-news'],
        'thumbnail' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Local Startup Raises $20 Million to Revolutionize Food Delivery',
        'slug' => 'local-startup-20-million-food-delivery',
        'excerpt' => 'The company plans to use drone technology for ultra-fast deliveries across the city.',
        'content' => 'A local tech startup has secured $20 million in Series A funding to launch a drone-based food delivery service that promises delivery within 10 minutes. The company has already completed successful pilot tests in several neighborhoods and plans to expand citywide by the end of the year. Investors are bullish on the company\'s proprietary drone navigation technology, which can operate effectively even in dense urban environments.',
        'status' => 'published',
        'published_at' => '2026-03-04 13:00:00',
        'view_count' => 1950,
        'categories' => ['business', 'technology'],
        'tags' => ['startup'],
        'thumbnail' => 'https://images.unsplash.com/photo-1535223289827-42f1e9919769?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'New Gadget Lineup Features Revolutionary Battery Technology',
        'slug' => 'new-gadget-revolutionary-battery',
        'excerpt' => 'The next generation of devices promises week-long battery life with rapid charging.',
        'content' => 'A major manufacturer unveiled its 2026 flagship gadget lineup featuring solid-state battery technology that can last up to 7 days on a single charge. The new batteries also support ultra-rapid charging, going from 0 to 100% in just 12 minutes. The innovation represents a massive leap forward in portable technology and is expected to reshape consumer expectations for all handheld devices.',
        'status' => 'published',
        'published_at' => '2026-03-03 10:00:00',
        'view_count' => 4800,
        'categories' => ['technology', 'gadgets'],
        'tags' => ['breaking-news', 'analysis'],
        'thumbnail' => 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Iran Nuclear Talks Enter Critical Phase as Deadline Looms',
        'slug' => 'iran-nuclear-talks-critical-phase',
        'excerpt' => 'Negotiators race against time to reach an agreement on the landmark nuclear deal.',
        'content' => 'International negotiations over Iran\'s nuclear program have entered a critical phase as the self-imposed deadline approaches. Diplomats from multiple world powers are working around the clock to bridge remaining gaps on uranium enrichment limits and sanctions relief. Sources close to the talks say progress has been made on key technical issues, but political disagreements remain. The outcome could reshape geopolitical dynamics across the Middle East and beyond.',
        'status' => 'published',
        'published_at' => '2026-03-03 14:00:00',
        'view_count' => 2780,
        'categories' => ['international'],
        'tags' => ['iran', 'war', 'breaking-news'],
        'thumbnail' => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Upcoming Elections: A Deep Dive into Key Policy Differences',
        'slug' => 'upcoming-elections-policy-differences',
        'excerpt' => 'How the major parties differ on economy, healthcare, and foreign policy.',
        'content' => 'With elections just months away, voters are beginning to examine the stark policy differences between the major parties. On the economy, one side favors tax cuts and deregulation while the other advocates increased public spending and wealth redistribution. Healthcare remains a deeply divisive issue, with proposals ranging from universal coverage to market-based reforms. Our analysis provides a comprehensive breakdown of where each party stands on the issues that matter most to voters.',
        'status' => 'published',
        'published_at' => '2026-03-02 09:00:00',
        'view_count' => 6100,
        'categories' => ['bangladesh', 'opinion'],
        'tags' => ['elections', 'analysis'],
        'thumbnail' => 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Massive Coral Reef Recovery Observed in Protected Marine Zones',
        'slug' => 'coral-reef-recovery-protected-marine-zones',
        'excerpt' => 'Scientists report unprecedented coral regrowth in areas where fishing was banned.',
        'content' => 'Marine biologists are celebrating unprecedented coral reef recovery in several protected ocean zones established five years ago. The areas, where commercial fishing and anchoring were banned, have seen coral coverage increase by up to 70%. The recovery is attributed to reduced human interference and innovative reef restoration techniques including coral gardening. Scientists say the findings provide a blueprint for marine conservation efforts worldwide.',
        'status' => 'published',
        'published_at' => '2026-03-01 11:00:00',
        'view_count' => 3400,
        'categories' => ['international', 'lifestyle'],
        'tags' => ['climate'],
        'thumbnail' => 'https://images.unsplash.com/photo-1582967788606-a171c1080cb0?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'War in Eastern Europe Escalates as New Offensive Begins',
        'slug' => 'war-eastern-europe-new-offensive',
        'excerpt' => 'Heavy fighting reported along the front lines as forces launch a major military operation.',
        'content' => 'Fighting in eastern Europe has intensified dramatically as military forces launched a large-scale offensive along multiple front lines. International observers report heavy shelling and troop movements in several key areas. Diplomatic efforts to broker a ceasefire have stalled, with both sides accusing each other of violating previous agreements. The UN Security Council has called an emergency session to address the escalating crisis.',
        'status' => 'published',
        'published_at' => '2026-02-28 16:00:00',
        'view_count' => 7200,
        'categories' => ['international'],
        'tags' => ['war', 'breaking-news'],
        'thumbnail' => 'https://images.unsplash.com/photo-1567521464027-f127ff144326?q=80&w=600&auto=format&fit=crop',
    ],
    // A draft article (not published — should NOT appear in public API)
    [
        'title' => 'Draft: Upcoming Trade Policy Changes Under Review',
        'slug' => 'draft-upcoming-trade-policy-changes',
        'excerpt' => 'The government is considering major revisions to international trade agreements.',
        'content' => 'This article is still in draft form. The content is being reviewed and verified before publication.',
        'status' => 'draft',
        'published_at' => null,
        'view_count' => 0,
        'categories' => ['business', 'bangladesh'],
        'tags' => ['economy'],
        'thumbnail' => null,
    ],
    // A submitted article (not published)
    [
        'title' => 'Submitted: Rural Healthcare Access Improving Nationwide',
        'slug' => 'submitted-rural-healthcare-access',
        'excerpt' => 'New clinics and telemedicine programs are reaching previously underserved communities.',
        'content' => 'This article has been submitted for editor review and pending approval.',
        'status' => 'submitted',
        'published_at' => null,
        'view_count' => 0,
        'categories' => ['bangladesh', 'lifestyle'],
        'tags' => ['investigation'],
        'thumbnail' => null,
    ],
];

// ----- Insert articles -----
$articleStmt = $db->pdo->prepare(
    "INSERT IGNORE INTO articles (title, slug, excerpt, content, status, reporter_id, published_at, view_count, created_at) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
);

$pivotCatStmt = $db->pdo->prepare("INSERT IGNORE INTO articles_categories (article_id, category_id) VALUES (?, ?)");
$pivotTagStmt = $db->pdo->prepare("INSERT IGNORE INTO articles_tags (article_id, tag_id) VALUES (?, ?)");
$mediaStmt = $db->pdo->prepare("INSERT INTO medias (file_url, media_type, alt_text, uploaded_by, created_at) VALUES (?, 'image', ?, ?, NOW())");
$pivotMediaStmt = $db->pdo->prepare("INSERT IGNORE INTO articles_medias (article_id, media_id) VALUES (?, ?)");

$inserted = 0;
foreach ($articles as $article) {
    $articleStmt->execute([
        $article['title'],
        $article['slug'],
        $article['excerpt'],
        $article['content'],
        $article['status'],
        $reporterId,
        $article['published_at'],
        $article['view_count'],
    ]);

    $articleId = $db->pdo->lastInsertId();
    if (!$articleId) {
        // Article might already exist (IGNORE), try fetching by slug
        $fetchStmt = $db->pdo->prepare("SELECT id FROM articles WHERE slug = ?");
        $fetchStmt->execute([$article['slug']]);
        $articleId = $fetchStmt->fetchColumn();
    }

    if ($articleId) {
        // Link categories
        foreach ($article['categories'] as $catSlug) {
            if (isset($categoryMap[$catSlug])) {
                $pivotCatStmt->execute([$articleId, $categoryMap[$catSlug]]);
            }
        }
        // Link tags
        foreach ($article['tags'] as $tagSlug) {
            if (isset($tagMap[$tagSlug])) {
                $pivotTagStmt->execute([$articleId, $tagMap[$tagSlug]]);
            }
        }
        // Link media (thumbnail)
        if (!empty($article['thumbnail'])) {
            $mediaStmt->execute([
                $article['thumbnail'],
                $article['title'],
                $reporterId,
            ]);
            $mediaId = $db->pdo->lastInsertId();
            if ($mediaId) {
                $pivotMediaStmt->execute([$articleId, $mediaId]);
            }
        }
        $inserted++;
    }
}

echo "Articles seeded! ($inserted articles with categories, tags, and media linked)\n";
