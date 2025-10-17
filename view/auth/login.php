<?php start_section('content'); ?>
<div class="main" data-mobile-height="">
    <section class="h-screen ">
        <div class="container py-16">
            <div class="max-w-[770px] w-full mx-auto rounded-2xl  bg-cardBg shadow-card -z-50">
                <div class="row">
                    <div class="col-12 md:col-6 !py-0">
                        <div class="py-6 px-4 md:px-6 md:pr-0">
                            <form method="POST" action="<?= route('login') ?>">
                                <?= csrf_field(); ?>
                                <h1
                                    class="text-2xl sm:text-[32px] font-extrabold text-primary mb-8 md:mb-12 leading-none">
                                    <?= __('Login') ?></h1>
                                <div class="mb-6">
                                    <label class="block tracking-wide text-black text-sm font-medium mb-2 required"
                                        for="email"><?= __('Email Address') ?></label>
                                    <input
                                        class=" appearance-none   w-full text-primary border border-[#97A3C0] rounded-[12px] py-3 px-4 mb-1 leading-tight "
                                        id="email" type="text" name="email" value="<?=old('email')?>">
                                     <?php error_message('email'); ?>
                                </div>
                                <div class="mb-6">
                                    <label class="block tracking-wide text-black text-sm font-medium mb-2 required"
                                        for="password"><?= __('Password') ?></label>
                                    <input
                                        class=" appearance-none block w-full text-primary border border-[#97A3C0] rounded-[12px] py-3 px-4 mb-1 leading-tight "
                                        id="password" type="password" name="password">
                                    <?php error_message('password'); ?>
                                </div>
                                <div class="flex justify-between mb-7">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="relative flex cursor-pointer items-center rounded-full"
                                            for="checkbox-1" data-ripple-dark="true">
                                           <input class="form-check-input" type="checkbox" name="remember" id="remember" <?= old('remember') ? 'checked' : '' ?>>

                                            <div
                                                class="pointer-events-none absolute top-2/4 left-2/4 -translate-y-2/4 -translate-x-2/4 text-white opacity-0 transition-opacity peer-checked:opacity-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                    viewBox="0 0 20 20" fill="currentColor" stroke="currentColor"
                                                    stroke-width="1">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <label for="checkbox-1" class="font-medium text-sm"> <?=__('Remember Me') ?></label>
                                    </div>
                                    <span class="font-medium text-sm text-primary"><a
                                            href="<?= route('password/reset') ?>"
                                            class="custom-control-labe"><?= __('Reset Password?') ?></a></span>
                                </div>
                                <button type="submit"
                                    class="text-lg font-bold leading-snug text-white bg-primary rounded-[23.5px] px-6 py-3 w-full shadow-btnNext">Login</button>
                            </form>
                            <!-- <p class="text-base font-bold mt-12 mb-4">For Demo Login, Click Below</p>
                            <div class="flex flex-shrink gap-x-2 sm:gap-x-4 justify-between sm:justify-start">
                                <button id="demo-admin"
                                    class="bg-[#4F97EC] text-white rounded-3xl px-4 py-3 shadow-btnAdmin text-sm md:text-base font-bold">Admin</button>
                                <button id="demo-employee"
                                    class="bg-[#EC874F] text-white rounded-3xl px-4 py-3 shadow-btnEmployee text-sm md:text-base font-bold">Employee</button>
                                <button id="demo-reception"
                                    class="bg-[#944FEC] text-white rounded-3xl px-4 py-3 shadow-btnReception text-sm md:text-base font-bold">Reception</button>
                            </div> -->

                        </div>
                    </div>
                    <div class="hidden md:block md:col-6 !py-0 ">
                        <div class="max-w-[373px] max-h-[561px] w-full h-full">
                            <img src="<?= asset('frontend/images/login/login.png')?>" alt="" class="w-full h-full">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php end_section(); ?>