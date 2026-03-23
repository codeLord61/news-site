<?php
/**
 * View: dashboard/editor_all_articles.php
 * Editor queue of submitted articles.
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
    <h2 class="text-xl font-bold text-neutral-900">All Articles</h2>
</div>

<p class="mb-4 text-sm text-body">
    Select a submitted article to move it into your pending review queue.
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
                <th scope="col" class="px-6 py-3 font-medium">Submitted At</th>
                <th scope="col" class="px-6 py-3 font-medium text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="submittedQueueBody">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                    <tr class="odd:bg-neutral-primary even:bg-neutral-secondary-soft border-b border-default" data-article-row-id="<?= (int)$article['id'] ?>">
                        <td class="px-6 py-4">
                            <?php if (!empty($article['thumbnail'])): ?>
                                <img class="w-14 h-10 rounded-base object-cover shrink-0" src="<?= htmlspecialchars($article['thumbnail']) ?>" alt="Article thumbnail">
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
                            <?= !empty($article['created_at']) ? date('M d, Y', strtotime((string)$article['created_at'])) : '-' ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                                <button
                                    type="button"
                                    class="select-submission-btn text-white bg-blue-600 box-border border border-transparent hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 shadow-xs font-medium leading-5 rounded-base text-xs px-3 py-2 focus:outline-none"
                                    data-article-id="<?= (int)$article['id'] ?>"
                                >
                                    Select
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="bg-neutral-primary" id="submittedQueueEmptyRow">
                    <td colspan="8" class="px-6 py-10 text-center text-body">
                        No submitted articles are waiting for selection.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="fixed bottom-5 right-5 z-50">
    <div id="editor-all-articles-toast" class="hidden items-center w-full max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-xs border border-default" role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-fg-success bg-success-soft rounded">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
            </svg>
            <span class="sr-only">Success icon</span>
        </div>
        <div class="ms-3 text-sm font-normal" id="editor-all-articles-toast-message">Article selected successfully.</div>
        <button type="button" class="ms-auto flex items-center justify-center text-body hover:text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded text-sm h-8 w-8 focus:outline-none" data-dismiss-target="#editor-all-articles-toast" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('submittedQueueBody');
    const toastEl = document.getElementById('editor-all-articles-toast');
    const toastMessageEl = document.getElementById('editor-all-articles-toast-message');
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

        if (document.getElementById('submittedQueueEmptyRow')) {
            return;
        }

        const emptyRow = document.createElement('tr');
        emptyRow.id = 'submittedQueueEmptyRow';
        emptyRow.className = 'bg-neutral-primary';
        emptyRow.innerHTML = '<td colspan="8" class="px-6 py-10 text-center text-body">No submitted articles are waiting for selection.</td>';
        tbody.appendChild(emptyRow);
    };

    document.querySelectorAll('.select-submission-btn').forEach((button) => {
        button.addEventListener('click', async () => {
            const articleId = button.getAttribute('data-article-id');
            if (!articleId) {
                return;
            }

            button.disabled = true;
            button.classList.add('opacity-70', 'cursor-not-allowed');

            try {
                const response = await fetch('<?= url('/editor/articles/select') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ article_id: Number(articleId) })
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Failed to select article.');
                }

                const row = button.closest('tr[data-article-row-id]');
                if (row) {
                    row.remove();
                }

                ensureEmptyState();
                showToast(data.message || 'Article selected successfully.');
            } catch (error) {
                alert(error instanceof Error ? error.message : 'Failed to select article.');
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        });
    });
});
</script>
