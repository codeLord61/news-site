<?php
/**
 * View: dashboard/editor_review_submission.php
 * Review page for a pending submission assigned to the current editor.
 */

$article = $article ?? [];
?>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a
        href="<?= url('/editor/pending-submissions') ?>"
        class="inline-flex items-center gap-2 text-sm font-medium text-body hover:text-heading"
    >
        <i class="fa-solid fa-arrow-left"></i>
        Back to Pending Submissions
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
            id="reviewApproveBtn"
            data-article-id="<?= (int)($article['id'] ?? 0) ?>"
            class="inline-flex items-center justify-center rounded-base text-xs font-medium px-4 py-2 text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200"
        >
            Approve
        </button>
    </div>
</div>

<article class="bg-neutral-primary-soft border border-default rounded-base shadow-xs">
    <header class="px-6 py-5 border-b border-default">
        <div class="flex items-center gap-2 mb-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-yellow-100 text-yellow-700">
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
    const approveBtn = document.getElementById('reviewApproveBtn');
    const rejectBtn = document.getElementById('reviewRejectBtn');
    const articleId = approveBtn?.getAttribute('data-article-id') || rejectBtn?.getAttribute('data-article-id');

    const submitDecision = async (action) => {
        if (!articleId) {
            alert('Missing article ID.');
            return;
        }

        const actionLabel = action === 'approve' ? 'approve' : 'reject';
        if (!window.confirm(`Are you sure you want to ${actionLabel} this submission?`)) {
            return;
        }

        const endpoint = action === 'approve'
            ? '<?= url('/editor/pending-submissions/approve') ?>'
            : '<?= url('/editor/pending-submissions/reject') ?>';

        [approveBtn, rejectBtn].forEach((btn) => {
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
                throw new Error(data.error || 'Failed to update submission.');
            }

            window.location.href = '<?= url('/editor/pending-submissions') ?>';
        } catch (error) {
            alert(error instanceof Error ? error.message : 'Failed to update submission.');
            [approveBtn, rejectBtn].forEach((btn) => {
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-not-allowed');
                }
            });
        }
    };

    if (approveBtn) {
        approveBtn.addEventListener('click', () => submitDecision('approve'));
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => submitDecision('reject'));
    }
});
</script>
