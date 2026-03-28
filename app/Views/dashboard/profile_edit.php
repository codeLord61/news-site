<?php
/**
 * View: dashboard/profile_edit.php
 * Form interface for user to update name and avatar via dashboard.
 */
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white border border-neutral-200 rounded-2xl shadow-sm p-6 md:p-8 min-h-[500px]">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-neutral-900">Edit Profile</h2>
            <a href="<?= url('/dashboard/profile') ?>" class="text-sm font-medium text-neutral-500 hover:text-neutral-900 underline transition-colors">
                Cancel
            </a>
        </div>
        
        <form action="<?= url('/dashboard/profile/update') ?>" method="POST" enctype="multipart/form-data" class="space-y-8 max-w-xl">
            
            <!-- Avatar Upload -->
            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-3 uppercase tracking-wide">Profile Image</label>
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 rounded-full bg-neutral-100 flex items-center justify-center overflow-hidden shrink-0 shadow-sm border border-neutral-200">
                        <?php if(!empty($user['avatar_path'])): ?>
                            <img src="<?= htmlspecialchars(resolve_media_url($user['avatar_path'])) ?>" alt="Avatar" class="w-full h-full object-cover" id="avatarPreview">
                        <?php else: ?>
                            <i class="fa-solid fa-user text-neutral-400 text-2xl" id="avatarPlaceholder"></i>
                            <img src="" alt="Avatar" class="w-full h-full object-cover hidden" id="avatarPreview">
                        <?php endif; ?>
                    </div>
                    
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="text-sm text-neutral-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[13px] file:font-semibold file:bg-[#E5F7F4] file:text-[#00A486] hover:file:bg-[#ccf1ea] transition-all cursor-pointer" onchange="previewAvatar(event)">
                </div>
            </div>
            
            <!-- Full Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-neutral-700 mb-2 uppercase tracking-wide">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full border border-neutral-300 rounded-xl shadow-sm focus:border-[#008068] focus:ring focus:ring-[#00A486]/20 px-4 py-3 text-neutral-900 transition-colors">
            </div>
            
            <!-- Actions -->
            <div class="pt-4 border-t border-neutral-100">
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-bold text-white transition-all shadow-md hover:shadow-lg hover:-translate-y-px" style="background: linear-gradient(135deg,#00B795,#008068);">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function previewAvatar(event) {
        if(event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                const placeholder = document.getElementById('avatarPlaceholder');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if(placeholder) placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
