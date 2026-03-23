<?php
/**
 * View: dashboard/reporter_article_preview.php
 * Reporter dashboard preview for a single article.
 */

$article = $article ?? [];

$statusBadgeClass = static function (string $status): string {
    return match (strtolower($status)) {
        'draft' => 'bg-[#E5F7F4] text-[#008068]',
        'submitted' => 'bg-blue-100 text-blue-700',
        'pending' => 'bg-yellow-100 text-yellow-700',
        'approved' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-red-100 text-red-700',
        'published' => 'bg-indigo-100 text-indigo-700',
        default => 'bg-neutral-tertiary text-body',
    };
};
?>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a
        href="<?= url('/my-articles') ?>"
        class="inline-flex items-center gap-2 text-sm font-medium text-body hover:text-heading"
    >
        <i class="fa-solid fa-arrow-left"></i>
        Back to My Articles
    </a>

    <div class="flex items-center gap-2">
        <?php if (($article['status'] ?? '') === 'published'): ?>
            <a
                href="<?= url('/articles/' . rawurlencode((string)($article['slug'] ?? ''))) ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center rounded-base text-xs font-medium px-4 py-2 border border-default text-body hover:bg-neutral-tertiary"
            >
                Open Public Page
            </a>
        <?php endif; ?>
        <a
            href="<?= url('/articles/new') ?>?article_id=<?= (int)($article['id'] ?? 0) ?>"
            class="inline-flex items-center justify-center rounded-base text-xs font-medium px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200"
        >
            Edit Article
        </a>
    </div>
</div>

<article class="bg-neutral-primary-soft border border-default rounded-base shadow-xs">
    <header class="px-6 py-5 border-b border-default">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= $statusBadgeClass((string)($article['status'] ?? '')) ?>">
                <?= ucfirst(htmlspecialchars((string)($article['status'] ?? 'unknown'))) ?>
            </span>
            <?php if (!empty($article['category_name'])): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-neutral-tertiary text-body">
                    <?= htmlspecialchars((string)$article['category_name']) ?>
                </span>
            <?php endif; ?>
            <?php if (!empty($article['tag_names'])): ?>
                <span class="text-xs text-body">Tags: <?= htmlspecialchars((string)$article['tag_names']) ?></span>
            <?php endif; ?>
        </div>

        <h2 class="text-2xl font-bold text-heading mb-2">
            <?= htmlspecialchars((string)($article['title'] ?? 'Untitled')) ?>
        </h2>

        <div class="flex flex-wrap items-center gap-4 text-xs text-body">
            <span><strong>Slug:</strong> <span class="font-mono"><?= htmlspecialchars((string)($article['slug'] ?? '-')) ?></span></span>
            <span><strong>Created:</strong> <?= !empty($article['created_at']) ? date('M d, Y H:i', strtotime((string)$article['created_at'])) : '-' ?></span>
            <span><strong>Updated:</strong> <?= !empty($article['updated_at']) ? date('M d, Y H:i', strtotime((string)$article['updated_at'])) : '-' ?></span>
        </div>
    </header>

    <div class="p-6">
        <?php if (!empty($article['thumbnail'])): ?>
            <img
                src="<?= htmlspecialchars((string)$article['thumbnail']) ?>"
                alt="Article thumbnail"
                class="w-full max-h-[360px] object-cover rounded-base border border-default mb-5"
            >
        <?php endif; ?>

        <?php if (!empty($article['excerpt'])): ?>
            <p class="mb-5 text-sm text-body">
                <strong>Excerpt:</strong> <?= htmlspecialchars((string)$article['excerpt']) ?>
            </p>
        <?php endif; ?>

        <div class="prose max-w-none text-heading">
            <?= (string)($article['content'] ?? '') ?>
        </div>
    </div>
</article>
