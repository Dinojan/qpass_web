<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php include_view('admin.layouts.header') ?>
<body>
    <audio id="myAudio1">
        <source src="<?=asset('beep.mp3')?>" type="audio/mpeg">
    </audio>
    <main class="db-main">
        <?php include_view('admin.layouts.menubar') ?>
        <?php include_view('admin.layouts.sidebar') ?>
        <?php include_view('admin.layouts.content') ?>
    </main>
    <?php include_view('admin.layouts.script') ?>
</body>
</html>
