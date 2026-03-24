-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 24, 2026 at 12:37 AM
-- Server version: 8.4.3
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `packly_news`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `excerpt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('draft','submitted','pending','approved','rejected','published') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reporter_id` int DEFAULT NULL,
  `managed_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `scheduled_publish_time` timestamp NULL DEFAULT NULL,
  `view_count` int DEFAULT '0',
  `share_count` int DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `excerpt`, `slug`, `content`, `status`, `reporter_id`, `managed_by`, `created_at`, `updated_at`, `approved_at`, `published_at`, `scheduled_publish_time`, `view_count`, `share_count`, `deleted_at`) VALUES
(21, 'Government Unveils New Infrastructure Plan Worth $50 Billion', 'A massive infrastructure overhaul spanning highways, bridges, and digital connectivity has been announced.', 'government-infrastructure-plan-50-billion', 'The government today announced an ambitious $50 billion infrastructure development plan that aims to transform the nation\'s transportation and digital networks over the next decade. The plan includes construction of 5,000 km of new highways, renovation of 800 bridges, and deployment of high-speed internet to rural areas. Officials say the project will generate millions of jobs and significantly boost economic growth. Critics, however, question the funding sources and environmental impact assessments.', 'published', 1, NULL, '2026-03-14 21:20:44', NULL, NULL, '2026-03-10 02:00:00', NULL, 4551, 0, NULL),
(22, 'Tech Giants Report Record Quarterly Earnings', 'Major technology companies exceeded analyst expectations with strong revenue growth.', 'tech-giants-record-quarterly-earnings', 'Several of the world\'s largest technology companies reported record-breaking earnings this quarter, driven by surging demand for cloud computing, AI services, and digital advertising. Combined revenues topped $300 billion, exceeding Wall Street expectations by a significant margin. Analysts attribute the growth to increased enterprise adoption of artificial intelligence tools and continued expansion in emerging markets.', 'published', 1, NULL, '2026-03-14 21:20:44', NULL, NULL, '2026-03-09 08:30:00', NULL, 3870, 0, NULL),
(23, 'National Football Team Secures Historic World Cup Qualification', 'A dramatic last-minute goal sealed the team\'s place in next year\'s World Cup.', 'national-football-world-cup-qualification', 'In what will be remembered as one of the greatest moments in the nation\'s sporting history, the national football team secured World Cup qualification with a stunning 2-1 victory. The winning goal came in the 93rd minute from a free kick that curled past the goalkeeper. Thousands of fans flooded the streets in celebration as the team qualified for only the second time in history.', 'published', 1, NULL, '2026-03-14 21:20:44', NULL, NULL, '2026-03-09 16:00:00', NULL, 8921, 0, NULL),
(24, 'Breakthrough AI Model Can Predict Natural Disasters 72 Hours Early', 'Researchers developed an AI system that significantly improves early warning capabilities.', 'ai-model-predict-natural-disasters', 'A team of international researchers has unveiled a groundbreaking AI model capable of predicting natural disasters up to 72 hours in advance with 94% accuracy. The model analyzes satellite imagery, seismic data, and atmospheric patterns using deep learning algorithms. Early testing successfully predicted two earthquakes and a tropical storm. The team plans to make the technology available to governments worldwide for disaster preparedness.', 'published', 1, NULL, '2026-03-14 21:20:44', NULL, NULL, '2026-03-08 04:00:00', NULL, 6341, 0, NULL),
(25, 'Global Climate Summit Reaches Historic Carbon Reduction Agreement', 'Over 150 nations commit to ambitious new targets for reducing greenhouse emissions.', 'global-climate-summit-carbon-agreement', 'In a landmark moment for global climate action, representatives from over 150 nations signed a binding agreement to reduce carbon emissions by 60% before 2040. The agreement, reached after two weeks of intense negotiations, includes financial commitments from developed nations to support green transitions in developing countries. Environmental groups cautiously welcomed the deal while warning that implementation timelines need to be accelerated.', 'published', 1, NULL, '2026-03-14 21:20:44', NULL, NULL, '2026-03-08 10:00:00', NULL, 5185, 0, NULL),
(26, 'Central Bank Holds Interest Rates Steady Amid Inflation Concerns', 'The central bank decided to maintain current rates while monitoring inflationary pressures.', 'central-bank-interest-rates-inflation', 'The central bank announced today that interest rates will remain unchanged at 5.25%, citing the need to balance economic growth with persistent inflationary pressures. Governor stated that while inflation has shown signs of moderating, it remains above the target band. The decision was widely expected by markets, though some economists had pushed for a rate cut to stimulate sluggish consumer spending.', 'published', 1, NULL, '2026-03-14 21:20:44', NULL, NULL, '2026-03-07 06:00:00', NULL, 2891, 0, NULL),
(27, 'New Study Links Sleep Quality to Long-Term Heart Health', 'Researchers found that consistent sleep patterns reduce cardiovascular risk by 35%.', 'sleep-quality-heart-health-study', 'A comprehensive 10-year study involving 50,000 participants has revealed a strong connection between consistent sleep quality and cardiovascular health. Participants who maintained 7-8 hours of regular sleep showed a 35% lower risk of heart disease compared to irregular sleepers. The study, published in a leading medical journal, also found that sleep disruptions were as harmful to heart health as smoking. Doctors now recommend sleep hygiene as a key component of preventive healthcare.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-07 03:00:00', NULL, 3211, 0, NULL),
(28, 'Award-Winning Director Announces New Film Based on True Events', 'The Oscar-winning filmmaker reveals details about an upcoming biographical drama.', 'award-winning-director-new-film-true-events', 'Acclaimed filmmaker and two-time Oscar winner has announced their next project — a biographical drama based on the incredible true story of a whistleblower who exposed corporate corruption. The film has already attracted A-list talent and is expected to begin production next month. Industry insiders predict it could be a major contender during awards season, with the source material described as both timely and deeply compelling.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-06 12:00:00', NULL, 4100, 0, NULL),
(29, 'Youth Entrepreneurs Leading the Next Wave of Bangladeshi Startups', 'Young founders are transforming the startup ecosystem with innovative tech solutions.', 'youth-entrepreneurs-bangladeshi-startups', 'A new generation of young entrepreneurs in Bangladesh is redefining the startup landscape. From fintech platforms to edtech solutions, these founders — many still in their twenties — are attracting international investors and building products that solve real problems. Industry experts say the youth-led startup boom is being fueled by improved digital infrastructure, growing venture capital interest, and a culture that increasingly celebrates entrepreneurship.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-06 05:00:00', NULL, 2450, 0, NULL),
(30, 'Opposition Party Demands Parliamentary Inquiry into Budget Spending', 'Allegations of misallocated funds spark calls for a formal investigation.', 'opposition-demands-parliamentary-inquiry-budget', 'The main opposition party has formally demanded a parliamentary inquiry into alleged irregularities in budget allocation for the current fiscal year. Party leaders presented documents suggesting that significant funds earmarked for public welfare were redirected to other projects without proper authorization. The ruling party dismissed the allegations as politically motivated, but several independent lawmakers have expressed support for an investigation.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-05 09:00:00', NULL, 3680, 0, NULL),
(31, 'Cricket World Cup Semi-Final Preview: Key Matchups to Watch', 'An in-depth look at the battles that could decide the fate of the tournament.', 'cricket-world-cup-semi-final-preview', 'As the Cricket World Cup enters the semi-final stage, all eyes are on the crucial matchups that could determine which teams advance to the final. The pace attack versus the explosive batting lineup promises a thrilling contest, while the spin battle in the other semi-final could be decisive on a turning pitch. Our analysts break down the key player battles, pitch conditions, and tactical approaches each team is likely to employ.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-05 14:00:00', NULL, 7500, 0, NULL),
(32, 'SpaceX Successfully Launches Largest Satellite Constellation Ever', 'A record 180 satellites deployed in a single mission to expand global internet coverage.', 'spacex-largest-satellite-constellation', 'SpaceX has achieved another milestone by successfully deploying 180 satellites in a single Falcon Heavy launch, the largest satellite constellation deployment in history. The satellites are part of the next-generation Starlink network, designed to provide high-speed internet access to underserved regions worldwide. The mission was flawlessly executed, with all satellites reaching their intended orbits within hours of launch.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-04 01:00:00', NULL, 5640, 0, NULL),
(33, 'Local Startup Raises $20 Million to Revolutionize Food Delivery', 'The company plans to use drone technology for ultra-fast deliveries across the city.', 'local-startup-20-million-food-delivery', 'A local tech startup has secured $20 million in Series A funding to launch a drone-based food delivery service that promises delivery within 10 minutes. The company has already completed successful pilot tests in several neighborhoods and plans to expand citywide by the end of the year. Investors are bullish on the company\'s proprietary drone navigation technology, which can operate effectively even in dense urban environments.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-04 07:00:00', NULL, 1950, 0, NULL),
(34, 'New Gadget Lineup Features Revolutionary Battery Technology', 'The next generation of devices promises week-long battery life with rapid charging.', 'new-gadget-revolutionary-battery', 'A major manufacturer unveiled its 2026 flagship gadget lineup featuring solid-state battery technology that can last up to 7 days on a single charge. The new batteries also support ultra-rapid charging, going from 0 to 100% in just 12 minutes. The innovation represents a massive leap forward in portable technology and is expected to reshape consumer expectations for all handheld devices.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-03 04:00:00', NULL, 4802, 0, NULL),
(35, 'Iran Nuclear Talks Enter Critical Phase as Deadline Looms', 'Negotiators race against time to reach an agreement on the landmark nuclear deal.', 'iran-nuclear-talks-critical-phase', 'International negotiations over Iran\'s nuclear program have entered a critical phase as the self-imposed deadline approaches. Diplomats from multiple world powers are working around the clock to bridge remaining gaps on uranium enrichment limits and sanctions relief. Sources close to the talks say progress has been made on key technical issues, but political disagreements remain. The outcome could reshape geopolitical dynamics across the Middle East and beyond.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-03 08:00:00', NULL, 2780, 0, NULL),
(36, 'Upcoming Elections: A Deep Dive into Key Policy Differences', 'How the major parties differ on economy, healthcare, and foreign policy.', 'upcoming-elections-policy-differences', 'With elections just months away, voters are beginning to examine the stark policy differences between the major parties. On the economy, one side favors tax cuts and deregulation while the other advocates increased public spending and wealth redistribution. Healthcare remains a deeply divisive issue, with proposals ranging from universal coverage to market-based reforms. Our analysis provides a comprehensive breakdown of where each party stands on the issues that matter most to voters.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-02 03:00:00', NULL, 6100, 0, NULL),
(37, 'Massive Coral Reef Recovery Observed in Protected Marine Zones', 'Scientists report unprecedented coral regrowth in areas where fishing was banned.', 'coral-reef-recovery-protected-marine-zones', 'Marine biologists are celebrating unprecedented coral reef recovery in several protected ocean zones established five years ago. The areas, where commercial fishing and anchoring were banned, have seen coral coverage increase by up to 70%. The recovery is attributed to reduced human interference and innovative reef restoration techniques including coral gardening. Scientists say the findings provide a blueprint for marine conservation efforts worldwide.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-03-01 05:00:00', NULL, 3400, 0, NULL),
(38, 'War in Eastern Europe Escalates as New Offensive Begins', 'Heavy fighting reported along the front lines as forces launch a major military operation.', 'war-eastern-europe-new-offensive', 'Fighting in eastern Europe has intensified dramatically as military forces launched a large-scale offensive along multiple front lines. International observers report heavy shelling and troop movements in several key areas. Diplomatic efforts to broker a ceasefire have stalled, with both sides accusing each other of violating previous agreements. The UN Security Council has called an emergency session to address the escalating crisis.', 'published', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, '2026-02-28 10:00:00', NULL, 7200, 0, NULL),
(39, 'Draft: Upcoming Trade Policy Changes Under Review', 'The government is considering major revisions to international trade agreements.', 'draft-upcoming-trade-policy-changes', 'This article is still in draft form. The content is being reviewed and verified before publication.', 'draft', 1, NULL, '2026-03-14 21:20:45', NULL, NULL, NULL, NULL, 0, 0, NULL),
(40, 'Submitted: Rural Healthcare Access Improving Nationwide', 'New clinics and telemedicine programs are reaching previously underserved communities.', 'submitted-rural-healthcare-access', 'This article has been submitted for editor review and pending approval.', 'published', 1, 10, '2026-03-14 21:20:45', '2026-03-23 23:44:01', '2026-03-23 23:43:48', '2026-03-23 23:44:01', NULL, 1, 0, NULL),
(41, 'Test', 'Testing first article', 'test', '<p>Testing article creation <strong>Bold,<em> italic, <s>strike, </s>  </em></strong><u>underline</u></p><h1><strong>heading 1</strong></h1><h2><strong>heading 2</strong></h2><blockquote><p><strong><em>asdlksa</em></strong></p></blockquote><ol><li><p>list</p></li><li><p>dsds</p></li></ol><ul><li><p>sdsds</p></li><li><p>asdsa</p></li></ul><pre><code>console.log(\'test\');</code></pre><p></p>', 'pending', 6, 8, '2026-03-23 15:19:35', '2026-03-23 21:33:48', NULL, NULL, NULL, 2, 0, NULL),
(42, 'test run 2 updated', 'test article 2', 'test-run-2-updated', '<p></p>', 'draft', 6, 10, '2026-03-23 16:56:23', '2026-03-23 22:52:45', '2026-03-23 22:13:10', NULL, NULL, 0, 0, NULL),
(43, 'test 3', 'test 3', 'test-3', '<p><strong>sdsds</strong></p><p><strong><em><mark>sd</mark></em></strong></p><p style=\"text-align: center\">sds</p><p style=\"text-align: right\">sds</p><p style=\"text-align: left\">sdsd</p><img src=\"/news-site/public/assets/uploads/articles/20260323234349_ba478e6f767f68c5.jpg\" alt=\"cat\" title=\"cat staring\" data-media-id=\"20\"><hr><p>sdsdsd</p><ol><li><p>fdfsf</p></li></ol><blockquote><p>asdsadd</p></blockquote><ul><li><p>sasd</p></li></ul><p></p>', 'published', 6, NULL, '2026-03-23 17:45:30', '2026-03-23 17:45:30', NULL, NULL, NULL, 3, 0, NULL),
(46, 'boeing 737', 'about boeing 737', 'boeing-737', '<p><strong>hello world</strong></p><img src=\"/news-site/public/assets/uploads/articles/20260324023816_bccebbd46bca9349.jpg\" alt=\"plane\" title=\"flying plane\" data-media-id=\"25\"><p><code>echo($plane);</code></p><p><mark>this is plane</mark></p>', 'published', 6, NULL, '2026-03-23 20:39:30', '2026-03-23 20:39:30', NULL, NULL, NULL, 5, 0, NULL),
(47, 'test draft', 'test draft', 'test-draft', '<p>test draft</p>', 'draft', 6, NULL, '2026-03-23 22:16:42', '2026-03-23 22:16:42', NULL, NULL, NULL, 0, 0, NULL),
(48, 'test 4', 'sds', 'test-4', '<p>asfasfasfda</p>', 'draft', 6, NULL, '2026-03-23 22:55:59', '2026-03-23 22:57:05', NULL, NULL, NULL, 0, 0, '2026-03-23 22:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `articles_categories`
--

CREATE TABLE `articles_categories` (
  `article_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `articles_categories`
