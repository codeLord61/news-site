<?php
/**
 * View: dashboard/index.php
 * Main dashboard content view — works for both Editor and Reporter roles.
 * Loaded inside the dashboard layout via $content.
 *
 * Expected: $userRole ('editor' | 'reporter')
 */
$userRole = $userRole ?? 'reporter';
?>

<!-- ── Stats Grid ── -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-7">

  <!-- Total Articles -->
  <div class="stat-card bg-white rounded-2xl p-5 border border-neutral-200">
    <div class="flex items-center justify-between mb-4">
      <p class="text-[13px] font-medium text-neutral-500">Total Articles</p>
      <span class="w-10 h-10 rounded-[10px] bg-[#E5F7F4] text-[#00A486] flex items-center justify-center text-base">
        <i class="fa-solid fa-newspaper"></i>
      </span>
    </div>
    <p class="text-[28px] font-bold text-neutral-900 leading-none tracking-tight">1,284</p>
    <p class="mt-2 text-[12px] font-medium text-emerald-600">
      <i class="fa-solid fa-arrow-trend-up mr-1"></i>+12% from last month
    </p>
  </div>

  <!-- Pending Submissions -->
  <div class="stat-card bg-white rounded-2xl p-5 border border-neutral-200">
    <div class="flex items-center justify-between mb-4">
      <p class="text-[13px] font-medium text-neutral-500">Pending Submissions</p>
      <span class="w-10 h-10 rounded-[10px] bg-yellow-50 text-yellow-600 flex items-center justify-center text-base">
        <i class="fa-regular fa-clock"></i>
      </span>
    </div>
    <p class="text-[28px] font-bold text-neutral-900 leading-none tracking-tight">23</p>
    <p class="mt-2 text-[12px] font-medium text-red-600">
      <i class="fa-solid fa-arrow-trend-down mr-1"></i>-3 since yesterday
    </p>
  </div>

  <!-- Total Views -->
  <div class="stat-card bg-white rounded-2xl p-5 border border-neutral-200">
    <div class="flex items-center justify-between mb-4">
      <p class="text-[13px] font-medium text-neutral-500">Total Views</p>
      <span class="w-10 h-10 rounded-[10px] bg-blue-50 text-blue-600 flex items-center justify-center text-base">
        <i class="fa-regular fa-eye"></i>
      </span>
    </div>
    <p class="text-[28px] font-bold text-neutral-900 leading-none tracking-tight">48.2k</p>
    <p class="mt-2 text-[12px] font-medium text-emerald-600">
      <i class="fa-solid fa-arrow-trend-up mr-1"></i>+8.4% this week
    </p>
  </div>

</div>

<!-- ── Recent Articles ── -->
<div class="flex items-center justify-between mb-3.5">
  <h2 class="text-[16px] font-bold text-neutral-900">Recent Articles</h2>
<?php if ($userRole === 'reporter'): ?>
  <a
    href="<?= url('/articles/new') ?>"
    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-[9px] text-[13px] font-semibold text-white transition-all duration-150 hover:shadow-lg hover:-translate-y-px"
    style="background:linear-gradient(135deg,#00B795,#008068);"
  >
    <i class="fa-solid fa-plus"></i> New Article
  </a>
<?php endif; ?>
</div>

