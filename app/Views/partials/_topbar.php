<?php
/**
 * Partial: _topbar.php
 * Shared topbar for the dashboard layout.
 */
$userName     = $userName     ?? 'John Doe';
$userInitials = $userInitials ?? 'JD';
$userEmail    = $userEmail    ?? 'user@packly.news';
$userRole     = $userRole     ?? 'Reporter';
$pageTitle    = $pageTitle    ?? 'Dashboard';
$pageSubtitle = $pageSubtitle ?? '';
?>

<!-- ════════════════════ TOPBAR ════════════════════ -->
<header id="topbar" role="banner"
    class="sticky top-0 z-30 flex items-center justify-between gap-4 px-6 bg-white border-b border-neutral-200"
    style="height:68px;">

    <!-- Left: hamburger + page title -->
    <div class="flex items-center gap-3.5 flex-1 min-w-0">
        <button id="hamburger-btn" type="button" aria-label="Toggle sidebar" aria-expanded="false"
            class="flex-shrink-0 w-9 h-9 rounded-[9px] border border-neutral-200 bg-white flex items-center justify-center text-neutral-600 hover:bg-[#E5F7F4] hover:text-[#00A486] hover:border-[#99E2D4] transition-colors duration-150">
            <i id="hamburger-icon" class="fa-solid fa-bars text-sm"></i>
        </button>

        <div class="min-w-0">
            <h1 id="topbar-title" class="text-[17px] font-bold text-neutral-900 leading-tight truncate">
                <?= htmlspecialchars($pageTitle) ?>
            </h1>
            <?php if ($pageSubtitle): ?>
            <p class="hidden sm:block text-xs text-neutral-400 mt-0.5 truncate">
                <?= htmlspecialchars($pageSubtitle) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: notifications + profile -->
    <div class="flex items-center gap-2 flex-shrink-0">

        <!-- ── Notification bell ── -->
        <div class="relative">
            <button id="notif-btn" type="button" aria-label="Notifications" aria-expanded="false"
                class="relative w-10 h-10 rounded-[10px] border border-neutral-200 bg-white flex items-center justify-center text-neutral-500 hover:bg-[#E5F7F4] hover:text-[#00A486] hover:border-[#99E2D4] transition-colors duration-150">
                <i class="fa-regular fa-bell text-base"></i>

                <!-- Dynamic unread badge -->
                <span id="unread-badge"
                    class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white shadow-sm z-10 pointer-events-none">
                </span>
            </button>

            <!-- Notification dropdown -->
            <div id="notif-dropdown"
                class="hidden dash-dropdown absolute right-0 top-[calc(100%+8px)] z-50 w-80 bg-white border border-neutral-200 rounded-xl shadow-xl overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-neutral-100">
                    <h2 class="text-sm font-bold text-neutral-900">Notifications</h2>
                    <button id="mark-all-read-btn" class="text-xs font-medium text-[#00A486] hover:underline">Mark all
                        as read</button>
                </div>

                <!-- List (populated by JS) -->
                <ul id="notif-list" role="list" class="max-h-[320px] overflow-y-auto">
                    <!-- JS will fill this -->
                </ul>

                <!-- Footer -->
                <div class="px-4 py-2.5 border-t border-neutral-100 text-center">
                    <a href="<?= url('/notifications') ?>"
                        class="text-xs font-medium text-[#00A486] hover:underline">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- Profile chip -->
        <div class="relative">
            <button id="profile-btn" type="button" aria-expanded="false"
                class="flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-xl border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-neutral-300 transition-colors duration-150">
                <div class="w-[38px] h-[38px] rounded-[10px] flex items-center justify-center text-white text-sm font-bold flex-shrink-0 tracking-wide"
                    style="background: linear-gradient(135deg,#32C5AA,#008068);">
                    <?= htmlspecialchars($userInitials) ?>
                </div>
                <div class="text-left leading-tight hidden sm:block">
                    <span class="block text-[13px] font-semibold text-neutral-800 whitespace-nowrap">
                        <?= htmlspecialchars($userName) ?>
                    </span>
                    <span class="block text-[11px] font-medium text-[#00A486] whitespace-nowrap">
                        <?= htmlspecialchars($userRole) ?>
                    </span>
                </div>
                <i class="chevron fa-solid fa-chevron-down text-[11px] text-neutral-400 ml-0.5"></i>
            </button>

            <!-- Profile dropdown -->
            <div id="profile-dropdown"
                class="hidden dash-dropdown absolute right-0 top-[calc(100%+8px)] z-50 w-52 bg-white border border-neutral-200 rounded-xl shadow-xl overflow-hidden p-1.5">
                <div class="px-3 py-2.5 border-b border-neutral-100 mb-1">
                    <p class="text-[13px] font-semibold text-neutral-800"><?= htmlspecialchars($userName) ?></p>
                    <p class="text-[11px] text-neutral-400 mt-0.5"><?= htmlspecialchars($userEmail) ?></p>
                </div>
                <a href="<?= url('/dashboard/profile') ?>"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13.5px] font-medium text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition-colors">
                    <span
                        class="w-7 h-7 rounded-[7px] bg-[#E5F7F4] text-[#00A486] flex items-center justify-center text-[13px] flex-shrink-0"><i
                            class="fa-solid fa-user"></i></span>
                    View Profile
                </a>
                <a href="<?= url('/dashboard/profile/edit') ?>"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13.5px] font-medium text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition-colors">
                    <span
                        class="w-7 h-7 rounded-[7px] bg-[#E5F7F4] text-[#00A486] flex items-center justify-center text-[13px] flex-shrink-0"><i
                            class="fa-solid fa-user-pen"></i></span>
                    Edit Profile
                </a>
                <div class="h-px bg-neutral-100 my-1"></div>
                <button type="button" onclick="handleLogout()"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13.5px] font-medium text-neutral-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                    <span
                        class="w-7 h-7 rounded-[7px] bg-red-50 text-red-600 flex items-center justify-center text-[13px] flex-shrink-0"><i
                            class="fa-solid fa-right-from-bracket"></i></span>
                    Sign Out
                </button>
            </div>
        </div>

    </div>
