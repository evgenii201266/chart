@extends('ariol::layouts.master')

@section('css_files')
    <link rel="stylesheet" href="{{ URL::asset('ariol/assets/custom/css/forms.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('ariol/assets/custom/css/datatables.css') }}">
@endsection

@section('content')
    <div class="breadcrumb-line breadcrumb-line-component content-group-lg">
        <ul class="breadcrumb">

        </ul>
    </div>
    <div class="tabbable">
        {!! $form !!}
    </div>
@endsection

@section('js_files')
    <script src="//maps.google.com/maps/api/js?key=AIzaSyAIVofgnW64-MOFlp0MUT7V549v9QWqaG4&amp;language=ru&amp;libraries=places"></script>
    <script src="{{ URL::asset('/ariol/assets/js/plugins/pickers/location/autocomplete_addresspicker.js') }}"></script>
    {{-- Форма. --}}
    <script src="{{ URL::asset('ariol/assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/forms/mask.min.js') }}"></script>
    {{-- "Выбиратели". --}}
    <script src="{{ URL::asset('ariol/assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/pickers/color/spectrum.js') }}"></script>
    {{-- Текстовые редакторы. --}}
    <script src="{{ URL::asset('ariol/assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/editors/summernote/lang/summernote-ru-RU.js') }}"></script>
    <script src="{{ URL::asset('ariol/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/ckeditor/ckeditor.js') }}"></script>
    {{-- Загрузка и обработка файлов. --}}
    <script src="{{ URL::asset('ariol/assets/custom/js/tmpl.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/load-image.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/canvas-to-blob.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jquery.blueimp-gallery.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/vendor/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.fileupload-process.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.fileupload-image.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.fileupload-audio.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.fileupload-video.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.fileupload-validate.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/jupload/jquery.fileupload-ui.js') }}"></script>
    <script id="template-upload" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-upload fade">
                <td data-td-file-name="{%=file.name%}" style="width: 10%;">
                    <span class="preview"></span>
                </td>
                <td style="width: 40%;">
                    <span data-file-name="{%=file.name%}">{%=file.name%}</span>
                    <strong class="error text-danger"></strong>
                </td>
                <td class="text-center" style="width: 15%;">
                    Обработка
                    <div class="progress progress-striped active" role="progressbar">
                        <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                    </div>
                </td>
                <td class="text-center" style="width: 35%;">
                    {% if (!i && !o.options.autoUpload) { %}
                        <button class="btn btn-primary start legitRipple">
                            <i class="icon-upload position-left"></i> Загрузить
                        </button>
                    {% } %}
                    {% if (!i) { %}
                        <button class="btn btn-warning cancel legitRipplel">
                            <i class="icon-blocked position-left"></i> Отменить
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
                <td style="width: 10%;">
                    {% if (file.thumbnailUrl) { %}
                        <a target="_blank" class="fancybox" href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery>
                            <img src="{%=file.thumbnailUrl%}">
                        </a>
                    {% } else { %}
                        <img src="/ariol/assets/images/custom/file.svg">
                    {% } %}
                </td>
                <td style="width: 40%;">
                    {% if (file.url) { %}
                        <a target="_blank" href="{%=file.url%}" title="{%=file.name%}">{%=file.name%}</a>
                    {% } else { %}
                        <span>{%=file.name%}</span>
                    {% } %}
                    {% if (file.error) { %}
                        <div><span class="label label-danger">Ошибка</span> {%=file.error%}</div>
                    {% } %}
                </td>
                <td class="text-center" style="width: 20%;">
                    {%=o.formatFileSize(file.size)%}
                </td>
                <td class="text-center" style="width: 30%;">
                    {% if (file.deleteUrl) { %}
                        <button class="btn btn-danger delete legitRipple" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                            <i class="icon-trash position-left"></i> Удалить
                        </button>
                    {% } else { %}
                        <button class="btn btn-warning cancel legitRipple">
                            <i class="icon-blocked position-left"></i> Отменить
                        </button>
                    {% } %}
                </td>
            </tr>
        {% } %}
    </script>
@endsection