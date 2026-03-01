<?php 
use app\core\App;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'The Daily News' ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= App::assetPath('css/styles.css'); ?>">
    <!-- <link rel="stylesheet" href="/assets/css/styles.css"> -->
</head>

<body class="text-gray-900">

    <!-- Mobile Container -->
    <div class="w-full max-w-md md:max-w-screen-xl mx-auto bg-white min-h-screen transition-all duration-300">

        <?php require __DIR__ . '/../partials/header.php'; ?>

        <!-- Main Content -->
        <main class="p-4 space-y-8">
            <?php echo $content ?? '' ?>
        </main>

        <?php require __DIR__ . '/../partials/footer.php'; ?>

    </div>

</body>

</html>