</header>

<!-- Notification JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifBtn = document.getElementById('notif-btn');
    const dropdown = document.getElementById('notif-dropdown');
    const notifList = document.getElementById('notif-list');
    const unreadBadge = document.getElementById('unread-badge');
    const markAllBtn = document.getElementById('mark-all-read-btn');

    let isOpen = false;

    // Toggle dropdown
    notifBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        isOpen = !isOpen;
        dropdown.classList.toggle('hidden', !isOpen);
        if (isOpen) loadNotifications();
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (isOpen && !dropdown.contains(e.target) && e.target !== notifBtn) {
            isOpen = false;
            dropdown.classList.add('hidden');
        }
    });

    // Load notifications
    async function loadNotifications() {
        try {
            const res = await fetch((window.appBaseUrl || "") + '/api/v1/notifications?limit=10');
            const data = await res.json();
            if (!data.success) throw new Error('Failed');

            renderNotifications(data.data.items);
            updateUnreadBadge(data.data.unread_count);
        } catch (err) {
            console.error(err);
            notifList.innerHTML =
                `<li class="px-4 py-6 text-center text-neutral-400 text-sm">Could not load notifications</li>`;
        }
    }

    function renderNotifications(items) {
        if (items.length === 0) {
            notifList.innerHTML =
                `<li class="px-4 py-6 text-center text-neutral-400 text-sm">No notifications yet</li>`;
            return;
        }

        let html = '';
        items.forEach(item => {
            const isUnread = item.is_read == 0 || item.is_read === false;
            const dotClass = isUnread ? 'bg-[#00B795]' : 'bg-neutral-300';

            html += `
            <li onclick="markAsReadAndNavigate(${item.id}, '${item.link}')" 
                class="flex gap-3 px-4 py-3 hover:bg-neutral-50 cursor-pointer border-b border-neutral-50 transition-colors">
                <span class="mt-1.5 flex-shrink-0 w-2 h-2 rounded-full ${dotClass}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] ${isUnread ? 'font-semibold text-neutral-900' : 'text-neutral-600'} leading-snug">${item.message}</p>
                    <time class="text-[11px] text-neutral-400 mt-0.5 block">${formatTimeAgo(item.created_at)}</time>
                </div>
            </li>`;
        });
        notifList.innerHTML = html;
    }

    function updateUnreadBadge(count) {
        // Ensure count is treated as a number
        const numCount = parseInt(count);
        if (numCount > 0) {
            unreadBadge.textContent = numCount > 99 ? '99+' : numCount;
            unreadBadge.classList.remove('hidden');
            // Force flex display to ensure centering of the number
            unreadBadge.style.display = 'flex';
        } else {
            unreadBadge.classList.add('hidden');
            unreadBadge.style.display = 'none';
        }
    }

    // Mark as read + navigate
    window.markAsReadAndNavigate = async function(id, link) {
        try {
            await fetch((window.appBaseUrl || "") + '/api/v1/notifications/read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    notification_id: id
                })
            });
        } catch (e) {}

        dropdown.classList.add('hidden');
        isOpen = false;
        window.location.href = link;
    };

    // Mark all as read
    markAllBtn.addEventListener('click', async function(e) {
        e.stopPropagation();
        try {
            await fetch((window.appBaseUrl || "") + '/api/v1/notifications/read-all', {
                method: 'POST'
            });
            loadNotifications();
        } catch (err) {
            console.error(err);
        }
    });

    function formatTimeAgo(datetime) {
        const now = new Date();
        const then = new Date(datetime);
        const diffMs = now - then;
        const diffMin = Math.floor(diffMs / 60000);

        if (diffMin < 1) return 'Just now';
        if (diffMin < 60) return diffMin + ' min ago';
        if (diffMin < 1440) return Math.floor(diffMin / 60) + ' hour ago';
        return Math.floor(diffMin / 1440) + ' day ago';
    }

    // === IMPORTANT: Load badge immediately on page load ===
    // This fixes "badge not showing by default"
    loadNotifications(); // ← This was missing before

    // Auto-refresh badge every 30 seconds
    setInterval(() => {
        if (!isOpen) {
            fetch((window.appBaseUrl || "") + '/api/v1/notifications?limit=1')
                .then(r => r.json())
                .then(data => {
                    if (data.success) updateUnreadBadge(data.data.unread_count);
                })
                .catch(() => {});
        }
    }, 30000);
});
</script>