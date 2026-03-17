<?php
use app\core\App;
require_once __DIR__ . '/partials/_time_ago.php';

$baseUrl = App::$PROJECT_ROOT_URL;
?>

<!-- Category Header -->
<div class="border-b border-gray-200 pb-2">
    <h2 class="text-3xl font-bold text-red-600"><?= htmlspecialchars($category['name']) ?></h2>
    <?php if (!empty($category['description'])): ?>
    <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($category['description']) ?></p>
    <?php endif; ?>
</div>

<?php if (!empty($featuredArticles)): ?>
<!-- Featured Section (Top 5 Articles) -->
<section class="grid grid-cols-2 md:grid-cols-6 gap-4 md:gap-6">
    <?php foreach ($featuredArticles as $index => $article): 
        // Determine grid classes based on index (0 to 4) to mimic original HTML layout exactly
        if ($index === 0) {
            $colClass = "col-span-2 md:col-span-3";
            $aspectClass = "aspect-video";
            $titleClass = "text-xl";
            $excerptClass = "text-sm";
        } elseif ($index === 1) {
            $colClass = "col-span-1 md:col-span-3";
            $aspectClass = "aspect-[4/3] md:aspect-video";
            $titleClass = "text-sm md:text-xl md:mb-2";
            $excerptClass = "text-xs md:text-sm";
        } else {
            $colClass = "col-span-1 md:col-span-2";
            $aspectClass = "aspect-[4/3]";
            $titleClass = "text-sm";
            $excerptClass = "text-xs";
        }
    ?>
    <a href="<?= $baseUrl ?>/articles/<?= htmlspecialchars($article['slug']) ?>" class="<?= $colClass ?> group block">
        <article class="flex flex-col gap-2">
            <div class="w-full <?= $aspectClass ?> bg-gray-200 rounded-md overflow-hidden">
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
                <h3 class="<?= $titleClass ?> font-bold leading-tight text-gray-900 mb-1 group-hover:text-blue-600">
                    <?= htmlspecialchars($article['title']) ?>
                </h3>
                <p class="<?= $excerptClass ?> text-gray-600 line-clamp-2 mb-1">
                    <?= htmlspecialchars($article['excerpt'] ?? '') ?>
                </p>
                <span class="text-xs text-gray-400"><?= timeAgo($article['published_at']) ?></span>
            </div>
        </article>
    </a>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if (!empty($latestArticles)): ?>
<hr class="border-gray-100">

<!-- Latest News -->
<section>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-6">
        <?php foreach ($latestArticles as $article): ?>
        <a href="<?= $baseUrl ?>/articles/<?= htmlspecialchars($article['slug']) ?>" class="block group">
            <article class="flex flex-col gap-2">
                <div class="w-full aspect-video bg-gray-200 rounded-md overflow-hidden">
                    <?php if (!empty($article['thumbnail'])): ?>
                    <img src="<?= htmlspecialchars($article['thumbnail']) ?>"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        alt="<?= htmlspecialchars($article['alt_text'] ?? $article['title']) ?>">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fa-solid fa-image text-2xl"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div>
                    <h4 class="text-sm font-bold leading-snug text-gray-900 line-clamp-2 group-hover:text-blue-600">
                        <?= htmlspecialchars($article['title']) ?>
                    </h4>
                    <span class="text-xs text-gray-400 mt-1 block"><?= timeAgo($article['published_at']) ?></span>
                </div>
            </article>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="mt-8 flex items-center justify-center space-x-2">
        <?php if ($pagination['has_prev']): ?>
            <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
        <?php endif; ?>
        <span class="px-4 py-2 text-sm text-gray-500">Page <?= $pagination['current_page'] ?> of <?= $pagination['total_pages'] ?></span>
        <?php if ($pagination['has_next']): ?>
            <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>