--

INSERT INTO `articles_categories` (`article_id`, `category_id`) VALUES
(21, 1),
(26, 1),
(29, 1),
(30, 1),
(36, 1),
(39, 1),
(40, 1),
(41, 1),
(24, 2),
(25, 2),
(27, 2),
(32, 2),
(35, 2),
(37, 2),
(38, 2),
(23, 3),
(31, 3),
(30, 4),
(36, 4),
(21, 5),
(22, 5),
(26, 5),
(33, 5),
(39, 5),
(42, 5),
(43, 5),
(46, 5),
(47, 5),
(29, 6),
(22, 7),
(24, 7),
(29, 7),
(32, 7),
(33, 7),
(34, 7),
(28, 8),
(27, 9),
(37, 9),
(40, 9),
(23, 10),
(31, 11),
(48, 11),
(34, 12);

-- --------------------------------------------------------

--
-- Table structure for table `articles_medias`
--

CREATE TABLE `articles_medias` (
  `article_id` int NOT NULL,
  `media_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `articles_medias`
--

INSERT INTO `articles_medias` (`article_id`, `media_id`) VALUES
(21, 1),
(22, 2),
(23, 3),
(24, 4),
(25, 5),
(26, 6),
(27, 7),
(28, 8),
(29, 9),
(30, 10),
(31, 11),
(32, 12),
(33, 13),
(34, 14),
(35, 15),
(36, 16),
(37, 17),
(38, 18),
(43, 20),
(46, 25),
(40, 26),
(46, 26);

-- --------------------------------------------------------

--
-- Table structure for table `articles_tags`
--

CREATE TABLE `articles_tags` (
  `article_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `articles_tags`
--

INSERT INTO `articles_tags` (`article_id`, `tag_id`) VALUES
(21, 1),
(23, 1),
(24, 1),
(25, 1),
(30, 1),
(32, 1),
(34, 1),
(35, 1),
(38, 1),
(41, 1),
(47, 1),
(22, 2),
(24, 2),
(26, 2),
(27, 2),
(31, 2),
(34, 2),
(36, 2),
(42, 2),
(28, 3),
(30, 3),
(40, 3),
(25, 4),
(37, 4),
(43, 4),
(48, 4),
(21, 5),
(22, 5),
(26, 5),
(29, 5),
(39, 5),
(46, 5),
(29, 6),
(33, 6),
(36, 7),
(35, 8),
(35, 9),
(38, 9);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `parent_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`) VALUES
(1, 'Bangladesh', 'bangladesh', 'News from Bangladesh', NULL),
(2, 'International', 'international', 'Global news and world affairs', NULL),
(3, 'Sports', 'sports', 'Sports coverage and match updates', NULL),
(4, 'Opinion', 'opinion', 'Editorials and columns', NULL),
(5, 'Business', 'business', 'Business, economy, and market news', NULL),
(6, 'Youth', 'youth', 'Youth news and updates', NULL),
(7, 'Technology', 'technology', 'Tech industry, gadgets, and innovation', NULL),
(8, 'Entertainment', 'entertainment', 'Movies, music, TV, and celebrity news', NULL),
(9, 'Lifestyle', 'lifestyle', 'Lifestyle, fashion, and travel news', NULL),
(10, 'Football', 'football', 'Football news and match highlights', 3),
(11, 'Cricket', 'cricket', 'Cricket scores, analysis, and news', 3),
(12, 'Gadgets', 'gadgets', 'Gadget reviews and launches', 7);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `article_id` int NOT NULL,
  `user_id` int NOT NULL,
  `managed_by` int DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `article_id`, `user_id`, `managed_by`, `content`, `is_approved`, `created_at`) VALUES
