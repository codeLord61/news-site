<?php

use app\core\App;
?>
<header class="sticky top-0 z-50 bg-white border-b border-gray-200">
    <!-- Top Bar: Logo & Date -->
    <div class="px-4 py-7 flex flex-col border-b border-gray-100 md:items-center">
        <a href="<?= url('/') ?>" class="text-2xl font-bold text-gray-900 leading-tight">Packly News</a>
        <!-- Thursday, March 19, 2026 -->
        <p class="text-xs text-gray-500 mt-1"><?= date('l, F j, Y') ?></p>
    </div>

    <!-- Navbar: Categories & Icons -->
    <div class="flex items-center justify-between pl-4 pr-2 h-12 bg-white">
        <!-- Scrollable Categories -->
        <nav class="flex-1 overflow-x-auto no-scrollbar mask-gradient-right">
            <ul class="flex items-center space-x-5 text-sm font-medium text-gray-600 whitespace-nowrap pr-4">
                <?php foreach (get_header_categories() as $category): ?>
                <li><a href="<?= url('/categories/' . $category['slug']) ?>"
                        class="hover:text-primary-600"><?= htmlspecialchars($category['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Fixed Icons (Right Side) -->
        <!-- Order from Left to Right: Search, Language, User, Hamburger -->
        <div class="flex items-center space-x-1 pl-2 bg-white shadow-[-10px_0_10px_rgba(255,255,255,1)] z-10">
            <button class="p-2 hover:bg-gray-100 rounded-full text-gray-600">
                <i class="fa-solid fa-magnifying-glass"></i> <span
                    class="hidden md:inline md:text-xs md:font-bold">Search</span>
            </button>

            <button class="p-2 hover:bg-gray-100 rounded-full text-gray-600">
                <i class="fa-solid fa-earth-africa"></i>
                <span class="text-xs font-bold">Bangla</span>
            </button>

            <a href="<?= url('/auth') ?>" id="headerLoginBtn"
                class="p-2 hover:bg-gray-100 rounded-full text-gray-600 block">
                <i class="fa-solid fa-user"></i><span class="hidden md:inline md:text-xs md:font-bold">Login</span>
            </a>

            <button id="headerLogoutBtn" style="display: none;"
                class="p-2 hover:bg-gray-100 rounded-full text-gray-600 block">
                <i class="fa-solid fa-sign-out-alt"></i><span
                    class="hidden md:inline md:text-xs md:font-bold">Logout</span>
            </button>

            <script>
            document.addEventListener("DOMContentLoaded", () => {
                const loginBtn = document.getElementById('headerLoginBtn');
                const logoutBtn = document.getElementById('headerLogoutBtn');
                const token = localStorage.getItem('auth_token');

                if (token) {
                    loginBtn.style.display = 'none';
                    logoutBtn.style.display = 'block';
                }

                logoutBtn.addEventListener('click', async () => {
                    const token = localStorage.getItem('auth_token');
                    try {
                        // Use the token fetched at click time to avoid any state issues
                        const response = await fetch((window.appBaseUrl || "") + "/api/v1/logout", {
                            method: "POST",
                            headers: {
                                "Authorization": "Bearer " + token,
                                "Accept": "application/json"
                            },
                            credentials: "include"
                        });
                        
                        // We check for response status but proceed anyway to ensure local cleanup
                        if (!response.ok) {
                            console.error("Logout API returned error:", response.status);
                        }
                    } catch (e) {
                        console.error("Logout network failed:", e);
                    }
                    // Clear all potential auth keys
                    localStorage.removeItem('auth_token');
                    localStorage.removeItem('user_role');

                    location.reload();
                });
            });
            </script>

            <button class="p-2 hover:bg-gray-100 rounded-full text-gray-600">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

</header>