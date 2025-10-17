
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($sitetitle) ? ucfirst($sitetitle) : "QuickPass - Appointment Booking & Visitor Gate Pass System With Qr Code" ?></title>
    <link rel="icon" href="<?= themeSetting('fav_icon') ? themeSetting('fav_icon')->favicon : asset('images/favicon.png') ?>">
    <link rel="stylesheet" href="<?= asset('frontend/fonts/fontawesome/css/fontawesome.css') ?>">
    <link rel="stylesheet" href="<?= asset('frontend/fonts/fontawesome/css/brands.css') ?>">
    <link rel="stylesheet" href="<?= asset('frontend/fonts/fontawesome/css/solid.css') ?>">
    <link rel="stylesheet" href="<?= asset('frontend/fonts/typography/plus-JAKARTA-SANS/PlusJakartaSans.css') ?>">
    <link rel="stylesheet" href="<?= asset('js/izitoast/dist/css/iziToast.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('frontend/css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('frontend/css/custom.css') ?>">
    <?php yield_content('css'); ?>
    <?php stack_content('css'); ?>
    <link rel="stylesheet" href="<?= asset('build/assets/app.4109e52c.css') ?>">
</head>

<!-- Head -->
