<?php
/**
 * View: dashboard/editor_review_submission.php
 * Review page for a pending submission assigned to the current editor.
 */

$article = $article ?? [];
$reviewMode = (($reviewMode ?? 'pending') === 'approved') ? 'approved' : 'pending';
$backPath = (string)($backPath ?? '/editor/pending-submissions');
$primaryAction = (string)($primaryAction ?? ($reviewMode === 'approved' ? 'publish' : 'approve'));
$backLabel = $reviewMode === 'approved' ? 'Back to Approved Articles' : 'Back to Pending Submissions';
$primaryLabel = $primaryAction === 'publish' ? 'Publish' : 'Approve';
$statusBadgeClass = $reviewMode === 'approved'
    ? 'bg-emerald-100 text-emerald-700'
    : 'bg-yellow-100 text-yellow-700';
?>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a
        href="<?= url($backPath) ?>"
        class="inline-flex items-center gap-2 text-sm font-medium text-body hover:text-heading"
    >
        <i class="fa-solid fa-arrow-left"></i>
        <?= htmlspecialchars($backLabel) ?>
    </a>

    <div class="flex items-center gap-2">
        <button
            type="button"
            id="reviewRejectBtn"
            data-article-id="<?= (int)($article['id'] ?? 0) ?>"
            class="inline-flex items-center justify-center rounded-base text-xs font-medium px-4 py-2 text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-200"
        >
            Reject
        </button>
        <button
            type="button"
            id="reviewPrimaryBtn"
            data-action="<?= htmlspecialchars($primaryAction) ?>"
            data-article-id="<?= (int)($article['id'] ?? 0) ?>"
            class="inline-flex items-center justify-center rounded-base text-xs font-medium px-4 py-2 text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200"
        >
            <?= htmlspecialchars($primaryLabel) ?>
        </button>
    </div>
</div>

<article class="bg-neutral-primary-soft border border-default rounded-base shadow-xs">
    <header class="px-6 py-5 border-b border-default">
        <div class="flex items-center gap-2 mb-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= htmlspecialchars($statusBadgeClass) ?>">
                <?= ucfirst(htmlspecialchars((string)($article['status'] ?? 'pending'))) ?>
            </span>
            <span class="text-xs text-body">Reporter: <?= htmlspecialchars((string)($article['reporter_name'] ?? '-')) ?></span>
        </div>

        <h2 class="text-2xl font-bold text-heading mb-2">
            <?= htmlspecialchars((string)($article['title'] ?? 'Untitled')) ?>
        </h2>

        <div class="flex flex-wrap items-center gap-4 text-xs text-body">
            <span><strong>Slug:</strong> <span class="font-mono"><?= htmlspecialchars((string)($article['slug'] ?? '-')) ?></span></span>
            <span><strong>Created:</strong> <?= !empty($article['created_at']) ? date('M d, Y H:i', strtotime((string)$article['created_at'])) : '-' ?></span>
            <span><strong>Updated:</strong> <?= !empty($article['updated_at']) ? date('M d, Y H:i', strtotime((string)$article['updated_at'])) : '-' ?></span>
            <span><strong>Category:</strong> <?= htmlspecialchars((string)($article['category_name'] ?? '-')) ?></span>
            <?php if ($reviewMode === 'approved'): ?>
                <span><strong>Approved At:</strong> <?= !empty($article['approved_at']) ? date('M d, Y H:i', strtotime((string)$article['approved_at'])) : '-' ?></span>
            <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const primaryBtn = document.getElementById('reviewPrimaryBtn');
    const rejectBtn = document.getElementById('reviewRejectBtn');
    const articleId = primaryBtn?.getAttribute('data-article-id') || rejectBtn?.getAttribute('data-article-id');
    const reviewMode = '<?= $reviewMode ?>';

    const submitDecision = async (action) => {
        if (!articleId) {
            alert('Missing article ID.');
            return;
        }

        const actionLabel = action === 'publish'
            ? 'publish'
            : (action === 'approve' ? 'approve' : 'reject');
        if (!window.confirm(`Are you sure you want to ${actionLabel} this article?`)) {
            return;
        }

        let endpoint = '';
        if (action === 'approve') {
            endpoint = '<?= url('/editor/pending-submissions/approve') ?>';
        } else if (action === 'publish') {
            endpoint = '<?= url('/editor/approved-articles/publish') ?>';
        } else {
            endpoint = reviewMode === 'approved'
                ? '<?= url('/editor/approved-articles/reject') ?>'
                : '<?= url('/editor/pending-submissions/reject') ?>';
        }

        [primaryBtn, rejectBtn].forEach((btn) => {
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
            }
        });

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ article_id: Number(articleId) }),
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                throw new Error(data.error || 'Failed to update article.');
            }

            window.location.href = '<?= url($backPath) ?>';
        } catch (error) {
            alert(error instanceof Error ? error.message : 'Failed to update article.');
            [primaryBtn, rejectBtn].forEach((btn) => {
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-not-allowed');
                }
            });
        }
    };

    if (primaryBtn) {
        const action = primaryBtn.getAttribute('data-action') || 'approve';
        primaryBtn.addEventListener('click', () => submitDecision(action));
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => submitDecision('reject'));
    }
});
</script>
