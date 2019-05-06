@extends('ariol::layouts.system')

@section('css_files')
    <link rel="stylesheet" href="{{ URL::asset('ariol/assets/custom/css/forms.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('ariol/assets/custom/css/common.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('ariol/assets/modules/system/localization/style.css') }}">
@endsection

@section('content')
    <div class="sidebar sidebar-secondary sidebar-default">
        <div class="sidebar-content">
            <div id="sidebar-localization" class="category-content no-padding">
                <ul id="languages" class="navigation navigation-language navigation-alt navigation-accordion">
                    <li class="navigation-header">
                        {{ translate('system.modules.packageItems.localization.packageItems.available-languages') }}
                    </li>
                    @foreach ($languages as $language)
                        @include('ariol::modules.system.includes.available-languages', [
                            'language' => $language,
                            'remove' => in_array($language['code'], ['en', 'ru']) ? false : true
                        ])
                    @endforeach
                </ul>
                <div class="p-r-15 p-b-10 p-l-15">
                    <button type="button" data-toggle="modal" data-target="#modal-create-language"
                            class="btn bg-slate-700 btn-block legitRipple">
                        {{ translate('system.modules.packageItems.localization.packageItems.add-language') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="lang-package" class="content-wrapper content-wrapper-secondary content-wrapper-localization">

    </div>
    <div id="modal-create-language" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h5 class="modal-title">
                        {{ translate('system.modules.packageItems.localization.packageItems.select-language') }}
                    </h5>
                </div>
                <form id="add-language" action="{{ url(config('ariol.admin-path') . '/system/localization/add-language') }}" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <select id="language" name="language" class="form-control language-select">
                                @include('ariol::modules.system.includes.list-languages', [
                                    'listLanguages' => $listLanguages
                                ])
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">
                            {{ translate('system.modules.packageItems.localization.packageItems.close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ translate('system.modules.packageItems.localization.packageItems.add') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js_files')
    <script src="{{ URL::asset('ariol/assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/js/plugins/forms/styling/switch.min.js') }}"></script>
    <script src="{{ URL::asset('ariol/assets/modules/system/localization/script.js') }}"></script>
@endsection