<div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">

  <!-- Card header -->
  <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-100">
    <h3 class="text-[15px] font-bold text-neutral-900">Latest Submissions &amp; Drafts</h3>
    <button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-neutral-200 text-[13px] font-medium text-neutral-600 hover:border-[#99E2D4] hover:text-[#008068] hover:bg-[#E5F7F4] transition-colors">
      <i class="fa-solid fa-filter text-xs"></i> Filter
    </button>
  </div>

  <!-- Table -->
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-neutral-50 border-b border-neutral-100">
          <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-neutral-500">Article</th>
          <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 hidden md:table-cell">Category</th>
          <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 hidden lg:table-cell">Author</th>
          <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-neutral-500">Status</th>
          <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 hidden sm:table-cell">Date</th>
          <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-neutral-500"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-neutral-50">

        <tr class="hover:bg-neutral-50 transition-colors">
          <td class="px-5 py-3.5">
            <p class="text-[13.5px] font-semibold text-neutral-900">Bangladesh Economy Q1 2026 Review</p>
            <p class="text-[11.5px] text-neutral-400 mt-0.5">1,240 words · 3 min read</p>
          </td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden md:table-cell">Economy</td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden lg:table-cell">Rahima Islam</td>
          <td class="px-4 py-3.5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-yellow-100 text-yellow-700">Pending</span>
          </td>
          <td class="px-4 py-3.5 text-[13px] text-neutral-500 hidden sm:table-cell whitespace-nowrap">Mar 22, 2026</td>
          <td class="px-4 py-3.5">
            <a href="#" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 text-[12px] font-medium text-neutral-600 hover:border-[#99E2D4] hover:text-[#008068] hover:bg-[#E5F7F4] transition-colors whitespace-nowrap">Review</a>
          </td>
        </tr>

        <tr class="hover:bg-neutral-50 transition-colors">
          <td class="px-5 py-3.5">
            <p class="text-[13.5px] font-semibold text-neutral-900">Climate Summit: Key Takeaways</p>
            <p class="text-[11.5px] text-neutral-400 mt-0.5">980 words · 2 min read</p>
          </td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden md:table-cell">Environment</td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden lg:table-cell">Karim Ahmed</td>
          <td class="px-4 py-3.5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700">Published</span>
          </td>
          <td class="px-4 py-3.5 text-[13px] text-neutral-500 hidden sm:table-cell whitespace-nowrap">Mar 21, 2026</td>
          <td class="px-4 py-3.5">
            <a href="#" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 text-[12px] font-medium text-neutral-600 hover:border-[#99E2D4] hover:text-[#008068] hover:bg-[#E5F7F4] transition-colors whitespace-nowrap">View</a>
          </td>
        </tr>

        <tr class="hover:bg-neutral-50 transition-colors">
          <td class="px-5 py-3.5">
            <p class="text-[13.5px] font-semibold text-neutral-900">Tech Startups in Dhaka: 2026 Trends</p>
            <p class="text-[11.5px] text-neutral-400 mt-0.5">1,550 words · 5 min read</p>
          </td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden md:table-cell">Technology</td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden lg:table-cell">Nusrat Jahan</td>
          <td class="px-4 py-3.5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#E5F7F4] text-[#008068]">Draft</span>
          </td>
          <td class="px-4 py-3.5 text-[13px] text-neutral-500 hidden sm:table-cell whitespace-nowrap">Mar 20, 2026</td>
          <td class="px-4 py-3.5">
            <a href="#" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 text-[12px] font-medium text-neutral-600 hover:border-[#99E2D4] hover:text-[#008068] hover:bg-[#E5F7F4] transition-colors whitespace-nowrap">Edit</a>
          </td>
        </tr>

        <tr class="hover:bg-neutral-50 transition-colors">
          <td class="px-5 py-3.5">
            <p class="text-[13.5px] font-semibold text-neutral-900">Dhaka Traffic: Smart Solutions Ahead</p>
            <p class="text-[11.5px] text-neutral-400 mt-0.5">720 words · 2 min read</p>
          </td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden md:table-cell">City</td>
          <td class="px-4 py-3.5 text-[13.5px] text-neutral-600 hidden lg:table-cell">Arif Rahman</td>
          <td class="px-4 py-3.5">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-700">Rejected</span>
          </td>
          <td class="px-4 py-3.5 text-[13px] text-neutral-500 hidden sm:table-cell whitespace-nowrap">Mar 19, 2026</td>
          <td class="px-4 py-3.5">
            <a href="#" class="px-2.5 py-1.5 rounded-lg border border-neutral-200 text-[12px] font-medium text-neutral-600 hover:border-[#99E2D4] hover:text-[#008068] hover:bg-[#E5F7F4] transition-colors whitespace-nowrap">Revise</a>
          </td>
        </tr>

      </tbody>
    </table>
  </div>

</div>
