<?php
/**
 * Layout: dashboard.php
 * Base layout for all dashboard pages (Reporter, Editor & Admin).
 *
 * Expected variables passed from the controller:
 *   $pageTitle    - string  Page heading shown in topbar
 *   $pageSubtitle - string  Secondary text under the heading
 *   $userName     - string
 *   $userInitials - string
 *   $userEmail    - string
 *   $userRole     - string  'editor' | 'admin'
 *   $content      - string  Rendered inner view HTML (from ob_get_clean())
 */
use app\core\App;
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> — Packly News</title>
  <meta name="description" content="Packly News CMS Dashboard" />

  <!-- Compiled Tailwind + Flowbite styles -->
  <link rel="stylesheet" href="<?= App::assetPath('css/styles.css') ?>" />
  <!-- Dashboard-specific styles -->
  <link rel="stylesheet" href="<?= App::assetPath('css/dashboard.css') ?>" />

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>

  <script>
    window.appBaseUrl = "<?= App::$PROJECT_ROOT_URL ?>";
  </script>
</head>
<body class="bg-neutral-100 text-neutral-800 h-full overflow-x-hidden">

<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" class="hidden fixed inset-0 z-35 bg-black/40" style="z-index:35;"></div>

<!-- Sidebar partial -->
<?php include __DIR__ . '/../partials/_sidebar.php'; ?>

<!-- Main wrapper: offset by sidebar width -->
<div id="main-wrapper" class="flex flex-col min-h-screen">

  <!-- Topbar partial -->
  <?php include __DIR__ . '/../partials/_topbar.php'; ?>

  <!-- Page content -->
  <main class="flex-1 p-6 sm:p-7">
    <?= $content ?? '' ?>
  </main>

</div>

<!-- Flowbite JS -->
<script src="<?= App::assetPath('js/flowbite.min.js') ?>"></script>
<!-- Dashboard interactions -->
<script src="<?= App::assetPath('js/dashboard.js') ?>"></script>

</body>
</html>
