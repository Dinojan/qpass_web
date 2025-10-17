<!DOCTYPE>
<html>
<?php
$sitetitle = $sitetitle ?? setting('site_title') ?? 'QuickPass - Appointment Booking & Visitor Gate Pass System With Qr Code';
include_view('frontend.layouts.partials.head._head', ['sitetitle' => $sitetitle]) ?>
<header class="mt-6">
    <div class="container">
        <div
            class="flex justify-between items-center py-2 px-2 backdrop-blur-xl bg-[rgba(255,255,255,0.6)] rounded-[32px]">
            <a href="<?= route('/') ?>" class="w-28">
                <img src="<?= themeSetting('site_logo') ? themeSetting('site_logo')->logo : asset('images/site_logo.png') ?>"
                    alt="logo" class="w-full">

            </a>
            <div class="lg:flex items-center gap-x-12 hidden">
                <nav class="lg:flex items-center gap-x-12 font-semibold text-lg">
                    <a href="<?= route('check-in.pre.registered') ?>"
                        class="hover:text-primary"><?= __('all.have_appoinment') ?></a>
                    <a href="<?= route('check-in.return') ?>"
                        class="hover:text-primary "><?= __('all.been_here_before') ?></a>
                    <?php if (authHelper()->user()): ?>
                        <a href="<?= route('checkout.index') ?>"
                            class="hover:text-primary "><?= __('all.check_out') ?></a>
                    <?php endif; ?>
                </nav>
                <div class="dropdown">
                    <button
                        class="dropdownbtn flex items-center justify-center  gap-2 rounded-3xl capitalize text-sm font-medium transition text-heading">
                        <?php foreach ($languages as $lang): ?>
                            <?php if (Session()->has('applocale') and Session()->get('applocale') and setting('locale')): ?>
                                <?php if (Session()->get('applocale') == $lang['code']): ?>
                                    <span id="current-lang" class="whitespace-nowrap font-semibold text-lg">
                                        <?= $lang['flag_icon'] == null ? '🇬🇧' : $lang['flag_icon'] ?>
                                        <?= $lang['name'] ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if (setting('locale') == $lang['code']): ?>
                                    <span id="current-lang" class="whitespace-nowrap font-semibold text-lg">
                                        <?= $lang['flag_icon'] == null ? '🇬🇧' : $lang['flag_icon'] ?>
                                        <?= $lang['name'] ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <i class="ms-1 fa-solid fa-chevron-down dropdown-icon"></i>
                    </button>

                    <ul
                        class="dropdown-content p-2 min-w-[180px] rounded-lg shadow-xl absolute top-16  z-10 border border-gray-200 bg-white hidden ">
                        <?php if (!empty($languages)): ?>
                            <?php foreach ($languages as $lang): ?>
                                <li class=" py-1.5 px-2.5 rounded-md cursor-pointer hover:bg-gray-100 list-none">
                                    <a href="<?= route('admin.lang.index', ['locale' => $lang['code']]) ?>"
                                        data-lang="<?= $lang['flag_icon'] == null ? '🇬🇧' : $lang['flag_icon'] ?> <?= $lang['name'] ?>"
                                        class="flex items-center gap-2 font-semibold text-lg"><span><?= $lang['flag_icon'] == null ? '🇬🇧' : $lang['flag_icon'] ?>
                                            <?= $lang['name'] ?></span></a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </ul>
                </div>
                <?php if (authHelper()->user()): ?>
                    <a href="<?= route('admin.dashboard.index') ?>"><button
                            class="bg-primary text-white rounded-[23.5px] px-6 py-3 leading-tight text-lg font-semibold"><?= __('all.go_to_dashboard') ?></button></a>

                <?php else: ?>
                    <a href="<?= route('login') ?>"><button
                            class="bg-primary text-white rounded-[23.5px] px-6 py-3 leading-tight text-lg font-semibold"><?= __('all.login') ?></button></a>

                <?php endif; ?>
            </div>
            <div id="open-sidebar"
                class="text-3xl cursor-pointer lg:hidden open-sidebar-button text-primary p-1 bg-primary rounded-md">
                <div class="hamburger"></div>
                <div class="hamburger"></div>
                <div class="hamburger"></div>
            </div>
        </div>
    </div>
</header>

