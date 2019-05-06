@extends('ariol::layouts.master')

@section('css_files')
    <link rel="stylesheet" href="{{ URL::asset('ariol/assets/custom/css/datatables.css') }}">
@endsection

@section('content')
    <div class="breadcrumb-line breadcrumb-line-component content-group-lg">
        <ul class="breadcrumb">

        </ul>
        @if ($creatable)
            <ul class="breadcrumb-elements">
                <li>
                    <a href="{{ $createUrl }}" class="legitRipple">
                        <i class="icon-plus3 position-left"></i> Создать
                    </a>
                </li>
            </ul>
        @endif
    </div>
    <div class="panel panel-flat" data-table-model="{{ $model }}">
        <table id="datatable" class="table table-bordered table-hover datatable-highlight">
            <thead>
                <tr>
                    <th class="text-center" style="width: 60px;">
                        <label for="select-all" class="checkbox-table">
                            <input id="select-all" value="1" class="styled" type="checkbox">
                        </label>
                    </th>
                    @foreach ($fields as $field)
                        <th @if (isset($field['width']) && $field['width'] != 0) style="width: {{ $field['width'] }}px;" @endif>
                            {{ !empty($field['title']) ? $field['title'] : null }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr id="tables-loading" class="hidden">
                    <th class="text-center" colspan="100%">
                        <img src="{{ URL::asset('ariol/assets/images/custom/loading.gif') }}" alt="Загрузка...">
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
    <input name="data-url" id="data-url" value="{{ $dataUrl }}" type="hidden">
@endsection

@section('js_files')
    <script src="{{ URL::asset('ariol/assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/pages/datatables_advanced.js') }}"></script>
    @if (isset($unSortableFields))
        <script>
            var dtData = {
                arrayJSONColTable: [],
                initSortable: function (field) {
                    dtData.arrayJSONColTable.push({
                        "bSortable": false,
                        "aTargets": [parseInt(field)]
                    });
                }
            };
        </script>
        @foreach ($unSortableFields as $unSortableField)
            <script>
                if (dtData.typeof !== 'undefined') {
                    dtData.initSortable("<?php echo $unSortableField; ?>");
                }
            </script>
        @endforeach
    @endif
    <script src="{{ URL::asset('ariol/assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/tables/datatables/extensions/buttons.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/custom/js/datatables.js?v=2') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/forms/styling/switch.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/pages/form_inputs.js') }}"></script>
@endsection