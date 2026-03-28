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
        <div class="flex flex-col md:flex-row gap-1 md:gap-4 text-xs flex-1">
            <span><i class="far fa-clock mr-1"></i> Published: <?= htmlspecialchars($formattedPublished) ?></span>
            <?php if ($formattedUpdated): ?>
                <span class="hidden md:inline text-gray-300">|</span>
                <span><i class="fas fa-sync-alt mr-1"></i> Updated: <?= htmlspecialchars($formattedUpdated) ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Bookmark Icon -->
        <div class="flex items-center mt-2 md:mt-0">
            <button onclick="toggleBookmark(<?= $article['id'] ?>)" class="text-primary-600 hover:text-primary-700 transition" title="Bookmark this article">
                <i id="bookmarkIcon" class="<?= (!empty($isBookmarked)) ? 'fas' : 'far' ?> fa-bookmark text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Featured Image -->
    <figure class="w-full">
        <div class="relative aspect-video md:aspect-21/9 bg-gray-100 overflow-hidden shadow-sm border border-gray-100">
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
    <div class="prose max-w-none text-gray-800 leading-relaxed font-serif">
        <?= $article['content'] // Raw HTML from DB/Editor ?>
    </div>

</article>

<hr class="my-8 border-gray-200">

<?php
$commentsCount = count($comments ?? []);
?>
<!-- Comments Section -->
<section class="max-w-2xl mx-auto mb-16">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900 border-l-4 border-primary-600 pl-3">
            Total comments (<?= $commentsCount ?>)
        </h3>
    </div>

    <!-- Comment Form -->
    <div class="bg-gray-50 p-4 border border-gray-200 mb-8">
        <div class="flex gap-4">
            <?php if (!empty($currentUser)): ?>
                <!-- Logged In User Avatar -->
                <div class="shrink-0">
                    <div class="w-10 h-10 bg-primary-100 text-primary-600 flex items-center justify-center font-bold overflow-hidden border border-gray-300">
                        <?php if (!empty($currentUser['avatar_path'])): ?>
                            <img src="<?= htmlspecialchars(resolve_media_url($currentUser['avatar_path'])) ?>" alt="User Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fa-solid fa-user"></i>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Comment Input Area Active -->
                <div class="flex-1">
                    <form action="<?= url('/comments/store') ?>" method="POST">
                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                        <input type="hidden" name="article_slug" value="<?= htmlspecialchars($article['slug']) ?>">
                        <textarea
                            name="content"
                            class="w-full bg-white border border-gray-300 p-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-primary-400 resize-y"
                            rows="3" placeholder="Write a comment..." required></textarea>
                        <div class="flex justify-end mt-2">
                            <button
                                type="submit"
                                class="px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold transition-colors">
                                Post Comment
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Guest Avatar -->
                <div class="shrink-0">
                    <div class="w-10 h-10 bg-gray-200 text-gray-400 flex items-center justify-center font-bold overflow-hidden border border-gray-300">
                        <i class="fa-solid fa-user text-xl"></i>
                    </div>
                </div>

                <!-- Comment Input Area Disabled -->
                <div class="flex-1">
                    <textarea
                        class="w-full bg-white border border-gray-300 p-3 text-sm text-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none cursor-not-allowed"
                        rows="3" placeholder="Sign In to comment" disabled></textarea>
                    <div class="flex justify-end mt-2">
                        <button class="px-4 py-1.5 bg-primary-600 text-white text-xs font-bold opacity-50 cursor-not-allowed" disabled>
                            Post Comment
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-6">
        <?php foreach (($comments ?? []) as $comment): ?>
            <div class="flex gap-4">
                <div class="shrink-0">
                    <div class="w-10 h-10 bg-primary-50 text-primary-600 flex items-center justify-center font-bold overflow-hidden border border-gray-200 rounded-full">
                        <?php if (!empty($comment['user_avatar'])): ?>
                            <img src="<?= htmlspecialchars(resolve_media_url($comment['user_avatar'])) ?>" alt="User Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fa-solid fa-user"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex-1 bg-white border border-gray-100 p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($comment['user_name']) ?></h4>
                        <span class="text-xs text-gray-500"><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></span>
                    </div>
                    <p class="text-gray-700 text-sm">
                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</section>

<script>
function toggleBookmark(articleId) {
    <?php if (empty($currentUser)): ?>
    alert('Please sign in to bookmark articles.');
    return;
    <?php endif; ?>

    fetch('<?= url('/bookmarks/toggle') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ article_id: articleId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = document.getElementById('bookmarkIcon');
            if (data.bookmarked) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        } else {
            alert(data.message || 'Error toggling bookmark.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}
</script>
