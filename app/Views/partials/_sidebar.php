<?php
/**
 * Partial: _sidebar.php
 * Shared sidebar for the dashboard layout.
 * Used by both Editor and Admin roles.
 * The $userRole variable (set by the calling view/controller) determines
 * which sections are rendered.
 *
 * Expected: $userRole = 'editor' | 'admin'
 */
$userRole = $userRole ?? 'editor';
$currentPath = $currentPath ?? (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$currentPath = rtrim((string)$currentPath, '/');
if ($currentPath === '') {
    $currentPath = '/';
}

$activeLinkClass = 'nav-link active relative flex items-center gap-3 px-3 py-[10px] rounded-[10px] text-sm font-medium text-[#00B795] bg-[#E5F7F4] mb-0.5';
$inactiveLinkClass = 'nav-link relative flex items-center gap-3 px-3 py-[10px] rounded-[10px] text-sm font-medium text-neutral-600 hover:bg-[#E5F7F4] hover:text-[#008068] mb-0.5 transition-colors duration-150';
$linkClass = static function (array $paths) use ($currentPath, $activeLinkClass, $inactiveLinkClass): string {
    return in_array($currentPath, $paths, true) ? $activeLinkClass : $inactiveLinkClass;
};
?>

<!-- ════════════════════ SIDEBAR ════════════════════ -->
<aside
  id="sidebar"
  role="navigation"
  aria-label="Main navigation"
  class="fixed top-0 left-0 z-40 h-screen w-64 bg-white border-r border-neutral-200 flex flex-col overflow-hidden"
>

  <!-- Logo -->
  <div class="flex items-center gap-3 px-4 py-5 border-b border-neutral-100 min-h-[68px] overflow-hidden">
    <!-- Logo icon -->
    <div class="flex-shrink-0 w-10 h-10 rounded-[10px] flex items-center justify-center font-bold text-white text-lg"
         style="background: linear-gradient(135deg,#00B795,#008068);">
      P
    </div>
    <!-- Logo text: hidden when collapsed -->
    <div class="sidebar-label-text leading-tight">
      <span class="block text-[17px] font-bold text-neutral-900 whitespace-nowrap">Packly</span>
      <span class="block text-[10px] font-semibold uppercase tracking-widest text-[#00B795]">News CMS</span>
    </div>
  </div>

  <!-- Navigation -->
  <nav id="sidebar-nav" class="flex-1 px-2 py-3 overflow-y-auto">

    <!-- ── Main Menu ── -->
    <p class="sidebar-section-label px-3 pb-1 pt-2 text-[10px] font-semibold uppercase tracking-widest text-neutral-400">
      Main Menu
    </p>

    <a href="<?= url('/dashboard') ?>"
       class="<?= $linkClass(['/dashboard']) ?>"
       data-tooltip="Dashboard">
      <span class="flex-shrink-0 w-5 text-center text-[15px]"><i class="fa-solid fa-gauge-high"></i></span>
      <span class="sidebar-nav-text">Dashboard</span>
    </a>

    <?php if ($userRole === 'reporter'): ?>
    <a href="<?= url('/my-articles') ?>"
       class="<?= $linkClass(['/my-articles']) ?>"
       data-tooltip="My Articles">
      <span class="flex-shrink-0 w-5 text-center text-[15px]"><i class="fa-solid fa-newspaper"></i></span>
      <span class="sidebar-nav-text">My Articles</span>
    </a>

    <a href="<?= url('/submissions') ?>"
       class="<?= $linkClass(['/submissions']) ?>"
       data-tooltip="Submissions">
      <span class="flex-shrink-0 w-5 text-center text-[15px]"><i class="fa-regular fa-clock"></i></span>
      <span class="sidebar-nav-text">Submissions</span>
    </a>

    <a href="<?= url('/articles/new') ?>"
       class="<?= $linkClass(['/articles/new']) ?>"
       data-tooltip="New Article">
      <span class="flex-shrink-0 w-5 text-center text-[15px]"><i class="fa-solid fa-plus"></i></span>
      <span class="sidebar-nav-text">New Article</span>
    </a>

    <!-- ── Analytics ── -->
    <p class="sidebar-section-label px-3 pb-1 pt-5 text-[10px] font-semibold uppercase tracking-widest text-neutral-400">
      Analytics
    </p>

    <a href="<?= url('/analytics') ?>"
       class="<?= $linkClass(['/analytics']) ?>"
       data-tooltip="Article Analytics">
      <span class="flex-shrink-0 w-5 text-center text-[15px]"><i class="fa-solid fa-chart-line"></i></span>
      <span class="sidebar-nav-text">Article Analytics</span>
    </a>
    <?php endif; ?>

    <?php if ($userRole === 'admin'): ?>
    <!-- ── Administration (admin only) ── -->
    <p class="sidebar-section-label px-3 pb-1 pt-5 text-[10px] font-semibold uppercase tracking-widest text-neutral-400">
      Administration
    </p>

    <a href="<?= url('/admin/users') ?>"
       class="<?= $linkClass(['/admin/users']) ?>"
       data-tooltip="Manage Users">
      <span class="flex-shrink-0 w-5 text-center text-[15px]"><i class="fa-solid fa-users-gear"></i></span>
      <span class="sidebar-nav-text">Manage Users</span>
    </a>

    <a href="<?= url('/admin/settings') ?>"
       class="<?= $linkClass(['/admin/settings']) ?>"
       data-tooltip="Site Settings">
      <span class="flex-shrink-0 w-5 text-center text-[15px]"><i class="fa-solid fa-sliders"></i></span>
      <span class="sidebar-nav-text">Site Settings</span>
    </a>
    <?php endif; ?>

  </nav>

</aside>
<!-- ════════════════ END SIDEBAR ════════════════ -->
