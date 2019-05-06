var adminPath = '';

$(document).ready(function() {
    adminPath = $('.page-container').attr('data-config-url');

    var $tagLanguage = $('language');

    $.ajaxSetup({
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /* Скролл в самый низ страницы. */
    $('#scroll-to-bottom').on('click', function () {
        $('html, body').animate({
            scrollTop: $(document).height()
        }, 500);
    });

    /* Скролл в самый верх страницы. */
    $('#scroll-to-top').on('click', function () {
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });

    /* Выделение активного пункта меню. */
    $('div.sidebar-main li.active').last().parent('ul').show().parents('li').addClass('active').closest('ul').show();

    /* Скролл до активного пункта меню. */
    var $item = $('li.active:last');

    if ($item.length) {
        var height = $item.height(),
            offset = $item.offset().top,
            windowHeight = $(window).height(),
            focus = (height < windowHeight) ? offset - ((windowHeight / 2) - (height / 2)) : offset;

        $('#sidebar-menu').mCustomScrollbar({
            axis: 'y',
            theme: 'default',
            scrollButtons: {
                enable: true
            },
            scrollInertia: 60,
            mouseWheelPixels: 50
        }).mCustomScrollbar('scrollTo', focus);
    }

    $('[data-toggle="tooltip"]').tooltip({
        trigger : 'hover'
    });

    /* Обновление стилей checkbox и radio. */
    updateUniform();

    $('#sidebar-localization').mCustomScrollbar({
        axis: 'y',
        theme: 'dark',
        scrollButtons: {
            enable: true
        },
        scrollInertia: 60,
        mouseWheelPixels: 50
    });

    /* Загрузка языкового пакета. */
    $('body').on('click', '[data-language-select]', function(e) {
        if (e.target !== undefined && !$(e.target).hasClass('styled') && !$(e.target).hasClass('icon-cross2')) {
            var language = $(this).attr('data-language-select');

            if (language != 'ru') {
                loadPackage(language);
            }
        }
    })
    /* Активация или деактивация языка. */
        .on('click', '.language-checkbox', function(e) {
            e.stopPropagation();

            var active = $(this).find('input').is(':checked') ? 'on' : 'off',
                language = $(this).closest('li').attr('data-language-select');

            $.ajax({
                url: '/' + adminPath + '/system/localization/toggle-active',
                data: {
                    active: active,
                    language: language
                },
                success: function(result) {
                    /* Обновление списка языков в шапке. */
                    updateAdminLanguages();

                    /* Уведомление о совершённом действии. */
                    resultNotice(result);
                }
            });
        })
        /* Назначение нового языка по умолчанию. */
        .on('change', 'input[name="default-language"]', function(e) {
            var language = $(this).val();

            $.ajax({
                url: '/' + adminPath + '/system/localization/toggle-default',
                data: {
                    language: language
                },
                success: function(result) {
                    /* Уведомление о совершённом действии. */
                    resultNotice(result);
                }
            });
        })
        /* Обновление значений checkbox'ов. */
        .on('click', ':checkbox', function () {
            var value = this.checked === true ? 1 : 0,
                $checkbox = $(this).attr('checked', this.checked).prop('checked', this.checked);

            $(this).val(value);
            $.uniform.update($checkbox);
        })
        /* Удаление языка. */
        .on('click', '.localization-remove', function() {
            var language = $(this).closest('li').attr('data-language-select');

            swal({
                    text: '',
                    type: "warning",
                    closeOnConfirm: true,
                    showCancelButton: true,
                    confirmButtonColor: "#FF7043",
                    cancelButtonText: $tagLanguage.attr('data-no-cancel'),
                    confirmButtonText: $tagLanguage.attr('data-yes-delete'),
                    title: $tagLanguage.attr('data-language-delete-are-you-sure')
                },
                function() {
                    $.ajax({
                        url: '/' + adminPath + '/system/localization/remove-language',
                        data: {
                            language: language
                        },
                        success: function(result) {
                            if (result.success) {
                                /* Обновление списка доступных для выбора языков. */
                                updateListLanguages($tagLanguage);

                                /* Обновление списка языков в шапке. */
                                updateAdminLanguages();

                                if ($('.page-container').attr('data-current-language') == language) {
                                    /* Обновление текущего языка в шапке. */
                                    updateCurrentLanguage();
                                }

                                $('[data-lang-package="' + language + '"]').remove();
                                $('[data-language-select="' + language + '"]').remove();

                                if ($('input[name="default-language"]:checked').length == 0) {
                                    var $radio = $('[data-language-select="ru"]');

                                    $radio.find('span').addClass('checked');
                                    $radio.find('input[name="default-language"]').attr('checked', this.checked).prop('checked', this.checked);
                                }
                            }

                            /* Уведомление о совершённом действии. */
                            resultNotice(result);
                        }
                    });
                });
        })
        /* Изменение языка, на основе которого будет заполняться перевод. */
        .on('change', '#select-language-for-translation', function() {
            var language = $(this).val();

            $.ajax({
                url: '/' + adminPath + '/system/localization/change-language-for-translation',
                data: {
                    language: language
                },
                success: function(phrases) {
                    if (phrases.error) {
                        /* Уведомление о совершённом действии. */
                        notice('error', phrases.error);
                    } else {
                        $.each(phrases, function(key, words) {
                            $.each(words, function(index, word) {
                                $('#' + key + ' tbody tr:eq(' + index + ') td:first-child span').text(word);
                            });
                        });
                    }
                }
            });
        })
        /* Показывать только те выражения, которые ещё не переведены. */
        .on('change', '#untranslated', function() {
            var checked = this.checked;
            var $inputs = $('.table-translate input[value!=""]');

            $.each($inputs, function() {
                if (checked) {
                    $(this).closest('tr').addClass('d-n');
                } else {
                    $(this).closest('tr').removeClass('d-n');
                }
            });
        });

    /* Инициализация выбора языка. */
    initLanguageSelect($tagLanguage);

    /* Добавление языка. */
    $('#add-language').ajaxForm({
        success: function(result) {
            if (! result.error) {
                $('#modal-create-language').modal('hide');

                $.ajax({
                    url: '/' + adminPath + '/system/localization/update-available-languages',
                    success: function(languages) {
                        $('li[data-language-select]').remove();
                        $('ul#languages li.navigation-header').after(languages);

                        /* Обновление стилей checkbox и radio. */
                        updateUniform();
                    }
                });

                /* Обновление списка языков в шапке. */
                updateAdminLanguages();

                /* Обновление списка доступных для выбора языков. */
                updateListLanguages($tagLanguage);

                /* Обновление стилей checkbox и radio. */
                updateUniform();

                /* Уведомление о совершённом действии. */
                notice('success', $tagLanguage.attr('data-add-language-success'));
            } else {
                /* Уведомление о совершённом действии. */
                notice('error', result.error);
            }
        }
    });
});

/* Обновление списка языков в шапке. */
function updateAdminLanguages() {
    $.ajax({
        url: '/' + adminPath + '/system/localization/update-admin-languages',
        success: function(languages) {
            $('#admin-languages').html(languages);
        }
    });
}

/* Обновление списка доступных для выбора языков. */
function updateListLanguages($tagLanguage) {
    var $language = $('#language');

    $.ajax({
        url: '/' + adminPath + '/system/localization/update-list-languages',
        success: function(languages) {
            $language.select2('destroy').html(languages);

            /* Инициализация выбора языка. */
            initLanguageSelect($tagLanguage);
        }
    });
}

/* Инициализация выбора языка. */
function initLanguageSelect($tagLanguage) {
    $('.language-select').select2({
        placeholder: $tagLanguage.attr('data-select-item'),
        language: {
            noResults: function() {
                return $tagLanguage.attr('data-no-founded');
            }
        },
        templateResult: templateLanguages,
        templateSelection: templateLanguages,
        escapeMarkup: function (markup) {
            return markup;
        }
    });
}

/* Шаблон для вывода иконок и названий языков. */
function templateLanguages(option) {
    if (! option.id) {
        return option.text;
    }

    return '<img src="/languages/' + option.id + '.svg" class="localization-select-language">' +
        '<div class="localization-name">' +
        option.text +
        '</div>' +
        '';
}

/* Загрузка языкового пакета. */
function loadPackage(language) {
    var $tagLanguage = $('language');

    $.ajax({
        url: '/' + adminPath + '/system/localization/load-package',
        data: {
            language: language
        },
        success: function(langPackage) {
            if (langPackage.error) {
                /* Уведомление о совершённом действии. */
                notice('error', langPackage.error);
            } else {
                $('#lang-package').html(langPackage);

                /*$('.table-translate').DataTable({
                    "bDestroy": true,
                    "iDisplayLength": 10,
                    "iDisplayStart": 0,
                    "bAutoWidth": false,
                    "bSort": false,
                    "bFilter": false,
                    "processing": false,
                    "bServerSide": false,
                    "bLengthChange" : false,
                    "bInfo" : false,
                    "oLanguage": {
                        "sUrl": "",
                        "sInfoEmpty": "",
                        "sInfoPostFix": "",
                        "sInfoFiltered": "",
                        "oPaginate": {
                            "sLast": $tagLanguage.attr('data-last'),
                            "sFirst": $tagLanguage.attr('data-first'),
                            "sNext": '<i class="icon-arrow-right7">',
                            "sPrevious": '<i class="icon-arrow-left7">'
                        },
                        "sSearch": "<span>" + $tagLanguage.attr('data-search') + ":</span>",
                        "sZeroRecords": $tagLanguage.attr('data-no-data'),
                        "sLengthMenu": "<span>" + $tagLanguage.attr('data-show') + ":</span> _MENU_",
                        "bProcessing": "<img src='/ariol/assets/images/custom/loading.gif' alt='" + $tagLanguage.attr('data-loading') + "'>",
                        "sProcessing": "<img src='/ariol/assets/images/custom/loading.gif' alt='" + $tagLanguage.attr('data-loading') + "'>"
                    }
                });*/

                /* Обновление стилей checkbox и radio. */
                updateUniform();

                /* Инициализация выбора языка. */
                initLanguageSelect($tagLanguage);

                /* Сохранение перевода. */
                $('#save-translate').ajaxForm({
                    data: {
                        selectedLanguageForTranslation: language
                    },
                    success: function(result) {
                        /* Уведомление о совершённом действии. */
                        resultNotice(result);

                        /* Загрузка языкового пакета. */
                        loadPackage(language);

                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);
                    }
                });
            }
        }
    });
}

window.onscroll = function() {
    scrollButtons()
};

/* Кнопки "вверх" и "вниз" для скроллинга по странице. */
function scrollButtons() {
    if (document.body.scrollTop > 1 || document.documentElement.scrollTop > 1) {
        $('#scroll-to-top').removeClass('hidden');
    } else {
        $('#scroll-to-top').addClass('hidden');
    }

    var scrollTop = document.body.scrollTop,
        height = document.documentElement.scrollHeight - document.documentElement.clientHeight;

    if ((scrollTop !== 0 && scrollTop < (height - 1)) || document.documentElement.scrollTop < (height - 1)) {
        $('#scroll-to-bottom').removeClass('hidden');
    } else {
        $('#scroll-to-bottom').addClass('hidden');
    }
}

/* Обновление стилей checkbox и radio. */
function updateUniform() {
    $('.styled').uniform({
        radioClass: 'choice'
    });
}

/* Обновление текущего языка в шапке. */
function updateCurrentLanguage() {
    $.ajax({
        url: '/' + adminPath + '/system/localization/change-current-admin-language',
        success: function(language) {
            $('#current-language').html(language);
        }
    });
}