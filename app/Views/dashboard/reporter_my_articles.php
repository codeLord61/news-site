<?php
/**
 * View: dashboard/reporter_my_articles.php
 * Reporter article list page.
 */

$articles = $articles ?? [];
$hideDraftHint = (bool)($hideDraftHint ?? false);

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
    <h2 class="text-xl font-bold text-neutral-900">My Articles</h2>
</div>

<?php if (!$hideDraftHint): ?>
    <p class="mb-4 text-sm text-body">
        Tip: Drafts stay private until you submit them for editorial review.
    </p>
<?php endif; ?>

<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
    <table class="w-full text-sm text-left rtl:text-right text-body">
        <thead class="text-sm text-body bg-neutral-secondary-soft border-b border-default">
            <tr>
                <th scope="col" class="px-6 py-3 font-medium">Thumbnail</th>
                <th scope="col" class="px-6 py-3 font-medium">Title</th>
                <th scope="col" class="px-6 py-3 font-medium">Category</th>
                <th scope="col" class="px-6 py-3 font-medium">Excerpt</th>
                <th scope="col" class="px-6 py-3 font-medium">Slug</th>
                <th scope="col" class="px-6 py-3 font-medium">Status</th>
                <th scope="col" class="px-6 py-3 font-medium">Created At</th>
                <th scope="col" class="px-6 py-3 font-medium">Updated At</th>
                <th scope="col" class="px-6 py-3 font-medium text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="reporterArticlesBody">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                    <tr class="odd:bg-neutral-primary even:bg-neutral-secondary-soft border-b border-default" data-article-row-id="<?= (int)$article['id'] ?>">
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
                            <?= htmlspecialchars((string)($article['category_name'] ?? '-')) ?>
                        </td>
                        <td class="px-6 py-4 max-w-[260px]">
                            <span><?= htmlspecialchars($truncate($article['excerpt'] ?? '')) ?></span>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs">
                            <?= htmlspecialchars((string)$article['slug']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= $statusBadgeClass((string)($article['status'] ?? '')) ?>">
                                <?= ucfirst(htmlspecialchars((string)($article['status'] ?? 'unknown'))) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= !empty($article['created_at']) ? date('M d, Y', strtotime((string)$article['created_at'])) : '-' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= !empty($article['updated_at']) ? date('M d, Y', strtotime((string)$article['updated_at'])) : '-' ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <?php if (($article['status'] ?? '') === 'published'): ?>
                                    <a
                                        href="<?= url('/articles/' . rawurlencode((string)$article['slug'])) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-base text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                        title="View Published Article"
                                        aria-label="View Published Article"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                <?php else: ?>
                                    <a
                                        href="<?= url('/my-articles/' . (int)$article['id'] . '/preview') ?>"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-base text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                        title="View Article in Dashboard"
                                        aria-label="View Article in Dashboard"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                <?php endif; ?>

                                <a
                                    href="<?= url('/articles/new') ?>?article_id=<?= (int)$article['id'] ?>"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-base text-[#008068] hover:bg-[#E5F7F4] focus:outline-none focus:ring-4 focus:ring-[#c8f0e8]"
                                    title="Edit Article"
                                    aria-label="Edit Article"
                                >
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>

                                <button
                                    type="button"
                                    class="delete-article-btn inline-flex items-center justify-center w-9 h-9 rounded-base text-red-600 hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100"
                                    title="Delete Article"
                                    aria-label="Delete Article"
                                    data-article-id="<?= (int)$article['id'] ?>"
                                >
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="bg-neutral-primary" id="reporterArticlesEmptyRow">
                    <td colspan="9" class="px-6 py-10 text-center text-body">
                        No articles found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="fixed bottom-5 right-5 z-50">
    <div id="reporter-articles-toast" class="hidden items-center w-full max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-xs border border-default" role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-fg-success bg-success-soft rounded">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
            </svg>
            <span class="sr-only">Success icon</span>
        </div>
        <div class="ms-3 text-sm font-normal" id="reporter-articles-toast-message">Article deleted successfully.</div>
        <button type="button" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none" data-dismiss-target="#reporter-articles-toast" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('reporterArticlesBody');
    const toastEl = document.getElementById('reporter-articles-toast');
    const toastMessageEl = document.getElementById('reporter-articles-toast-message');
    let toastTimeout;

    const showToast = (message) => {
        toastMessageEl.textContent = message;
        toastEl.classList.remove('hidden');
        toastEl.classList.add('flex');

        clearTimeout(toastTimeout);
        toastTimeout = window.setTimeout(() => {
            toastEl.classList.add('hidden');
            toastEl.classList.remove('flex');
        }, 3000);
    };

    const ensureEmptyState = () => {
        if (!tbody) {
            return;
        }

        const rows = tbody.querySelectorAll('tr[data-article-row-id]');
        if (rows.length > 0) {
            return;
        }

        if (document.getElementById('reporterArticlesEmptyRow')) {
            return;
        }

        const emptyRow = document.createElement('tr');
        emptyRow.id = 'reporterArticlesEmptyRow';
        emptyRow.className = 'bg-neutral-primary';
        emptyRow.innerHTML = '<td colspan="9" class="px-6 py-10 text-center text-body">No articles found.</td>';
        tbody.appendChild(emptyRow);
    };

    document.querySelectorAll('.delete-article-btn').forEach((button) => {
        button.addEventListener('click', async () => {
            const articleId = button.getAttribute('data-article-id');
            if (!articleId) {
                return;
            }

            if (!window.confirm('Are you sure you want to delete this article?')) {
                return;
            }

            button.disabled = true;
            button.classList.add('opacity-70', 'cursor-not-allowed');

            try {
                const response = await fetch('<?= url('/api/v1/reporter/articles/delete') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ article_id: Number(articleId) }),
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Failed to delete article.');
                }

                const row = button.closest('tr[data-article-row-id]');
                if (row) {
                    row.remove();
                }

                ensureEmptyState();
                showToast(data.message || 'Article deleted successfully.');
            } catch (error) {
                alert(error instanceof Error ? error.message : 'Failed to delete article.');
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        });
    });
});
</script>
