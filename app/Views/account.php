<main class="w-full max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Left Panel: Navigation -->
        <aside class="w-full md:w-64 shrink-0">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-2">My Account</h2>
            <nav class="flex flex-col gap-2" id="accountNav">
                <button onclick="switchTab('profile')" id="nav-profile" class="text-left px-4 py-2 font-medium bg-primary-50 text-primary-600 rounded-md transition-colors relative">
                    <i class="fa-solid fa-user mr-2"></i> My profile
                </button>
                <button onclick="switchTab('edit')" id="nav-edit" class="text-left px-4 py-2 font-medium text-gray-600 hover:bg-gray-50 rounded-md transition-colors relative">
                    <i class="fa-solid fa-pen-to-square mr-2"></i> Edit profile
                </button>
                <button onclick="switchTab('comments')" id="nav-comments" class="text-left px-4 py-2 font-medium text-gray-600 hover:bg-gray-50 rounded-md transition-colors relative">
                    <i class="fa-solid fa-comments mr-2"></i> My Comments
                </button>
                <button onclick="switchTab('bookmarks')" id="nav-bookmarks" class="text-left px-4 py-2 font-medium text-gray-600 hover:bg-gray-50 rounded-md transition-colors relative">
                    <i class="fa-solid fa-bookmark mr-2"></i> My bookmarks
                </button>
            </nav>
        </aside>

        <!-- Right Panel: Content Area -->
        <div class="flex-1 bg-white border border-gray-200 shadow-sm p-6 md:p-8 min-h-125">
            
            <!-- My Profile Section -->
            <section id="tab-profile" class="block">
                <h3 class="text-xl font-bold text-gray-900 mb-6">My Profile</h3>
                
                <div class="flex items-center gap-6 mb-8">
                    <div class="w-24 h-24 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-3xl overflow-hidden shrink-0">
                        <?php if(!empty($user['avatar_path'])): ?>
                            <img src="<?= htmlspecialchars(resolve_media_url($user['avatar_path'])) ?>" alt="Avatar" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fa-solid fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-primary-600"><?= htmlspecialchars($user['name']) ?></h4>
                    </div>
                </div>
                
                <hr class="my-6 border-gray-100">
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 text-gray-700 rounded-md inline-block min-w-75">
                        <?= htmlspecialchars($user['email']) ?>
                    </div>
                </div>
            </section>

            <!-- Edit Profile Section -->
            <section id="tab-edit" class="hidden">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Edit Profile</h3>
                
                <form action="<?= url('/my-account/update') ?>" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-md">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden shrink-0 border border-gray-200">
                                <?php if(!empty($user['avatar_path'])): ?>
                                    <img src="<?= htmlspecialchars(resolve_media_url($user['avatar_path'])) ?>" alt="Avatar" class="w-full h-full object-cover" id="avatarPreview">
                                <?php else: ?>
                                    <i class="fa-solid fa-user text-gray-400" id="avatarPlaceholder"></i>
                                    <img src="" alt="Avatar" class="w-full h-full object-cover hidden" id="avatarPreview">
                                <?php endif; ?>
                            </div>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-colors cursor-pointer" onchange="previewAvatar(event)">
                        </div>
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 p-2 border">
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-6 shadow-sm transition-colors rounded-md">
                            Update
                        </button>
                    </div>
                </form>
            </section>

            <!-- My Comments Section -->
            <section id="tab-comments" class="hidden">
                <h3 class="text-xl font-bold text-gray-900 mb-6">My Comments</h3>
                
                <?php if (empty($comments)): ?>
                    <p class="text-gray-500 italic">You haven't posted any comments yet.</p>
                <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach($comments as $comment): ?>
                        <div class="border border-gray-100 bg-gray-50 p-4 flex flex-col md:flex-row gap-4">
                            <div class="shrink-0">
                                <?php if(!empty($comment['article_thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars(resolve_media_url($comment['article_thumbnail'])) ?>" alt="Thumbnail" class="w-32 h-20 object-cover border border-gray-200 bg-white">
                                <?php else: ?>
                                    <div class="w-32 h-20 bg-gray-200 border border-gray-200 flex items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-2">
                                    <a href="<?= url('/articles/' . $comment['article_slug']) ?>" class="hover:text-primary-600 transition-colors">
                                        <?= htmlspecialchars($comment['article_title']) ?>
                                    </a>
                                </h4>
                                <p class="text-gray-700 text-sm mb-3">
                                    <?= nl2br(htmlspecialchars($comment['content'])) ?>
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-500 mt-auto pt-2 border-t border-gray-200 border-dashed">
                                    <span><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></span>
                                    <a href="<?= url('/articles/' . $comment['article_slug']) ?>" class="text-primary-600 hover:underline font-bold">
                                        View article &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- My Bookmarks Section -->
            <section id="tab-bookmarks" class="hidden">
                <h3 class="text-xl font-bold text-gray-900 mb-6">My bookmarks</h3>
                
                <p id="no-bookmarks-msg" class="text-gray-500 italic <?= !empty($bookmarks) ? 'hidden' : '' ?>">You haven't bookmarked any articles yet.</p>

                <div id="bookmarks-list-container" class="space-y-6 <?= empty($bookmarks) ? 'hidden' : '' ?>">
                    <?php foreach(($bookmarks ?? []) as $bookmark): ?>
                    <div class="bookmark-card border border-gray-100 bg-gray-50 p-4 flex flex-col md:flex-row gap-4 relative pr-10">
                        <button onclick="removeBookmarkCard(<?= $bookmark['article_id'] ?>, this)" class="absolute top-4 right-4 text-primary-600 hover:text-primary-700 transition" title="Remove Bookmark">
                            <i class="fa-solid fa-bookmark text-xl"></i>
                        </button>
                        <div class="shrink-0">
                            <?php if(!empty($bookmark['article_thumbnail'])): ?>
                                <img src="<?= htmlspecialchars(resolve_media_url($bookmark['article_thumbnail'])) ?>" alt="Thumbnail" class="w-32 h-20 object-cover border border-gray-200 bg-white">
                            <?php else: ?>
                                <div class="w-32 h-20 bg-gray-200 border border-gray-200 flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 mb-2">
                                <a href="<?= url('/articles/' . $bookmark['article_slug']) ?>" class="hover:text-primary-600 transition-colors">
                                    <?= htmlspecialchars($bookmark['article_title']) ?>
                                </a>
                            </h4>
                            <p class="text-gray-700 text-sm mb-3">
                                <?= htmlspecialchars(mb_substr(strip_tags($bookmark['article_excerpt'] ?? ''), 0, 150)) . '...' ?>
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500 mt-auto pt-2 border-t border-gray-200 border-dashed">
                                <span>Published: <?= date('M j, Y g:i A', strtotime($bookmark['published_at'])) ?></span>
                                <a href="<?= url('/articles/' . $bookmark['article_slug']) ?>" class="text-primary-600 hover:underline font-bold">
                                    View article &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>
    </div>
</main>

<script>
    function switchTab(tabId) {
        // Hide all tabs
        document.getElementById('tab-profile').classList.add('hidden');
        document.getElementById('tab-edit').classList.add('hidden');
        document.getElementById('tab-comments').classList.add('hidden');
        document.getElementById('tab-bookmarks').classList.add('hidden');
        
        // Remove active styles from nav
        const navs = ['profile', 'edit', 'comments', 'bookmarks'];
        navs.forEach(nav => {
            const el = document.getElementById('nav-' + nav);
            el.classList.remove('bg-primary-50', 'text-primary-600');
            el.classList.add('text-gray-600', 'hover:bg-gray-50');
        });
        
        // Show target tab
        document.getElementById('tab-' + tabId).classList.remove('hidden');
        document.getElementById('tab-' + tabId).classList.add('block');
        
        // Add active styles to target nav
        const activeNav = document.getElementById('nav-' + tabId);
        activeNav.classList.remove('text-gray-600', 'hover:bg-gray-50');
        activeNav.classList.add('bg-primary-50', 'text-primary-600');
    }

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

    function removeBookmarkCard(articleId, buttonEl) {
        if (!confirm("Remove this bookmark?")) return;
        
        fetch('<?= url('/bookmarks/toggle') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ article_id: articleId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && !data.bookmarked) {
                // remove the card
                const card = buttonEl.closest('.bookmark-card');
                if (card) {
                    card.remove();
                    // if no cards left, show empty message
                    const container = document.getElementById('bookmarks-list-container');
                    if (container && container.children.length === 0) {
                        const noBookmarksMsg = document.getElementById('no-bookmarks-msg');
                        if(noBookmarksMsg) noBookmarksMsg.classList.remove('hidden');
                        container.classList.add('hidden');
                    }
                }
            } else {
                alert(data.message || 'Error removing bookmark.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
</script>