(1, 21, 7, NULL, 'first comment', 1, '2026-03-23 23:22:34'),
(2, 34, 7, NULL, 'great feature', 1, '2026-03-23 23:29:27');

-- --------------------------------------------------------

--
-- Table structure for table `medias`
--

CREATE TABLE `medias` (
  `id` int NOT NULL,
  `file_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `media_type` enum('image') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_thumbnail` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medias`
--

INSERT INTO `medias` (`id`, `file_url`, `media_type`, `caption`, `alt_text`, `is_thumbnail`, `uploaded_by`, `created_at`) VALUES
(1, 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Government Unveils New Infrastructure Plan Worth $50 Billion', 1, 1, '2026-03-14 21:20:44'),
(2, 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Tech Giants Report Record Quarterly Earnings', 1, 1, '2026-03-14 21:20:44'),
(3, 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'National Football Team Secures Historic World Cup Qualification', 1, 1, '2026-03-14 21:20:44'),
(4, 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Breakthrough AI Model Can Predict Natural Disasters 72 Hours Early', 1, 1, '2026-03-14 21:20:44'),
(5, 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Global Climate Summit Reaches Historic Carbon Reduction Agreement', 1, 1, '2026-03-14 21:20:44'),
(6, 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Central Bank Holds Interest Rates Steady Amid Inflation Concerns', 1, 1, '2026-03-14 21:20:45'),
(7, 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'New Study Links Sleep Quality to Long-Term Heart Health', 1, 1, '2026-03-14 21:20:45'),
(8, 'https://images.unsplash.com/photo-1524850011238-e3d235c7d4c9?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Award-Winning Director Announces New Film Based on True Events', 1, 1, '2026-03-14 21:20:45'),
(9, 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Youth Entrepreneurs Leading the Next Wave of Bangladeshi Startups', 1, 1, '2026-03-14 21:20:45'),
(10, 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Opposition Party Demands Parliamentary Inquiry into Budget Spending', 1, 1, '2026-03-14 21:20:45'),
(11, 'https://images.unsplash.com/photo-1531415074968-036ba1b575da?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Cricket World Cup Semi-Final Preview: Key Matchups to Watch', 1, 1, '2026-03-14 21:20:45'),
(12, 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'SpaceX Successfully Launches Largest Satellite Constellation Ever', 1, 1, '2026-03-14 21:20:45'),
(13, 'https://images.unsplash.com/photo-1535223289827-42f1e9919769?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Local Startup Raises $20 Million to Revolutionize Food Delivery', 1, 1, '2026-03-14 21:20:45'),
(14, 'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'New Gadget Lineup Features Revolutionary Battery Technology', 1, 1, '2026-03-14 21:20:45'),
(15, 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Iran Nuclear Talks Enter Critical Phase as Deadline Looms', 1, 1, '2026-03-14 21:20:45'),
(16, 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Upcoming Elections: A Deep Dive into Key Policy Differences', 1, 1, '2026-03-14 21:20:45'),
(17, 'https://images.unsplash.com/photo-1582967788606-a171c1080cb0?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'Massive Coral Reef Recovery Observed in Protected Marine Zones', 1, 1, '2026-03-14 21:20:45'),
(18, 'https://images.unsplash.com/photo-1567521464027-f127ff144326?q=80&w=600&auto=format&fit=crop', 'image', NULL, 'War in Eastern Europe Escalates as New Offensive Begins', 1, 1, '2026-03-14 21:20:45'),
(19, '/news-site/public/assets/uploads/articles/20260323225501_77bdc9ccd06e1d02.jpg', 'image', 'dog shivers', 'shiver dog', 0, 6, '2026-03-23 16:55:01'),
(20, '/news-site/public/assets/uploads/articles/20260323234349_ba478e6f767f68c5.jpg', 'image', 'cat staring', 'cat', 0, 6, '2026-03-23 17:43:49'),
(25, '/news-site/public/assets/uploads/articles/20260324023816_bccebbd46bca9349.jpg', 'image', 'flying plane', 'plane', 0, 6, '2026-03-23 20:38:16'),
(26, '/news-site/public/assets/uploads/articles/20260324023930_fb9992b4d7dc92c3.jpg', 'image', 'boeing 737 fly', 'boeing 737', 1, 6, '2026-03-23 20:39:30');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int NOT NULL,
  `migration` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `created_at`) VALUES
(1, '001_create_roles_table.php', '2026-03-07 20:35:11'),
(2, '002_create_users_table.php', '2026-03-07 20:35:11'),
(3, '003_create_personal_access_tokens_table.php', '2026-03-07 20:35:11'),
(8, '004_create_articles_table.php', '2026-03-11 01:44:51'),
(9, '005_create_categories_table.php', '2026-03-11 01:44:51'),
(10, '006_create_tags_table.php', '2026-03-11 01:44:51'),
(11, '007_create_articles_pivot_tables.php', '2026-03-11 01:44:51'),
(12, '008_create_medias_tables.php', '2026-03-14 21:18:39'),
(13, '009_add_is_thumbnail_to_medias.php', '2026-03-23 19:55:41'),
(14, '010_create_comments_table.php', '2026-03-23 23:14:09');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `user_id`, `token`, `created_at`, `expires_at`) VALUES
(1, 1, 'dc69e345fc3528bb434fc72250b9c156912bcf6ffe4e12ffbb927e0f9ae1d32c', '2026-03-07 20:37:11', '2026-04-06 14:37:11'),
(2, 1, '4f482f3835ec3902d208d898a89291fc80cdaadc9553c0cb0ceb90719e368854', '2026-03-07 20:46:50', '2026-04-06 14:46:50'),
(4, 1, '5d4293684c8ab28c8bb573a92d9c70f622fbd1fbe2490aadf8f57dbcefa432d0', '2026-03-07 21:00:51', '2026-04-06 15:00:51'),
(7, 3, '04ef1d8015ebc9e23afef4e2f6848eb822add75a9e44aec08ae71dfd9354ef5a', '2026-03-22 06:46:44', '2026-04-21 06:46:44'),
(8, 3, '6af02cb593f2f37b3cd0af92baca480843a4ef287997f61390c60abfbee5cdd1', '2026-03-22 06:47:34', '2026-04-21 06:47:34'),
(9, 3, 'bfdc5facbd7e0946030ab5525b8d4eb5b9af7658a3268bc276af074131fcf1f8', '2026-03-22 06:47:56', '2026-04-21 06:47:56'),
(10, 5, '7dbdf9fb24fd57e1de8b66a0b98933b2c7f02b28173e58c6def5f6b3a5c87ed5', '2026-03-22 06:51:50', '2026-04-21 06:51:50'),
(11, 1, 'ef06067d5b3737801ff9a4a49280b9fbd4a3a0ab03b0165d8d5a0e937ece0a22', '2026-03-22 06:52:30', '2026-04-21 06:52:30'),
(12, 1, '4321252f612b22be902f9cdf6b2c15f12ff718ab47b66df7b22e804b40f34b94', '2026-03-22 18:20:58', '2026-04-21 18:20:58'),
(14, 7, 'adbd3387d8f528f147e533f2e05a9c7b090a88afeacafeb2bcf6e712a24d4265', '2026-03-23 07:54:33', '2026-04-22 07:54:33'),
(15, 7, '7f7997d613a05b524dc03823ca0fa6c3845d1209cc70969268ca78e0047e8e8b', '2026-03-23 08:02:42', '2026-04-22 08:02:42'),
(16, 7, '0033ed42da92bee2f7985b0a4d7e4c32363681a6571d8214506e3c6e79a53b59', '2026-03-23 08:10:55', '2026-04-22 08:10:55'),
(20, 7, '1104f7ea709450be56ddf5aeafbaed385af63f5f8037b801da13bb02b9368d2a', '2026-03-23 08:27:48', '2026-04-22 08:27:48'),
(22, 7, 'b7c1346c21825f1f5bc637e84b4a73440f3f7d5ccdb4c5566964bcd88894c448', '2026-03-23 08:30:53', '2026-04-22 08:30:53'),
(23, 7, '7daf96ce88eba9c5adba99f99faba325f313afad7b0e593d63dbda4e62dcb2ae', '2026-03-23 08:36:45', '2026-04-22 08:36:45'),
(24, 7, 'eae914a2fc02ea41028174b85e37a97fd5bf5d33726fe486ec075709da7a7988', '2026-03-23 08:38:52', '2026-04-22 08:38:52'),
(26, 7, 'fa19b82de8763ec34f0f7be6973a64f29a55660315a5719a1a51d277eb20b676', '2026-03-23 08:45:02', '2026-04-22 08:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Admin', 'Full system access', '2026-03-07 20:35:28'),
(2, 'Editor', 'Content moderation, scheduling news and approve/reject news', '2026-03-07 20:35:28'),
(3, 'Reporter', 'Create and edit own articles, submit news for approval', '2026-03-07 20:35:28'),
(4, 'Reader', 'Bookmark articles, comment on articles, edit or delete own comments, update profile', '2026-03-07 20:35:28'),
(5, 'Guest', 'Read-only access', '2026-03-07 20:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`) VALUES
(1, 'Breaking News', 'breaking-news'),
(2, 'Analysis', 'analysis'),
(3, 'Investigation', 'investigation'),
(4, 'Climate', 'climate'),
(5, 'Economy', 'economy'),
(6, 'Startup', 'startup'),
(7, 'Elections', 'elections'),
(8, 'Iran', 'iran'),
(9, 'War', 'war');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `pass` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role_id` int NOT NULL,
  `avatar_path` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `password`, `pass`, `role_id`, `avatar_path`, `created_at`, `updated_at`) VALUES
(1, 'admin@packlynews.com', 'System Admin', '$2y$12$41uuCzWF3Vh9OI0xFGDRjeeDWiHOhZzkouLhml77UKh.V.ROfdA2C', NULL, 1, NULL, '2026-03-07 20:35:28', NULL),
(2, 'john@doe.com', 'John Doe', '$2y$12$rEW9rvOSH20l2.VO48vzi.Te0knI5rR/Wb/S3K0Hf09.RA7u.ir1G', NULL, 4, NULL, '2026-03-07 21:07:11', NULL),
(3, 'reporter@test.com', 'Reporter Test', '$2y$12$eWrAm69AvPiUa0CjGWXxNek/EUQ1UzTTrpv00cjq4SyJBIo1wFCNq', NULL, 4, NULL, '2026-03-22 06:46:31', NULL),
(4, 'reporter2@test.com', 'Reporter Two', '$2y$12$wVz3vkGRBKJEjxsiW6UwmubGtWoUbb8KsXBbPj79Oy8p4A6DLdZeC', NULL, 3, NULL, '2026-03-22 06:50:56', NULL),
(5, 'reporter3@test.com', 'Reporter Two', '$2y$12$c1pTVw8fI9Wl9IWhPvpHbOgWjwHOd/l4ZwSunsBN/4pqTjj5IxR4S', NULL, 3, NULL, '2026-03-22 06:51:38', NULL),
(6, 'reporter3@gmail.com', 'reporter3', '$2y$12$4Ebh0HeIRoBwJgUlspDTxuGXhW8JPnvdzZy6YPlL1pi.wsoZqEzG2', 'reporter3', 3, NULL, '2026-03-22 19:17:47', NULL),
(7, 'bob@gmail.com', 'car', '$2y$12$Zp/2ANCNtHwFM2lS.rZSVueFXd82DFwOpXPMAjUr/9Tl2cw0TSr5a', 'bob123', 4, '/uploads/avatars/avatar_7_1774308131.jpg', '2026-03-22 19:44:15', NULL),
(8, 'editor@gmail.com', 'editor', '$2y$12$WeGJ/3ft/StWazroGHtFne65PuRIubtDDXCxeF6ZP1lsZdccTt0ai', 'editor123', 2, NULL, '2026-03-23 10:37:44', NULL),
(9, 'foo@gmail.com', 'foo', '$2y$12$6W/8lu5KIi23i4Gbef6uBusm05d3Bx8/lmosh1e2wXS972Xau9lse', 'foo123', 3, NULL, '2026-03-23 12:03:14', NULL),
(10, 'editor2@test.com', 'Editor 2', '$2y$12$jenFjY0nwSQ5e24tS7mRo.Bk7Me7ZMlp871zr9vvRjr1t6jq.Lb06', 'editor2', 2, NULL, '2026-03-23 21:38:02', NULL),
(11, 'user@test.com', 'user 1', '$2y$12$SY/ehl7BXt4w1cD5aub6vOcZ9yYctY.tJkRWebK5aFcB9TzTtdA9.', 'user1', 4, NULL, '2026-03-23 23:31:19', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_articles_reporter` (`reporter_id`),
  ADD KEY `fk_articles_editor` (`managed_by`);
