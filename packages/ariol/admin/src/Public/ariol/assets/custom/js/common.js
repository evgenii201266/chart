var $tagLanguage = '';

$(document).ready(function() {
    var adminPath = $('.page-container').attr('data-config-url');

    $tagLanguage = $('language');

    /* Изменение языка в админке. */
    $('body').on('click', 'ul#admin-languages li', function() {
        var code = $(this).attr('data-language-code');

        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/' + adminPath + '/system/localization/change-admin-language',
            data: {code: code},
            success: function() {
                window.location.reload(true);
            }
        });
    });
});