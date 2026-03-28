<?php
/**
 * View: dashboard/editor_published_articles.php
 * Published articles assigned to the current editor.
 */

$articles = $articles ?? [];

$truncate = static function (?string $text, int $limit = 90): string {
    $text = trim((string)$text);
    if ($text === '') {
        return '-';
    }

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $limit, '...');
    }

    return strlen($text) > $limit ? substr($text, 0, $limit - 3) . '...' : $text;
};
?>

<div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-bold text-neutral-900">Published Articles</h2>
</div>

<p class="mb-4 text-sm text-body">
    These are published articles handled by you. View opens the public page as readers see it.
</p>

<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
    <table class="w-full text-sm text-left rtl:text-right text-body">
        <thead class="text-sm text-body bg-neutral-secondary-soft border-b border-default">
            <tr>
                <th scope="col" class="px-6 py-3 font-medium">Thumbnail</th>
                <th scope="col" class="px-6 py-3 font-medium">Title</th>
                <th scope="col" class="px-6 py-3 font-medium">Reporter</th>
                <th scope="col" class="px-6 py-3 font-medium">Category</th>
                <th scope="col" class="px-6 py-3 font-medium">Excerpt</th>
                <th scope="col" class="px-6 py-3 font-medium">Slug</th>
                <th scope="col" class="px-6 py-3 font-medium">Published At</th>
                <th scope="col" class="px-6 py-3 font-medium text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                    <tr class="odd:bg-neutral-primary even:bg-neutral-secondary-soft border-b border-default">
                        <td class="px-6 py-4">
                            <?php if (!empty($article['thumbnail'])): ?>
                                <img class="w-14 h-10 rounded-base object-cover shrink-0" src="<?= htmlspecialchars((string)$article['thumbnail']) ?>" alt="Article thumbnail">
                            <?php else: ?>
                                <div class="inline-flex items-center justify-center w-14 h-10 rounded-base shrink-0 bg-neutral-tertiary text-body">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            <?= htmlspecialchars((string)$article['title']) ?>
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= htmlspecialchars((string)($article['reporter_name'] ?? '-')) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= htmlspecialchars((string)($article['category_name'] ?? '-')) ?>
                        </td>
                        <td class="px-6 py-4 max-w-[260px]">
                            <?= htmlspecialchars($truncate($article['excerpt'] ?? '')) ?>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs">
                            <?= htmlspecialchars((string)$article['slug']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= !empty($article['published_at']) ? date('M d, Y', strtotime((string)$article['published_at'])) : '-' ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                <a
                                    href="<?= url('/articles/' . rawurlencode((string)$article['slug'])) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center rounded-base text-xs font-medium px-3 py-2 border border-default text-body hover:bg-neutral-tertiary"
                                >
                                    View
                                </a>
                                <a
                                    href=""
                                    class="inline-flex items-center justify-center rounded-base text-xs font-medium px-3 py-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200"
                                >
                                    Revise
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="bg-neutral-primary">
                    <td colspan="8" class="px-6 py-10 text-center text-body">
                        No published articles found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