<aside id="sidebar"
    class="fixed inset-0 z-50 w-screen h-screen invisible opacity-0 bg-black/50 transition-all duration-300">
    <div class="w-full bg-white transition-all duration-300 -translate-y-full">
        <div class="flex justify-between items-start p-4">
            <?php if (setting('site_logo')): ?>
                <a href="<?= route('/') ?>" class="w-28">
                    <img src="<?php echo asset('images/' . setting('site_logo')) ?>" alt="logo" class="w-full">
                </a>
            <?php endif; ?>
            <span class="cursor-pointer ml-28 lg:hidden text-primary block" id="close-sidebar">
                <i class="fa-regular fa-circle-xmark"></i>
            </span>
        </div>
        <hr class="w-full h-[1px] bg-[#d4d4d4]">
        <div class="p-2.5 w-full ">
            <ul class="flex flex-col text-[18px]">
                <li class="w-full py-2 px-2 hover:bg-primary rounded-md hover:text-white mb-2"><a
                        href="<?= route('check-in.pre.registered') ?>"><?= __('all.have_appoinment') ?></a>
                </li>
                <li class="w-full py-2 px-2 hover:bg-primary rounded-md hover:text-white mb-2"><a
                        href="<?= route('check-in.return') ?>"><?= __('all.been_here_before') ?></a></li>
                <?php if (authHelper()->user()): ?>
                    <li class="w-full py-2 px-2 hover:bg-primary rounded-md hover:text-white mb-2"><a
                            href="<?= route('checkout.index') ?>"><?= __('all.check_out') ?></a></li>
                <?php endif; ?>
            </ul>
            <div class="dropdown py-2 px-2 mb-2">
                <?php if (!empty($language)):  ?>
                    <button
                        class="dropdownbtn w-full flex items-center gap-2 rounded-3xl capitalize text-sm font-medium text-heading transition-all duration-300 ease-in-out">
                        <?php foreach ($language as $lang): ?>
                            <?php if (Session()->has('applocale') and Session()->get('applocale') and setting('locale')): ?>
                                <?php if (Session()->get('applocale') == $lang['code']): ?>
                                    <span id="current-lang" class="flex-auto text-left whitespace-nowrap font-semibold text-lg">
                                        <?= $lang['flag_icon'] == null ? '🇬🇧' : $lang['flag_icon'] ?>
                                        <?= $lang['name'] ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if (setting('locale') == $lang['code']): ?>
                                    <span id="current-lang" class="whitespace-nowrap font-semibold text-lg">
                                        <?= $lang['flag_icon'] == null ? '🇬🇧' : $lang['flag_icon'] ?>
                                        <?= $lang['name'] ?> </span>
                                <?php endif; ?>
                            <?php endif; ?>     <?php endforeach; ?>
                        <i class="ms-1 fa-solid fa-chevron-down dropdown-icon"></i>
                    </button>
                <?php endif; ?>
                <?php if (!empty($language)): ?>
                    <ul class="dropdown-content min-w-[180px] rounded-lg lg:shadow-xl z-10 border-3 hidden pt-4 px-4">
                        <?php foreach ($language as $lang): ?>
                            <li class="py-1.5 rounded-md cursor-pointer hover:bg-gray-100 list-none">
                                <a href="<?= route('admin.lang.index', ['locale' => $lang['code']]) ?>"
                                    class="flex items-center gap-2 font-semibold text-lg">
                                    <span> <?= $lang['flag_icon'] == null ? '🇬🇧' : $lang['flag_icon'] ?>
                                        <?= $lang['name'] ?></span></a>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php if (authHelper()->user()): ?>
                <a href="<?= route('admin.dashboard.index') ?>"><button
                        class="bg-primary text-white rounded-md px-6 py-3 leading-tight w-full"><?= __('all.go_to_dashboard') ?></button></a>
            <?php else: ?>
                <a href="<?= route('login') ?>"><button
                        class="bg-primary text-white rounded-md px-6 py-3 leading-tight w-full"><?= __('all.login') ?></button></a>
            <?php endif; ?>
        </div>
    </div>
</aside>


<!-- Main Content -->
<div class="main" data-mobile-height="">
    <?php yield_content('content'); ?>
</div>
<!-- Main Content -->

<?php yield_content('extras'); ?>

<?php yield_content('modals'); ?>

<?php include_view('frontend.layouts.partials.script._scripts') ?>
<?php stack_content('js'); ?>
</body>

</html>