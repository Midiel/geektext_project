$(function () {
    $('.img-thumbnail').on('click', function () {
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
        $('#imagemodal').on('click', function () {
            $('#imagemodal').modal('hide');
        });
    });
});
