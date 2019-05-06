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
        <link href="{{ URL::asset('ariol/assets/custom/css/common.css') }}" rel="stylesheet" type="text/css">

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
                  data-date-Jan="{{ translate('system.form.packageItems.date-and-time.packageItems.Jan') }}"
                  data-date-Feb="{{ translate('system.form.packageItems.date-and-time.packageItems.Feb') }}"
                  data-date-Mar="{{ translate('system.form.packageItems.date-and-time.packageItems.Mar') }}"
                  data-date-Apr="{{ translate('system.form.packageItems.date-and-time.packageItems.Apr') }}"
                  data-date-May="{{ translate('system.form.packageItems.date-and-time.packageItems.May') }}"
                  data-date-Jun="{{ translate('system.form.packageItems.date-and-time.packageItems.Jun') }}"
                  data-date-Jul="{{ translate('system.form.packageItems.date-and-time.packageItems.Jul') }}"
                  data-date-Aug="{{ translate('system.form.packageItems.date-and-time.packageItems.Aug') }}"
                  data-date-Sep="{{ translate('system.form.packageItems.date-and-time.packageItems.Sep') }}"
                  data-date-Oct="{{ translate('system.form.packageItems.date-and-time.packageItems.Oct') }}"
                  data-date-Nov="{{ translate('system.form.packageItems.date-and-time.packageItems.Nov') }}"
                  data-date-Dec="{{ translate('system.form.packageItems.date-and-time.packageItems.Dec') }}"
                  data-date-January="{{ translate('system.form.packageItems.date-and-time.packageItems.January') }}"
                  data-date-February="{{ translate('system.form.packageItems.date-and-time.packageItems.February') }}"
                  data-date-March="{{ translate('system.form.packageItems.date-and-time.packageItems.March') }}"
                  data-date-April="{{ translate('system.form.packageItems.date-and-time.packageItems.April') }}"
                  data-date-May="{{ translate('system.form.packageItems.date-and-time.packageItems.May') }}"
                  data-date-June="{{ translate('system.form.packageItems.date-and-time.packageItems.June') }}"
                  data-date-July="{{ translate('system.form.packageItems.date-and-time.packageItems.July') }}"
                  data-date-August="{{ translate('system.form.packageItems.date-and-time.packageItems.August') }}"
                  data-date-September="{{ translate('system.form.packageItems.date-and-time.packageItems.September') }}"
                  data-date-October="{{ translate('system.form.packageItems.date-and-time.packageItems.October') }}"
                  data-date-November="{{ translate('system.form.packageItems.date-and-time.packageItems.November') }}"
                  data-date-December="{{ translate('system.form.packageItems.date-and-time.packageItems.December') }}"
                  data-date-Sun="{{ translate('system.form.packageItems.date-and-time.packageItems.Sun') }}"
                  data-date-Mon="{{ translate('system.form.packageItems.date-and-time.packageItems.Mon') }}"
                  data-date-Tue="{{ translate('system.form.packageItems.date-and-time.packageItems.Tue') }}"
                  data-date-Wed="{{ translate('system.form.packageItems.date-and-time.packageItems.Wed') }}"
                  data-date-Thu="{{ translate('system.form.packageItems.date-and-time.packageItems.Thu') }}"
                  data-date-Fri="{{ translate('system.form.packageItems.date-and-time.packageItems.Fri') }}"
                  data-date-Sat="{{ translate('system.form.packageItems.date-and-time.packageItems.Sat') }}"
                  data-date-close="{{ translate('system.form.packageItems.date-and-time.packageItems.close') }}"
                  data-date-today="{{ translate('system.form.packageItems.date-and-time.packageItems.today') }}"
                  data-date-next-month="{{ translate('system.form.packageItems.date-and-time.packageItems.next-month') }}"
                  data-date-prev-month="{{ translate('system.form.packageItems.date-and-time.packageItems.prev-month') }}"
                  data-date-select-month="{{ translate('system.form.packageItems.date-and-time.packageItems.select-month') }}"
                  data-date-select-year="{{ translate('system.form.packageItems.date-and-time.packageItems.select-year') }}"
                  data-date-select-date-and-time="{{ translate('system.form.packageItems.date-and-time.packageItems.select-date-and-time') }}"
                  data-date-month="{{ translate('system.form.packageItems.date-and-time.packageItems.month') }}"
                  data-date-minutes="{{ translate('system.form.packageItems.date-and-time.packageItems.minutes') }}"
                  data-date-seconds="{{ translate('system.form.packageItems.date-and-time.packageItems.seconds') }}"
                  data-date-hour="{{ translate('system.form.packageItems.date-and-time.packageItems.hour') }}"
                  data-date-year="{{ translate('system.form.packageItems.date-and-time.packageItems.year') }}"
                  data-date-day="{{ translate('system.form.packageItems.date-and-time.packageItems.day') }}"
                  data-color-cancel="{{ translate('system.form.packageItems.color.packageItems.cancel') }}"
                  data-color-choose="{{ translate('system.form.packageItems.color.packageItems.choose') }}"
                  data-color-clear="{{ translate('system.form.packageItems.color.packageItems.clear') }}"
                  data-color-no-selected="{{ translate('system.form.packageItems.color.packageItems.no-selected') }}"
                  data-color-more="{{ translate('system.form.packageItems.color.packageItems.more') }}"
                  data-color-hide="{{ translate('system.form.packageItems.color.packageItems.hide') }}"
        ></language>
        @include('ariol::includes.header')
        <div id="main-admin-block" class="page-container"
             data-config-url="{{ config('ariol.admin-path') }}"
             data-current-language="{{ Localization::getAdminLocale() }}">
            <div class="page-content">
                @include('ariol::includes.sidebar')
                <div class="content-wrapper">
                    <div class="content">
                        @yield('content')

                        <a id="scroll-to-bottom" class="btn bg-danger-400 btn-float btn-rounded btn-icon legitRipple fab-menu-btn fab-menu fab-menu-absolute fab-menu-top-right fab-menu-opacity affix hidden">
                            <i class="icon-arrow-down8"></i>
                        </a>

                        <a id="scroll-to-top" class="btn bg-danger-400 btn-float btn-rounded btn-icon legitRipple fab-menu-btn fab-menu fab-menu-fixed fab-menu-bottom-right fab-menu-opacity hidden">
                            <i class="icon-arrow-up8"></i>
                        </a>
                    </div>
                </div>
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
        <script src="{{ URL::asset('ariol/assets/js/plugins/scrollbar/main.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/custom/js/functions.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/custom/js/common.js') }}"></script>
        @yield('js_files')
        <script src="{{ URL::asset('ariol/assets/custom/js/custom.js?v=2') }}"></script>
    </body>
</html>