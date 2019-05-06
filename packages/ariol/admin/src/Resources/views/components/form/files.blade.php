<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
        <strong>{{ $label }}</strong>
        @if ($require)
            <span class="text-danger-800">*</span>
        @endif
    </label>
    <input id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}" name="{{ $name }}"
           type="hidden" value="{{ json_encode($value) }}">
    <script>
        var files_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?> = [];
    </script>
    <div id="fileupload_{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                @if ($creatable)
                    <div class="uploader">
                        <input class="file-styled-primary" name="selectedFilesForUpload[]" type="file" multiple>
                        <span class="action btn bg-blue legitRipple" style="-moz-user-select: none;">
                            <i class="icon-add position-left"></i> Добавить
                        </span>
                    </div>
                @endif
                @if (! empty($description))
                    <span class="help-block">{{ $description }}</span>
                @endif
                <span class="fileupload-process"></span>
            </div>
            <div class="col-lg-5 fileupload-progress fade">
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width: 0;"></div>
                </div>
                <div class="progress-extended"></div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-form-margin" role="presentation">
                <tbody class="files">
                    @if (! empty($value) && is_array($value))
                        @foreach ($value as $file)
                            @if (file_exists(public_path() . $file))
                                <script>
                                    files_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?>.push("<?php echo $file; ?>");
                                </script>
                                <tr class="template-download fade in">
                                    <td style="width: 10%;">
                                        <?php $allowedMimeTypes = [
                                            'image/jpeg', 'image/gif', 'image/png', 'image/tif',
                                            'image/bmp', 'image/svg+xml', 'image/x-icon', 'image/tiff',
                                        ];
                                        $contentType = mime_content_type(public_path() . $file); ?>
                                        <a class="fancybox" download="" title="" href="{{ $file }}">
                                            @if (in_array($contentType, $allowedMimeTypes) )
                                                @if ($contentType == 'image/svg+xml' || $contentType == 'image/x-icon')
                                                    <img src="{{ $file }}" style="width: {{ config('ariol.preview-size.width') }}px; height: {{ config('ariol.preview-size.height') }}px">
                                                @else
                                                    <img src="{{ GlideImage::load($file, ['h' => config('ariol.preview-size.height'), 'w' => config('ariol.preview-size.width')]) }}">
                                                @endif
                                            @else
                                                <img src="{{ URL::asset('ariol/assets/images/custom/file.svg') }}" alt="File">
                                            @endif
                                        </a>
                                    </td>
                                    <td style="width: 80%;">
                                        <?php $parts = explode('/', $file); ?>
                                        <a target="_blank" title="" href="{{ $file }}">{{ end($parts) }}</a>
                                    </td>
                                    @if ($destroyable)
                                        <td style="width: 10%;">
                                            <button class="btn btn-danger delete legitRipple" data-url="{{ $file }}" data-type="DELETE">
                                                <i class="icon-trash position-left"></i> Удалить
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <script src="{{ URL::asset('ariol/assets/js/core/libraries/jquery.min.js') }}"></script>
    <script>
        $(function () {
            $('<?php echo !empty($groupTab) ? 'form[data-group-tab=' . $groupTab . '] ' : null; ?>input[name=<?php echo $name; ?>]').val(files_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?>);

            $('#fileupload_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?>').fileupload({
                url: '/admin/file-uploader',
                maxFileSize: 25000000,
                previewCrop: true,
                autoUpload: true,
                destroy: function (e, data) {
                    swal({
                        title: "Вы уверены?",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Да, удалить",
                        cancelButtonText: "Нет, отменить",
                        closeOnConfirm: true
                    },
                    function() {
                        var $data = data.url.split('?file=');

                        $.ajax({
                            type: 'post',
                            url: '/delete-thumbnail',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {file: $data[1]},
                            success: function() {}
                        });

                        $.each(files_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?>, function (index, file) {
                            if (file === data.url.split('?file=').join('files/')) {
                                delete files_<?php echo $name; ?>[index];
                            }
                        });
                        $(data.context).remove();
                        $('<?php echo !empty($groupTab) ? 'form[data-group-tab=' . $groupTab . '] ' : null; ?>input[name=<?php echo $name; ?>]').val(files_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?>);
                    });
                },
                process: function(data, td) {
                    var $td = td.files[0].name;
                    var $td_name = $('[data-td-file-name="' + $td + '"]');

                    $td_name.prepend('<img src="/ariol/assets/images/custom/file.svg">');

                    var $arr = $td.split('.');

                    if ($.inArray($arr[1], ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'ico']) > -1) {
                        $('[data-td-file-name="' + $td + '"] span.preview').show();
                        $('[data-td-file-name="' + $td + '"] img').remove();
                    }
                },
                success: function (data) {
                    $.each(data.selectedFilesForUpload, function (index, file) {
                        files_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?>.push(file.url);
                    });

                    $('<?php echo !empty($groupTab) ? 'form[data-group-tab=' . $groupTab . '] ' : null; ?>input[name=<?php echo $name; ?>]').val(files_<?php echo !empty($groupTab) ? $groupTab . '_' : null; ?><?php echo $name; ?>);
                }
            });
        });
    </script>
</div>