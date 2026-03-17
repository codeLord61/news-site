<?php
use app\core\App;
require_once __DIR__ . '/partials/_time_ago.php';

$baseUrl = App::$PROJECT_ROOT_URL;
?>

            <!-- Hero Section -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($hero as $article): ?>
                <a href="<?= $baseUrl ?>/articles/<?= htmlspecialchars($article['slug']) ?>" class="group block space-y-3">
                    <!-- Image -->
                    <div class="relative w-full h-56 bg-gray-200 rounded-lg overflow-hidden">
                        <?php if (!empty($article['thumbnail'])): ?>
                        <img src="<?= htmlspecialchars($article['thumbnail']) ?>"
                            alt="<?= htmlspecialchars($article['alt_text'] ?? $article['title']) ?>"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="fa-solid fa-image text-4xl"></i>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Headline -->
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 group-hover:text-blue-600">
                        <?= htmlspecialchars($article['title']) ?>
                    </h2>

                    <!-- Excerpt -->
                    <p class="text-sm text-gray-600 leading-relaxed font-sans line-clamp-2">
                        <?= htmlspecialchars($article['excerpt'] ?? '') ?>
                    </p>

                    <!-- Timestamp -->
                    <p class="text-xs text-gray-400 font-medium"><?= timeAgo($article['published_at']) ?></p>
                </a>
                <?php endforeach; ?>
            </section>

            <?php foreach ($sections as $index => $section):
                $category = $section['category'];
                $articles = $section['articles'];
                if (empty($articles)) continue;

                // Assign border colors to category sections for visual variety
                $borderColors = ['green-600', 'blue-600', 'red-600', 'orange-600', 'purple-600', 'pink-600', 'yellow-600', 'teal-600'];
                $borderColor = $borderColors[$index % count($borderColors)];
            ?>

            <hr class="border-gray-100">

            <!-- Category: <?= htmlspecialchars($category['name']) ?> -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 border-l-4 border-<?= $borderColor ?> pl-2"><?= htmlspecialchars($category['name']) ?></h3>
                    <a href="<?= $baseUrl ?>/categories/<?= htmlspecialchars($category['slug']) ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700">See All</a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-5 gap-y-10">
                    <?php foreach ($articles as $article): ?>
                    <a href="<?= $baseUrl ?>/articles/<?= htmlspecialchars($article['slug']) ?>" class="block group">
                        <article class="flex flex-col gap-2">
                            <div class="w-full aspect-[4/3] bg-gray-200 rounded-md overflow-hidden">
                                <?php if (!empty($article['thumbnail'])): ?>
                                <img src="<?= htmlspecialchars($article['thumbnail']) ?>"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    alt="<?= htmlspecialchars($article['alt_text'] ?? $article['title']) ?>">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-image text-3xl"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col">
                                <h4
                                    class="text-sm font-bold leading-snug text-gray-900 line-clamp-2 mb-1 group-hover:text-blue-600">
                                    <?= htmlspecialchars($article['title']) ?>
                                </h4>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-1">
                                    <?= htmlspecialchars($article['excerpt'] ?? '') ?>
                                </p>
                                <span class="text-xs text-gray-400"><?= timeAgo($article['published_at']) ?></span>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <?php endforeach; ?>
