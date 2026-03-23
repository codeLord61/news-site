<!-- Overlay Menu (Full Page) -->
<div id="overlayMenu" class="fixed inset-0 z-50 bg-white hidden flex-col pt-4 overflow-y-auto">
    <!-- Close Button and Top Area -->
    <div class="px-4 flex justify-between items-start relative mb-4">
        <!-- Center Title & Date container -->
        <div class="absolute left-1/2 -translate-x-1/2 w-full max-w-xs text-center">
            <h2 class="text-3xl font-bold text-gray-900 leading-tight">Packly News</h2>
            <p class="text-sm text-gray-500 mt-1"><?= date('l, F j, Y') ?></p>
        </div>
        
        <!-- Empty div for flex spacing -->
        <div></div>

        <!-- Close Button -->
        <button id="closeOverlayBtn" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 z-10">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- Main Content Grid -->
    <!-- Mobile: flex-col with order handling | Desktop: grid 2 cols -->
    <div class="flex-1 w-full max-w-7xl mx-auto px-4 mt-8 flex flex-col md:grid md:grid-cols-2 gap-8 md:gap-4 pb-12">
        
        <!-- Account / Social (Top on Mobile, Right on Desktop) -->
        <div class="order-1 md:order-2 flex flex-col space-y-8 md:pl-12">
            
            <!-- Auth Section container -->
            <div id="overlayAuthSection" class="min-h-20">
                <!-- Logged Out State -->
                <div id="overlayLoggedOut" class="hidden">
                    <a href="<?= url('/auth') ?>" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded-md transition-colors w-full md:w-auto text-center">
                        Login
                    </a>
                </div>

                <!-- Logged In State -->
                <div id="overlayLoggedIn" class="hidden flex-col space-y-4">
                    <div class="flex items-center space-x-3 bg-gray-50 p-4 rounded-lg border border-gray-100 shadow-sm">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xl overflow-hidden shrink-0">
                            <img id="overlayUserAvatar" src="" alt="Avatar" class="w-full h-full object-cover hidden" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                            <i class="fa-solid fa-user hidden" id="overlayUserDefaultIcon"></i>
                        </div>
                        <!-- Info -->
                        <div>
                            <p id="overlayUserName" class="text-gray-900 font-bold text-lg">Username</p>
                            <a href="<?= url('/my-account') ?>" class="text-sm text-primary-600 hover:text-primary-800 font-medium">My Account</a>
                        </div>
                    </div>
                    
                    <div>
                        <button id="overlayLogoutBtn" class="flex items-center space-x-2 text-red-600 hover:text-red-700 font-bold px-2 py-1 transition-colors">
                            <i class="fa-solid fa-sign-out-alt"></i><span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 md:hidden">

            <!-- Social and Footer Links -->
            <div class="space-y-4">
                <div>
                    <span class="text-sm font-bold text-gray-900 block mb-2">Follow us on:</span>
                    <div class="flex items-center space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white transition-colors">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-pink-600 hover:text-white transition-colors">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <div class="pt-4 flex flex-col space-y-3">
                    <a href="#" class="text-gray-700 hover:text-primary-600 font-semibold">About Us</a>
                    <a href="#" class="text-gray-700 hover:text-primary-600 font-semibold">Contact Us</a>
                </div>
            </div>
        </div>

        <!-- Categories (Bottom on Mobile, Left on Desktop) -->
        <div class="order-3 md:order-1 flex flex-col space-y-4 md:pr-12 md:border-r border-gray-100">
            <h3 class="text-3xl font-bold text-gray-900 mb-10">Categories</h3>
            <ul class="flex flex-col space-y-2">
                <?php 
                $overlayCategories = get_categories_with_children();
                foreach ($overlayCategories as $cat): 
                    $hasChildren = !empty($cat['children']);
                ?>
                    <li class="border-b border-gray-50 pb-2 last:border-0">
                        <div class="flex items-center justify-between group">
                            <a href="<?= url('/categories/' . $cat['slug']) ?>" class="text-lg font-medium text-gray-800 hover:text-primary-600 flex-1 py-1">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                            <?php if ($hasChildren): ?>
                            <button class="p-2 text-gray-400 hover:text-primary-600 overlay-dropdown-toggle" data-target="overlay-child-<?= $cat['id'] ?>">
                                <i class="fa-solid fa-chevron-down text-sm transition-transform duration-200"></i>
                            </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($hasChildren): ?>
                        <ul id="overlay-child-<?= $cat['id'] ?>" class="hidden pl-4 mt-2 mb-2 space-y-2 border-l-2 border-primary-100 ml-1">
                            <?php foreach ($cat['children'] as $child): ?>
                            <li>
                                <a href="<?= url('/categories/' . $child['slug']) ?>" class="block text-gray-600 hover:text-primary-600 py-1 text-sm font-medium">
                                    <?= htmlspecialchars($child['name']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</div>
