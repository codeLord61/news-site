            <!-- Hero Section -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($articles['hero'] as $article): ?>
                <a href="<?= htmlspecialchars($article['url']) ?>" class="group block space-y-3">
                    <!-- Image -->
                    <div class="relative w-full h-56 bg-gray-200 rounded-lg overflow-hidden">
                        <img src="<?= htmlspecialchars($article['image']) ?>"
                            alt="<?= htmlspecialchars($article['title']) ?>"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>

                    <!-- Headline -->
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 group-hover:text-blue-600">
                        <?= htmlspecialchars($article['title']) ?>
                    </h2>

                    <!-- Summary -->
                    <p class="text-sm text-gray-600 leading-relaxed font-sans line-clamp-2">
                        <?= htmlspecialchars($article['summary']) ?>
                    </p>

                    <!-- Timestamp -->
                    <p class="text-xs text-gray-400 font-medium"><?= htmlspecialchars($article['time_ago']) ?></p>
                </a>
                <?php endforeach; ?>
            </section>

            <hr class="border-gray-100">

            <!-- Top Stories (List Layout) -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 border-l-4 border-red-600 pl-2">Top Stories</h3>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700">See All</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($articles['top_stories'] as $article): ?>
                    <a href="<?= htmlspecialchars($article['url']) ?>" class="block group">
                        <article class="flex gap-4 items-start md:flex-col md:gap-3">
                            <div
                                class="w-24 h-24 md:w-full md:h-48 flex-shrink-0 bg-gray-200 rounded-md overflow-hidden">
                                <img src="<?= htmlspecialchars($article['image']) ?>"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    alt="<?= htmlspecialchars($article['title']) ?>">
                            </div>
                            <div class="flex-1 flex flex-col justify-between h-24 md:h-auto py-0.5">
                                <div>
                                    <span
                                        class="text-[10px] font-bold text-<?= htmlspecialchars($article['category_color'] ?? 'blue-600') ?> uppercase tracking-wide mb-1 block"><?= htmlspecialchars($article['category']) ?></span>
                                    <h4
                                        class="text-sm md:text-base font-bold leading-snug text-gray-900 line-clamp-2 mb-1 group-hover:text-blue-600">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </h4>
                                    <p class="text-xs text-gray-500 line-clamp-2 hidden md:block">
                                        <?= htmlspecialchars($article['summary']) ?>
                                    </p>
                                </div>
                                <span class="text-xs text-gray-400 mt-2"><?= htmlspecialchars($article['time_ago']) ?></span>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <hr class="border-gray-100">

            <!-- Category: Bangladesh -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 border-l-4 border-green-600 pl-2">Bangladesh</h3>
                    <a href="category.html" class="text-xs font-semibold text-blue-600 hover:text-blue-700">See All</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <?php foreach ($articles['bangladesh'] as $article): ?>
                    <a href="<?= htmlspecialchars($article['url']) ?>" class="block group">
                        <article class="flex flex-col gap-2">
                            <div class="w-full aspect-[4/3] bg-gray-200 rounded-md overflow-hidden">
                                <img src="<?= htmlspecialchars($article['image']) ?>"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    alt="<?= htmlspecialchars($article['title']) ?>">
                            </div>
                            <div class="flex flex-col">
                                <h4
                                    class="text-sm font-bold leading-snug text-gray-900 line-clamp-2 mb-1 group-hover:text-blue-600">
                                    <?= htmlspecialchars($article['title']) ?>
                                </h4>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-1">
                                    <?= htmlspecialchars($article['summary']) ?>
                                </p>
                                <span class="text-xs text-gray-400"><?= htmlspecialchars($article['time_ago']) ?></span>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <hr class="border-gray-100">

            <!-- Category: International -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 border-l-4 border-blue-600 pl-2">International</h3>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700">See All</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <?php foreach ($articles['international'] as $article): ?>
                    <a href="<?= htmlspecialchars($article['url']) ?>" class="block group">
                        <article class="flex flex-col gap-2">
                            <div class="w-full aspect-[4/3] bg-gray-200 rounded-md overflow-hidden">
                                <img src="<?= htmlspecialchars($article['image']) ?>"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    alt="<?= htmlspecialchars($article['title']) ?>">
                            </div>
                            <div class="flex flex-col">
                                <h4
                                    class="text-sm font-bold leading-snug text-gray-900 line-clamp-2 mb-1 group-hover:text-blue-600">
                                    <?= htmlspecialchars($article['title']) ?>
                                </h4>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-1">
                                    <?= htmlspecialchars($article['summary']) ?>
                                </p>
                                <span class="text-xs text-gray-400"><?= htmlspecialchars($article['time_ago']) ?></span>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Category: Technology (2 Column Grid) -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 border-l-4 border-orange-600 pl-2">Smart Living</h3>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700">See All</a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-5 gap-y-10">
                    <?php foreach ($articles['smart_living'] as $article): ?>
                    <a href="<?= htmlspecialchars($article['url']) ?>" class="block group">
                        <article class="flex flex-col gap-2">
                            <div class="w-full aspect-[4/3] bg-gray-200 rounded-md overflow-hidden">
                                <img src="<?= htmlspecialchars($article['image']) ?>"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    alt="<?= htmlspecialchars($article['title']) ?>">
                            </div>
                            <div class="flex flex-col">
                                <h4
                                    class="text-sm font-bold leading-snug text-gray-900 line-clamp-2 mb-1 group-hover:text-blue-600">
                                    <?= htmlspecialchars($article['title']) ?>
                                </h4>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-1">
                                    <?= htmlspecialchars($article['summary']) ?>
                                </p>
                                <span class="text-xs text-gray-400"><?= htmlspecialchars($article['time_ago']) ?></span>
                            </div>
                        </article>
                    </a>
                    <?php endforeach; ?>
                </div>
            </section>
