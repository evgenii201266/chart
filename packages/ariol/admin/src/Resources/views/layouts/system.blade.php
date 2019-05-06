<!DOCTYPE html>
<html lang="{{ Localization::getAdminLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ translate('admin.packageTitle') }}</title>

        <link rel="icon" href="{{ URL::asset('ariol/assets/favicon.ico') }}">

        <link href="//fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/bootstrap.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('ariol/assets/css/core.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/components.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/scrollbar.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/custom.css') }}" rel="stylesheet" type="text/css">

        @yield('css_files')

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <language data-loading="{{ translate('system.grid.packageItems.loading') }}"
                  data-delete-selected="{{ translate('system.grid.packageItems.delete-selected') }}"
                  data-search="{{ translate('system.grid.packageItems.search') }}" data-show="{{ translate('system.grid.packageItems.show') }}"
                  data-first="{{ translate('system.grid.packageItems.first') }}" data-last="{{ translate('system.grid.packageItems.last') }}"
                  data-no-data="{{ translate('system.grid.packageItems.no-data') }}" data-no-selected="{{ translate('system.grid.packageItems.no-selected') }}"
                  data-yes-delete="{{ translate('system.grid.packageItems.yes-delete') }}" data-no-cancel="{{ translate('system.grid.packageItems.no-cancel') }}"
                  data-delete-are-you-sure="{{ translate('system.grid.packageItems.delete-are-you-sure') }}" data-delete-success="{{ translate('system.grid.packageItems.delete-success') }}"
                  data-select-item="{{ translate('system.form.packageItems.select-item') }}" data-no-founded="{{ translate('system.form.packageItems.no-founded') }}"
                  data-select-all="{{ translate('system.form.packageItems.select-all') }}" data-all-selected="{{ translate('system.form.packageItems.all-selected') }}"
                  data-selected="{{ translate('system.form.packageItems.selected') }}" data-destroyed="{{ translate('system.form.packageItems.destroyed') }}"
                  data-language-delete-are-you-sure="{{ translate('system.modules.packageItems.localization.packageItems.delete-are-you-sure') }}"
                  data-add-language-success="{{ translate('system.modules.packageItems.localization.packageItems.add-language-success') }}"
        ></language>
        @include('ariol::includes.header')
        <div class="page-container" data-config-url="{{ config('ariol.admin-path') }}"
             data-current-language="{{ Localization::getAdminLocale() }}">
            <div class="page-content">
                @include('ariol::includes.sidebar')
                @yield('content')

                <a id="scroll-to-bottom" class="btn bg-danger-400 btn-float btn-rounded btn-icon legitRipple fab-menu-btn fab-menu fab-menu-absolute fab-menu-top-right fab-menu-opacity affix hidden">
                    <i class="icon-arrow-down8"></i>
                </a>

                <a id="scroll-to-top" class="btn bg-danger-400 btn-float btn-rounded btn-icon legitRipple fab-menu-btn fab-menu fab-menu-fixed fab-menu-bottom-right fab-menu-opacity hidden">
                    <i class="icon-arrow-up8"></i>
                </a>
            </div>
        </div>

        <script src="{{ URL::asset('ariol/assets/js/plugins/loaders/pace.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/core/libraries/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/core/libraries/jquery_ui/full.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/core/libraries/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/loaders/blockui.min.js') }}"></script>

        <script src="{{ URL::asset('ariol/assets/js/core/app.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/ui/ripple.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/notifications/noty.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/forms/wizards/form_wizard/form.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/forms/selects/select2.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/scrollbar/main.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/custom/js/functions.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/custom/js/common.js') }}"></script>
        @yield('js_files')
    </body>
</html>