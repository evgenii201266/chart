$(document).ready(function() {
    var dataUrl = $('#data-url').val();
    dataUrl = dataUrl.replace('//', '/');

    var $table = $('#datatable').DataTable({
        dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
        buttons: [
            {
                extend: 'copyHtml5',
                className: 'btn btn-default',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excelHtml5',
                className: 'btn btn-default',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csvHtml5',
                className: 'btn btn-default',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-default',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        "oLanguage": {
            "bProcessing": "<img src='/ariol/assets/images/custom/loading.gif' alt='" + $tagLanguage.attr('data-loading') + "'>",
            "sProcessing": "<img src='/ariol/assets/images/custom/loading.gif' alt='" + $tagLanguage.attr('data-loading') + "'>",
            "sLengthMenu": "<span>Показать:</span> _MENU_",
            "sZeroRecords": $tagLanguage.attr('data-no-data'),
            "sInfo": "<button id='delete-selected-items' class='btn btn-danger'>" +
                $tagLanguage.attr('data-delete-selected') +
            "</button>",
            "sInfoEmpty": "",
            "sInfoFiltered": "",
            "sInfoPostFix": "",
            "sSearch": "<span>" + $tagLanguage.attr('data-search') + ":</span>",
            "sUrl": "",
            "oPaginate": {
                "sLast": $tagLanguage.attr('data-last'),
                "sFirst": $tagLanguage.attr('data-first'),
                "sPrevious": '<i class="icon-arrow-left7">',
                "sNext": '<i class="icon-arrow-right7">'
            }
        },
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(0).text-center', nRow).addClass('text-center');
        },
        "fnDrawCallback": function () {
            /* Первая ячейка первой колонки. */
            var $th = $('th:eq(0)');

            if ($th.find(':checkbox')) {
                /* Убираем сортировку у ячейки с выбором всех записей. */
                $th.removeClass('sorting_asc').addClass('sorting_disabled');
            }

            /* Центрируем все ячейки в шапке таблицы. */
            //$('th').addClass('text-center');

            /* Активируем переключатель поля с типом activity в самой таблице. */
            $('.list-activity').bootstrapSwitch({
                size: 'mini',
                onText: 'Вкл',
                offText: 'Выкл',
                onSwitchChange: function (event, state) {
                    $.ajax({
                        type: 'post',
                        url: '/grid/activity',
                        data: {
                            model: $(this).data('model'),
                            state: (state === true ? 1 : 0),
                            id: $(this).data('id'),
                            updating_field: $(this).data('updating-field')
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function () {}
                    })
                }
            });

            $('[name="datatable_length"]').select2({
                width: 'auto'
            });

            $('.styled').uniform({
                radioClass: 'choice'
            });

            /* Выбрать сразу все записи. */
            $('#select-all').on('click', function() {
                var $select_all = this.checked;

                $('[data-item-id]').each(function() {
                    var id = $(this).attr('data-item-id');

                    $('[data-item-id="' + id + '"]').prop('checked', $select_all).attr('checked', $select_all);
                    $.uniform.update();
                });
            });

            /* Выбор записей. */
            $('#datatable').on('click', '[data-item-id]', function () {
                if ($('[data-item-id]:not(:checked)').length > 0) {
                    $('#select-all').prop('checked', false);
                } else {
                    $('#select-all').prop('checked', true);
                }

                $.uniform.update();
            });

            /* Удаление выбранных записей в таблице. */
            $('#delete-selected-items').on('click', function() {
                var selected = [];
                var tr = [];

                /* Счётчик активного пункта меню. */
                var $count = $('.sidebar li.active:last').find('span.label');

                $('[data-item-id]:checked').each(function(i) {
                    selected[i] = $(this).val();
                    tr[i] = $(this).closest('tr');
                });

                if (tr.length === 0) {
                    swal({
                        title: $tagLanguage.attr('data-no-selected'),
                        confirmButtonColor: "#2196F3",
                        type: "error"
                    });
                } else {
                    swal({
                        type: "warning",
                        closeOnCancel: true,
                        closeOnConfirm: true,
                        showCancelButton: true,
                        showLoaderOnConfirm: true,
                        confirmButtonColor: "#EF5350",
                        title: $tagLanguage.attr('data-delete-are-you-sure'),
                        cancelButtonText: $tagLanguage.attr('data-no-cancel'),
                        confirmButtonText: $tagLanguage.attr('data-yes-delete')
                    }, function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: 'post',
                                url: '/grid/delete-items',
                                data: {
                                    selected: selected,
                                    model: $('[data-table-model]').attr('data-table-model')
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(result) {
                                    if (result.error) {
                                        swal({
                                            title: result.error,
                                            confirmButtonColor: "#2196F3",
                                            type: "error"
                                        });
                                    } else {
                                        $('#select-all').prop('checked', false).attr('checked', false);
                                        $.uniform.update();

                                        $.each(tr, function(index) {
                                            $table.row(index).remove();
                                        });

                                        $table.draw(false);

                                        /* Обновление счётчика в меню, если он существует. */
                                        if ($count.length !== 0) {
                                            $count.text(parseInt($count.text()) - selected.length);
                                        }

                                        noty({
                                            width: 200,
                                            text: $tagLanguage.attr('data-delete-success'),
                                            type: 'success',
                                            dismissQueue: true,
                                            timeout: 2000,
                                            layout: 'topRight'
                                        });
                                    }
                                }
                            });
                        }
                    });
                }
            });

            var $table = $('.datatable-highlight').DataTable();

            /* Выделение колонки, строки и их пересечения при наведении на ячейку. */
            if ($('.datatable-highlight tbody td').length !== 0) {
                $('.datatable-highlight tbody').on('mouseover', 'td', function() {
                    if ($table.cell(this).index() !== undefined) {
                        var colIdx = $table.cell(this).index().column;

                        if (colIdx !== null) {
                            $($table.cells().nodes()).removeClass('active');
                            $($table.column(colIdx).nodes()).addClass('active');
                        }
                    }
                }).on('mouseleave', function() {
                    $($table.cells().nodes()).removeClass('active');
                });
            }
        },
        "aoColumnDefs": dtData.arrayJSONColTable,
        "bDestroy": true,
        "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Все']],
        "iDisplayLength": 25,
        "bAutoWidth": false,
        "bSort": true,
        "bFilter": true,
        "processing": true,
        "bServerSide": true,
        "ajax": {
            "url": dataUrl,
            "type": 'post',
            "headers": {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            "beforeSend" : function(){
                $('#tables-loading').removeClass('hidden');
            },
            "complete": function(){
                $('#tables-loading').addClass('hidden');
            }
        },
        "bProcessing": true
    });

    /* Редактирование данных прямо в таблице. */
    $('table').on('keyup', 'div[contenteditable="true"]', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);

        if (code === 27) {
            $(this).text($(this).attr('data-field-value'));
            $(this).blur();
        }
    }).on('keypress', 'div[contenteditable="true"]', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);

        if (code === 13) {
            e.preventDefault();

            var value = $(this).text();
            var id = $(this).attr('data-entry-id');
            var field = $(this).attr('data-field-name');
            var model = $('[data-table-model]').attr('data-table-model');

            $.ajax({
                type: 'post',
                url: '/grid/save',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id, value: value, field: field, model: model
                },
                success: function(notice) {
                    if (notice.type === 'error') {
                        $(e.target).text($(e.target).attr('data-field-value'));
                    }

                    noty({
                        width: 200,
                        text: notice.message,
                        type: notice.type,
                        dismissQueue: true,
                        timeout: 2000,
                        layout: 'topRight'
                    });
                }
            });

            $(this).blur();

            return false;
        }
    }).on('click', 'td', function() {
        var $div = $(this).find('div[contenteditable="true"]');

        if ($div.length) {
            $div.focus();
        }
    });

    /* Удаление записи. */
    $('body').on('click', '.delete-item-confirm', function() {
        var link = $(this).attr('data-link');
        var text = $(this).attr('data-confirm-text');
        var $tr = $(this).closest('tr');

        /* Счётчик активного пункта меню. */
        var $count = $('.sidebar li.active:last').find('span.label');

        swal({
            title: text,
            type: "warning",
            closeOnCancel: true,
            closeOnConfirm: true,
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonColor: "#EF5350",
            cancelButtonText: $tagLanguage.attr('data-no-cancel'),
            confirmButtonText: $tagLanguage.attr('data-yes-delete')
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: link,
                    type: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        if (result.error) {
                            swal({
                                title: result.error,
                                confirmButtonColor: "#2196F3",
                                type: "error"
                            });
                        } else {
                            $table.row($tr).remove().draw(false);

                            /* Обновление счётчика в меню, если он существует. */
                            if ($count.length !== 0) {
                                $count.text(parseInt($count.text()) - 1);
                            }

                            noty({
                                width: 200,
                                text: $tagLanguage.attr('data-destroyed'),
                                type: 'success',
                                dismissQueue: true,
                                timeout: 2000,
                                layout: 'topRight'
                            });
                        }
                    }
                });
            }
        });
    });
});