<?php
use app\core\App;
require_once __DIR__ . '/partials/_time_ago.php';

$baseUrl = App::$PROJECT_ROOT_URL;

// Format dates explicitly, e.g., "Sat Feb 14, 2026 10:30 AM"
$publishedDate = new DateTime($article['published_at']);
$formattedPublished = $publishedDate->format('D M j, Y g:i A');

$formattedUpdated = '';
if (!empty($article['updated_at'])) {
    $updatedDate = new DateTime($article['updated_at']);
    $formattedUpdated = $updatedDate->format('D M j, Y g:i A');
}
?>

<!-- Article Header -->
<article class="max-w-3xl mx-auto space-y-8 py-4">
    <!-- Title -->
    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold font-serif text-gray-900 leading-tight">
        <?= htmlspecialchars($article['title']) ?>
    </h1>

    <!-- Metadata -->
    <div class="flex flex-col md:flex-row md:items-center text-sm text-gray-500 gap-2 md:gap-4 border-b border-gray-100 pb-6">
        <div class="flex items-center gap-2">
            <span class="font-bold text-primary-600 uppercase"><?= htmlspecialchars($primaryCategory) ?></span>
            <span class="text-gray-300">|</span>
            <span class="font-medium text-gray-700">By <?= htmlspecialchars($article['reporter']['name'] ?? 'Staff Correspondent') ?></span>
        </div>
        <div class="flex flex-col md:flex-row gap-1 md:gap-4 text-xs">
            <span><i class="far fa-clock mr-1"></i> Published: <?= htmlspecialchars($formattedPublished) ?></span>
            <?php if ($formattedUpdated): ?>
                <span class="hidden md:inline text-gray-300">|</span>
                <span><i class="fas fa-sync-alt mr-1"></i> Updated: <?= htmlspecialchars($formattedUpdated) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Featured Image -->
    <figure class="w-full">
        <div class="relative aspect-video md:aspect-21/9 bg-gray-100 rounded-xl overflow-hidden shadow-sm border border-gray-100">
            <?php if (!empty($article['thumbnail'])): ?>
            <img src="<?= htmlspecialchars($article['thumbnail']) ?>"
                alt="<?= htmlspecialchars($article['alt_text'] ?? $article['title']) ?>"
                class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-400">
                <i class="fa-solid fa-image text-5xl"></i>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($article['alt_text'])): ?>
        <figcaption class="text-xs text-gray-500 italic text-center mt-3">
            <?= htmlspecialchars($article['alt_text']) ?>
        </figcaption>
        <?php endif; ?>
    </figure>

    <!-- Article Body -->
    <div class="prose max-w-none text-gray-800 leading-relaxed font-serif space-y-4">
        <?= $article['content'] // Raw HTML from DB/Editor ?>
    </div>

</article>

<hr class="my-8 border-gray-200">

<!-- Comments Section (Static Placeholder) -->
<section class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900 border-l-4 border-primary-600 pl-3">
            Total comments (0)
        </h3>
    </div>

    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="flex gap-4">
            <!-- User Avatar -->
            <div class="shrink-0">
                <div class="w-10 h-10 rounded-full bg-gray-300 overflow-hidden border border-gray-300">
                    <img src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?q=80&w=100&auto=format&fit=crop"
                        alt="User Avatar" class="w-full h-full object-cover opacity-60">
                </div>
            </div>

            <!-- Comment Input Area -->
            <div class="flex-1">
                <textarea
                    class="w-full bg-white border border-gray-300 rounded-md p-3 text-sm text-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none cursor-not-allowed"
                    rows="3" placeholder="Sign In to comment" disabled></textarea>
                <div class="flex justify-end mt-2">
                    <button
                        class="px-4 py-1.5 bg-primary-600 text-white text-xs font-bold rounded opacity-50 cursor-not-allowed"
                        disabled>
                        Post Comment
                    </button>
                </div>
            </div>
        </div>
    </div>

</section>
