<?php
/**
 * View: dashboard/profile_view.php
 * Displays the current authenticated user's profile details.
 */
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm p-6 md:p-8 min-h-[500px]">
        
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-neutral-900">My Profile</h2>
            <a href="<?= url('/dashboard/profile/edit') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-neutral-200 text-sm font-medium text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 transition-colors">
                <i class="fa-solid fa-user-pen"></i> Edit Profile
            </a>
        </div>
        
        <div class="flex items-center gap-6 mb-8">
            <div class="w-24 h-24 rounded-full flex items-center justify-center text-white font-bold text-3xl overflow-hidden shrink-0 shadow-sm" style="background: linear-gradient(135deg,#32C5AA,#008068);">
                <?php if(!empty($user['avatar_path'])): ?>
                    <img src="<?= htmlspecialchars(resolve_media_url($user['avatar_path'])) ?>" alt="Avatar" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= htmlspecialchars($userInitials) ?>
                <?php endif; ?>
            </div>
            <div>
                <h4 class="text-3xl font-bold text-neutral-900 tracking-tight"><?= htmlspecialchars($user['name']) ?></h4>
            </div>
        </div>
        
        <hr class="my-8 border-neutral-100">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label class="block text-sm font-semibold text-neutral-600 mb-2 uppercase tracking-wide">Email Address</label>
                <div class="px-4 py-3 bg-neutral-50 border border-neutral-200 text-neutral-800 rounded-xl">
                    <?= htmlspecialchars($user['email']) ?>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-neutral-600 mb-2 uppercase tracking-wide">Account Role</label>
                <div class="px-4 py-3 bg-neutral-50 border border-neutral-200 text-neutral-800 rounded-xl font-medium flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#00A486]"></span>
                    <?= htmlspecialchars(ucfirst($userRole)) ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-600 mb-2 uppercase tracking-wide">Date Joined</label>
                <div class="px-4 py-3 bg-neutral-50 border border-neutral-200 text-neutral-800 rounded-xl">
                    <?= date('F j, Y, g:i a', strtotime($user['created_at'])) ?>
                </div>
            </div>
        </div>

    </div>
</div>