ALTER TABLE `articles` ADD FULLTEXT KEY `ft_articles_search` (`title`,`content`);

--
-- Indexes for table `articles_categories`
--
ALTER TABLE `articles_categories`
  ADD PRIMARY KEY (`article_id`,`category_id`),
  ADD KEY `fk_articles_categories_category` (`category_id`);

--
-- Indexes for table `articles_medias`
--
ALTER TABLE `articles_medias`
  ADD PRIMARY KEY (`article_id`,`media_id`),
  ADD KEY `fk_articles_medias_media` (`media_id`);

--
-- Indexes for table `articles_tags`
--
ALTER TABLE `articles_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `fk_articles_tags_tag` (`tag_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_categories_parent` (`parent_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_article` (`article_id`),
  ADD KEY `fk_comments_user` (`user_id`),
  ADD KEY `fk_comments_editor` (`managed_by`);

--
-- Indexes for table `medias`
--
ALTER TABLE `medias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_media_user` (`uploaded_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_tokens_user` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medias`
--
ALTER TABLE `medias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_articles_editor` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articles_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `articles_categories`
--
ALTER TABLE `articles_categories`
  ADD CONSTRAINT `fk_articles_categories_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articles_categories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `articles_medias`
--
ALTER TABLE `articles_medias`
  ADD CONSTRAINT `fk_articles_medias_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articles_medias_media` FOREIGN KEY (`media_id`) REFERENCES `medias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `articles_tags`
--
ALTER TABLE `articles_tags`
  ADD CONSTRAINT `fk_articles_tags_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articles_tags_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_editor` FOREIGN KEY (`managed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medias`
--
ALTER TABLE `medias`
  ADD CONSTRAINT `fk_media_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD CONSTRAINT `fk_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
