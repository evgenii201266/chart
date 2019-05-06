$(document).ready(function () {
    $('.styled').uniform({
        radioClass: 'choice'
    });

    $('body').on('click', ':checkbox', function () {
        var $checkbox = $(this).attr('checked', this.checked).prop('checked', this.checked);
        $.uniform.update($checkbox);
    });

    var $notMultiSelectFiltering = $('select:not(.multiselect-select-all-filtering)');

    /* Выбор одного элемента из списка. */
    if ($notMultiSelectFiltering.length > 0) {
        $.each($notMultiSelectFiltering, function () {
            $(this).select2({
                placeholder: $tagLanguage.attr('data-select-item'),
                language: {
                    noResults: function () {
                        return $tagLanguage.attr('data-no-founded');
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        });
    }

    var $multiSelectFiltering = $('.multiselect-select-all-filtering');

    /* Выбор нескольких элементов из списка. */
    if ($multiSelectFiltering.length > 0) {
        $.each($multiSelectFiltering, function () {
            $(this).multiselect({
                maxHeight: 300,
                includeSelectAllOption: true,
                enableClickableOptGroups: true,
                enableFiltering: true,
                templates: {
                    filter: '<li class="multiselect-item multiselect-filter">' +
                        '<i class="icon-search4"></i> <input class="form-control" type="text">' +
                    '</li>'
                },
                selectAllText: $tagLanguage.attr('data-select-all'),
                allSelectedText: $tagLanguage.attr('data-add-selected'),
                nSelectedText: $tagLanguage.attr('data-selected'),
                nonSelectedText: $tagLanguage.attr('data-no-selected'),
                filterPlaceholder: $tagLanguage.attr('data-search'),
                enableCaseInsensitiveFiltering: true,
                onInitialized: function(select, container) {
                    $('.styled, .multiselect-container input').uniform({
                        radioClass: 'choice'
                    });
                },
                onSelectAll: function() {
                    $.uniform.update();
                },
                onChange: function() {
                    $.uniform.update();
                }
            });
        });
    }

    $('.phone-mask').each(function() {
        var placeholder = $(this).attr('placeholder');
        var mask = placeholder.replace(/_/g , '0');

        if (placeholder !== '') {
            $(this).mask(mask);
        }
    });

    var $pickDate = $('.pickadate');

    /* Выбор даты. */
    if ($pickDate.length > 0) {
        $pickDate.pickadate({
            monthsFull: [
                $tagLanguage.attr('data-date-January'), $tagLanguage.attr('data-date-February'), $tagLanguage.attr('data-date-March'), $tagLanguage.attr('data-date-April'),
                $tagLanguage.attr('data-date-May'), $tagLanguage.attr('data-date-June'), $tagLanguage.attr('data-date-July'), $tagLanguage.attr('data-date-August'),
                $tagLanguage.attr('data-date-September'), $tagLanguage.attr('data-date-October'), $tagLanguage.attr('data-date-November'), $tagLanguage.attr('data-date-December')
            ],
            weekdaysShort: [
                $tagLanguage.attr('data-date-Sun'), $tagLanguage.attr('data-date-Mon'), $tagLanguage.attr('data-date-Tue'),
                $tagLanguage.attr('data-date-Wed'), $tagLanguage.attr('data-date-Thu'), $tagLanguage.attr('data-date-Fri'), $tagLanguage.attr('data-date-Sat')
            ],
            showMonthsShort: undefined,
            showWeekdaysFull: undefined,
            closeOnSelect: true,
            closeOnClear: true,

            clear: '',
            close: $tagLanguage.attr('data-date-close'),
            today: $tagLanguage.attr('data-date-today'),

            format: "yyyy-mm-dd",

            labelMonthNext: $tagLanguage.attr('data-date-next-month'),
            labelMonthPrev: $tagLanguage.attr('data-date-prev-month'),
            labelMonthSelect: $tagLanguage.attr('data-date-select-month'),
            labelYearSelect: $tagLanguage.attr('data-date-select-year')
        });
    }

    var $colorPicker = $('.colorpicker-show-input');

    /* Выбор цвета. */
    if ($colorPicker.length > 0) {
        var localization = $.spectrum.localization["ru"] = {
            cancelText: $tagLanguage.attr('data-color-cancel'),
            chooseText: $tagLanguage.attr('data-color-choose'),
            clearText: $tagLanguage.attr('data-color-clear'),
            noColorSelectedText: $tagLanguage.attr('data-color-no-selected'),
            togglePaletteMoreText: $tagLanguage.attr('data-color-more'),
            togglePaletteLessText: $tagLanguage.attr('data-color-hide')
        };

        $.extend($.fn.spectrum.defaults, localization);

        $colorPicker.spectrum({
            showInput: true
        });
    }

    $('.addresspicker').each(function() {
        var form = $(this).closest('form').attr('data-group-tab');
        var group = (form !== undefined) ? form + '_' : '';

        var id = $(this).attr('data-id-address');
        var value = [];

        $('input[name="' + id + '\\[\\]"]').each(function() {
            value.push($(this).val());
        });

        var address = (value[0] !== '') ? value[0] : '';
        var lat = (value[1] !== '') ? value[1] : '53.9045398';
        var lng = (value[2] !== '') ? value[2] : '27.561524400000053';

        $('#' + group + id).val(address);
        $('#' + group + id + '_lat').val(lat);
        $('#' + group + id + '_lng').val(lng);

        var $addressPickerMap = $(this).addresspicker({
            regionBias: "by",
            reverseGeocode: true,
            updateCallback: getCoordinates,
            mapOptions: {
                zoom: 17,
                center: new google.maps.LatLng(lat, lng),
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            },
            elements: {
                map: '#' + group + id + '_map'
            }
        });

        var $marker = $addressPickerMap.addresspicker('marker');
        $marker.setVisible(true);

        $addressPickerMap.addresspicker('updatePosition');

        function getCoordinates(geocodeResult, parsedGeocodeResult){
            $('#' + group + id + '_lat').val(parsedGeocodeResult.lat);
            $('#' + group + id + '_lng').val(parsedGeocodeResult.lng);
        }
    });

    if ($('.tinymce').length > 0) {
        tinyMCE.init({
            selector: '.tinymce',
            language: 'ru',
            height: 200,
            theme: 'modern',
            fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
            force_br_newlines : false,
            force_p_newlines : false,
            forced_root_block : '',
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
            ],
            toolbar1: 'undo redo | styleselect | sizeselect | bold italic | fontselect |  fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | preview'
        });
    }

    if ($('.summernote').length > 0) {
        $('.summernote').summernote({
            height: 150,
            lang: 'ru-RU',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontstyle', ['strikethrough', 'superscript', 'subscript']],
                ['fontname', ['fontname', 'fontsize', 'height']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    for (var i = 0; i < files.length; i++) {
                        summerNoteSendFile(files[i], $(this).attr('id'));
                    }
                },
                onMediaDelete: function(image) {
                    $.ajax({
                        type: 'POST',
                        url: '/summerNote-remove-image',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {image: image.attr('src')},
                        success: function() {
                            image.remove();
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                },
                onChange: function(content) {
                    if (content.indexOf('img src') > -1) {
                        return false;
                    }
                }
            }
        });

        function summerNoteSendFile(file, editor) {
            var $form_data = new FormData();
            $form_data.append('image', file);

            $.ajax({
                type: 'POST',
                url: '/summerNote-upload-image',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $form_data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    $('#' + editor).summernote('insertImage', url);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }

        $('.link-dialog input[type=checkbox], .note-modal-form input[type=radio]').uniform({
            radioClass: 'choice'
        });

        $('.note-image-input').uniform({
            fileButtonClass: 'action btn bg-warning-400'
        });
    }

    $('.autocomplete').each(function (i, el) {
        $(el).autocomplete({
            serviceUrl: '/autocomplete',
            params: {
                model: $(el).data('model'),
                saving_field: $(el).data('saving_field'),
                output_field: $(el).data('output_field')
            },
            onSelect: function (suggestion) {
                $(this).next().val(suggestion.data);
            }
        }).focusout(function () {
            if (! $.trim($(el).val())) {
                $(el).next().val('');
            }
        });
    });

    $('form').on('keydown', function(e) {
        if (e.ctrlKey && e.keyCode === 83) {
            $('.save-changes').click();

            return false;
        }
    });

    var first = '';

    var form = '';
    var form_group = '';

    $('form.main-form').ajaxForm({
        beforeSerialize: function(f, options) {
            form = f.attr('id');
            form_group = f.attr('data-group-tab');

            $('form#' + form + ' button.save-changes').attr('disabled', true);

            $('textarea.summernote').each(function (index, item) {
                $(item).html($(item).summernote('code'));
            });

            $('textarea.tinymce').each(function (index, item) {
                $(item).html(tinyMCE.editors[index].getContent());
            });

            $('textarea.ckeditor').each(function (index, item) {
                $(item).html(CKEDITOR.instances[$(item).attr('id')].getData());
            });
        },
        beforeSubmit: function() {
            $('form#' + form + ' .save-load-changes').removeClass('hidden');
            $('form#' + form + ' .save-changes').attr('disabled', true);
        },
        success: function (result) {
            $('form#' + form + ' .save-load-changes').addClass('hidden');
            $('form#' + form + ' .save-changes').attr('disabled', false);

            $('label').css('color', 'inherit');

            var type = (result.checkFields) ? 'error' : 'success';

            if (form !== 'form-base' || type === 'error') {
                noty({
                    width: 200,
                    text: result.message,
                    type: type,
                    dismissQueue: true,
                    timeout: 4000,
                    layout: 'topRight'
                });
            }

            if (result.checkFields) {
                form_group = (form_group !== undefined) ? form_group + '_' : '';

                $.each(result.checkFields, function(field) {
                    var labelField = $('label[for="' + form_group + result.checkFields[field] + '"]');

                    labelField.css('color', 'red');

                    if (first === '') {
                        first = field;

                        var destination = labelField.offset().top;
                        $('html').animate({ scrollTop: destination }, 100);
                    }
                });

                return false;
            } else {
                if (form === 'form-base') {
                    window.location.href = $('form#' + form + ' #form-cancel').attr('href');
                } else {
                    $('html').animate({ scrollTop: $('body').offset().top }, 100);
                }
            }
        },
        error: function () {
            $('form#' + form + ' .save-load-changes').addClass('hidden');
            $('form#' + form + ' .save-changes').attr('disabled', false);
        }
    });

    $(document).on('click', '.add_more_element_arr', function () {
        var content = $(this).parents('.type_array').first().clone();

        $(this).parents('.array_type_data').append(content);
        $(this).parents('.array_type_data').find('.type_array').last().find('input').val('');
        $(this).parents('.array_type_data').find('.type_array').last().find('.add_more_element_arr')
            .removeClass('add_more_element_arr').removeClass('btn-success').addClass('btn-danger')
            .addClass('remove_more_element_arr').find('.icon-add')
            .removeClass('icon-add').addClass('icon-trash');

        $(this).parents('.array_type_data').find('.type_array').last().find('input').focus();
    });

    $(document).on('click', '.remove_more_element_arr', function () {
        $(this).closest('.type_array').remove();
    });

    if ($('.sortable_hash').length > 0) {
        $('.sortable_hash').sortable({
            handle: 'button.btn-primary',
            cancel: ''
        }).disableSelection().sortable({
            handle: 'button.btn-primary',
            cancel: '',
            beforeStop: function() {
                $(this).find('.add_more_element_hash').removeClass('add_more_element_hash')
                    .addClass('remove_more_element_hash').find('.icon-add')
                    .removeClass('icon-add').addClass('icon-trash');
                $(this).find('.remove_more_element_hash').first().removeClass('remove_more_element_hash')
                    .addClass('add_more_element_hash').find('.icon-trash')
                    .removeClass('icon-trash').addClass('icon-add');
            }
        });
    }

    $(document).on('click', '.add_more_element_hash', function () {
        var content = $(this).parents('.type_array').first().clone();

        $(this).parents('.sortable_hash').append(content);
        $(this).parents('.array_type_data').find('.type_array').last().find('.form-control').val('');
        $(this).parents('.array_type_data').find('.type_array').last().find('.add_more_element_hash')
            .removeClass('btn-success').addClass('btn-danger').removeClass('add_more_element_hash')
            .addClass('remove_more_element_hash').find('.icon-add')
            .removeClass('icon-add').addClass('icon-trash');

        $('.sortable_hash').sortable('refresh');

        $(this).parents('.array_type_data').find('.type_array').last().find('.first').focus();
    });

    $(document).on('click', '.remove_more_element_hash', function (e) {
        $(this).closest('.type_array').remove();
    });

    /* Выделение активного пункта меню. */
    $('div.sidebar-main li.active').last().parent('ul').show().parents('li').addClass('active').closest('ul').show();

    /* Скролл до активного пункта меню. */
    var $item = $('li.active:last');

    if ($item.length) {
        var offset = $item.offset().top;
        var height = $item.height();
        var windowHeight = $(window).height();

        var focus = (height < windowHeight) ? offset - ((windowHeight / 2) - (height / 2)) : offset;

        $('#sidebar-menu').mCustomScrollbar({
            theme: 'default',
            scrollButtons: {
                enable: true
            },
            axis: 'y',
            mouseWheelPixels: 50,
            scrollInertia: 60
        }).mCustomScrollbar('scrollTo', focus);
    }

    /* Сохранение выбранного таба в сессию. */
    $('[data-ariol-tab]').on('click', function() {
        var tab = $(this).attr('data-ariol-tab');

        $.ajax({
            type: 'post',
            url: '/save-tab',
            data: {tab: tab},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    /* Рекурсивные хлебные крошки. */
    $($('.sidebar-main .active').get().reverse()).each(function () {
        $('ul.breadcrumb').prepend('<li>' +
            (($(this).find('a') && $(this).find('a').attr('href') !== undefined) ? '<a href="' + $(this).find('a').attr('href') + '">' : '') +
            $(this).find('span.sidebar-menu-name:first').text().trim() +
            (($(this).find('a') && $(this).find('a').attr('href') !== undefined) ? '</a>' : '') +
        '</li>');
    });
});