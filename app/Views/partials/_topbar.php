<?php
/**
 * Partial: _topbar.php
 * Shared topbar for the dashboard layout.
 *
 * Expected variables (set by calling view/controller):
 *   $userName     - string  e.g. "Arif Rahman"
 *   $userInitials - string  e.g. "AR"
 *   $userEmail    - string  e.g. "arif@packly.news"
 *   $userRole     - string  e.g. "Admin" | "Editor" | "Reporter"
 *   $pageTitle    - string  e.g. "Dashboard"
 *   $pageSubtitle - string  e.g. "Welcome back, Arif!"
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
                <!-- Unread badge -->
                <span
                    class="absolute top-[7px] right-[8px] w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>

            <!-- Notification dropdown -->
            <div id="notif-dropdown"
                class="hidden dash-dropdown absolute right-0 top-[calc(100%+8px)] z-50 w-80 bg-white border border-neutral-200 rounded-xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-neutral-100">
                    <h2 class="text-sm font-bold text-neutral-900">Notifications</h2>
                    <a href="<?= url('/notifications/mark-read') ?>"
                        class="text-xs font-medium text-[#00A486] hover:underline">Mark all as read</a>
                </div>

                <!-- List -->
                <ul id="notif-list" role="list">
                    <li
                        class="flex gap-3 px-4 py-3 hover:bg-neutral-50 cursor-pointer border-b border-neutral-50 transition-colors">
                        <span class="mt-1.5 flex-shrink-0 w-2 h-2 rounded-full bg-[#00B795]"></span>
                        <div>
                            <p class="text-[13px] text-neutral-700 leading-snug"><strong
                                    class="font-semibold text-neutral-900">Karim Ahmed</strong> submitted a new article
                                for review.</p>
                            <time class="text-[11px] text-neutral-400 mt-0.5 block">5 minutes ago</time>
                        </div>
                    </li>
                    <li
                        class="flex gap-3 px-4 py-3 hover:bg-neutral-50 cursor-pointer border-b border-neutral-50 transition-colors">
                        <span class="mt-1.5 flex-shrink-0 w-2 h-2 rounded-full bg-[#00B795]"></span>
                        <div>
                            <p class="text-[13px] text-neutral-700 leading-snug">Your article <strong
                                    class="font-semibold text-neutral-900">"Climate &amp; Economy"</strong> was
                                published.</p>
                            <time class="text-[11px] text-neutral-400 mt-0.5 block">1 hour ago</time>
                        </div>
                    </li>
                    <li
                        class="flex gap-3 px-4 py-3 hover:bg-neutral-50 cursor-pointer border-b border-neutral-50 transition-colors">
                        <span class="mt-1.5 flex-shrink-0 w-2 h-2 rounded-full bg-neutral-300"></span>
                        <div>
                            <p class="text-[13px] text-neutral-700 leading-snug"><strong
                                    class="font-semibold text-neutral-900">Admin</strong> left a comment on your draft.
                            </p>
                            <time class="text-[11px] text-neutral-400 mt-0.5 block">Yesterday at 3:40 PM</time>
                        </div>
                    </li>
                    <li class="flex gap-3 px-4 py-3 hover:bg-neutral-50 cursor-pointer transition-colors">
                        <span class="mt-1.5 flex-shrink-0 w-2 h-2 rounded-full bg-neutral-300"></span>
                        <div>
                            <p class="text-[13px] text-neutral-700 leading-snug">System maintenance scheduled for
                                <strong class="font-semibold text-neutral-900">Sunday 2 AM</strong>.</p>
                            <time class="text-[11px] text-neutral-400 mt-0.5 block">2 days ago</time>
                        </div>
                    </li>
                </ul>

                <!-- Footer -->
                <div class="px-4 py-2.5 border-t border-neutral-100 text-center">
                    <a href="<?= url('/notifications') ?>"
                        class="text-xs font-medium text-[#00A486] hover:underline">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- ── Profile chip ── -->
        <div class="relative">
            <button id="profile-btn" type="button" aria-expanded="false"
                class="flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-xl border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-neutral-300 transition-colors duration-150">
                <!-- Avatar -->
                <div class="w-[38px] h-[38px] rounded-[10px] flex items-center justify-center text-white text-sm font-bold flex-shrink-0 tracking-wide"
                    style="background: linear-gradient(135deg,#32C5AA,#008068);">
                    <?= htmlspecialchars($userInitials) ?>
                </div>
                <!-- Info -->
                <div class="text-left leading-tight hidden sm:block">
                    <span class="block text-[13px] font-semibold text-neutral-800 whitespace-nowrap">
                        <?= htmlspecialchars($userName) ?>
                    </span>
                    <span class="block text-[11px] font-medium text-[#00A486] whitespace-nowrap">
                        <?= htmlspecialchars($userRole) ?>
                    </span>
                </div>
                <!-- Chevron -->
                <i class="chevron fa-solid fa-chevron-down text-[11px] text-neutral-400 ml-0.5"></i>
            </button>

            <!-- Profile dropdown -->
            <div id="profile-dropdown"
                class="hidden dash-dropdown absolute right-0 top-[calc(100%+8px)] z-50 w-52 bg-white border border-neutral-200 rounded-xl shadow-xl overflow-hidden p-1.5">
                <!-- User info header -->
                <div class="px-3 py-2.5 border-b border-neutral-100 mb-1">
                    <p class="text-[13px] font-semibold text-neutral-800"><?= htmlspecialchars($userName) ?></p>
                    <p class="text-[11px] text-neutral-400 mt-0.5"><?= htmlspecialchars($userEmail) ?></p>
                </div>

                <!-- View Profile -->
                <a href="<?= url('/dashboard/profile') ?>"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13.5px] font-medium text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition-colors">
                    <span
                        class="w-7 h-7 rounded-[7px] bg-[#E5F7F4] text-[#00A486] flex items-center justify-center text-[13px] flex-shrink-0">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    View Profile
                </a>

                <!-- Edit Profile -->
                <a href="<?= url('/dashboard/profile/edit') ?>"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13.5px] font-medium text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 transition-colors">
                    <span
                        class="w-7 h-7 rounded-[7px] bg-[#E5F7F4] text-[#00A486] flex items-center justify-center text-[13px] flex-shrink-0">
                        <i class="fa-solid fa-user-pen"></i>
                    </span>
                    Edit Profile
                </a>

                <div class="h-px bg-neutral-100 my-1"></div>

                <!-- Sign Out -->
                <button type="button" onclick="handleLogout()"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-[13.5px] font-medium text-neutral-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                    <span
                        class="w-7 h-7 rounded-[7px] bg-red-50 text-red-600 flex items-center justify-center text-[13px] flex-shrink-0">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </span>
                    Sign Out
                </button>
            </div>
        </div>

    </div>
</header>
<!-- ════════════════ END TOPBAR ════════════════ -->