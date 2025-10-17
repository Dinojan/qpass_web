
<script src="<?= asset('frontend/js/jquery.js') ?>"></script>
<script src="<?= asset('frontend/js/script.js') ?>"></script>
<script src="<?= asset('js/izitoast/dist/js/iziToast.min.js') ?>"></script>
<?= yield_content('scripts'); ?>
<?= stack_content('scripts'); ?>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // @if(session('success'))
    //     iziToast.success({
    //         title: 'Success',
    //         message: "{{ session('success') }}",
    //         position: 'topRight'
    //     });
    // @endif

    // @if(session('error'))
    //     iziToast.error({
    //         title: 'Error',
    //         message: "{{ session('error') }}",
    //         position: 'topRight'
    //     });
    // @endif
</script>
