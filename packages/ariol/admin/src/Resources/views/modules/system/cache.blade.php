@extends('ariol::layouts.master')

@section('css_files')
    <link rel="stylesheet" href="{{ URL::asset('ariol/assets/custom/css/forms.css') }}">
@endsection

@section('content')
    <div class="breadcrumb-line breadcrumb-line-component content-group-lg">
        <a class="breadcrumb-elements-toggle">
            <i class="icon-menu-open"></i>
        </a>
        <ul class="breadcrumb"></ul>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div id="system-cache" class="panel panel-dark">
                <div class="panel-heading">
                    <h6 class="panel-title">
                        {{ translate('system.modules.packageItems.cache-clear.packageItems.trash') }} -
                        <span id="cache-size">
                            {{ $cache }} {{ translate('system.modules.packageItems.cache-clear.packageItems.mb') }}
                        </span>
                    </h6>
                    <div class="heading-elements">
                        <button id="cache-clear" type="button" class="btn btn-danger heading-btn legitRipple">
                            {{ translate('system.modules.packageItems.cache-clear.packageItems.clear') }}
                        </button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height has-minimum-width" id="system-graphic"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_files')
    <script src="{{ URL::asset('ariol/assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
    <script>
        $(document).ready(function() {
            var basic_donut = '';

            require.config({
                paths: {
                    echarts: '/ariol/assets/js/plugins/visualization/echarts'
                }
            });
            require(
                [
                    'echarts',
                    'echarts/theme/limitless',
                    'echarts/chart/pie',
                    'echarts/chart/funnel'
                ],
                function (ec, limitless) {
                    basic_donut = ec.init(document.getElementById('system-graphic'), limitless);

                    chartOptions(basic_donut, [{{ $cacheSite }}, {{ $cacheSystem }}, {{ $cacheImages }}]);

                    window.onresize = function () {
                        setTimeout(function (){
                            basic_donut.resize();
                        }, 200);
                    }
                }
            );

            $('body').on('click', '#cache-clear', function() {
                var $block = $(this).closest('.panel-dark');

                $($block).block({
                    message: '<i class="icon-spinner4 spinner"></i>',
                    overlayCSS: {
                        backgroundColor: '#fff',
                        opacity: 0.8,
                        cursor: 'wait'
                    },
                    css: {
                        border: 0,
                        padding: 0,
                        backgroundColor: 'transparent'
                    },
                    onBlock: function() {
                        $.ajax({
                            type: 'post',
                            url: '/{{ config('ariol.admin-path') }}/system/cache/clear',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                $($block).unblock();

                                $('#cache-size').text(data.cache + ' {{ translate('system.modules.packageItems.cache-clear.packageItems.mb') }}');

                                chartOptions(basic_donut, [parseInt(data.cacheSite), parseInt(data.cacheSystem), parseInt(data.cacheImages)]);
                            }
                        });
                    }
                });
            });
        });

        function chartOptions(donut, data) {
            donut.setOption({
                legend: {
                    orient: 'vertical',
                    x: 'left',
                    data: [
                        '{{ translate('system.modules.packageItems.cache-clear.packageItems.cache-site') }}',
                        '{{ translate('system.modules.packageItems.cache-clear.packageItems.cache-system') }}',
                        '{{ translate('system.modules.packageItems.cache-clear.packageItems.useless-images') }}'
                    ]
                },
                color: [
                    '#ff6347', '#616b72', '#298db3'
                ],
                calculable: false,
                series: [
                    {
                        name: 'Cache',
                        type: 'pie',
                        radius: ['35%', '60%'],
                        center: ['50%', '60%'],
                        itemStyle: {
                            normal: {
                                label: {
                                    show: false
                                },
                                labelLine: {
                                    show: false
                                }
                            },
                            emphasis: {
                                label: {
                                    show: true,
                                    formatter: '{c} Мб' + '\n\n' + '({d}%)',
                                    position: 'center',
                                    textStyle: {
                                        fontSize: '17',
                                        fontWeight: '500'
                                    }
                                }
                            }
                        },
                        data: [
                            {
                                value: data[0] + 0.001,
                                name: '{{ translate('system.modules.packageItems.cache-clear.packageItems.cache-site') }}'
                            },
                            {
                                value: data[1] + 0.001,
                                name: '{{ translate('system.modules.packageItems.cache-clear.packageItems.cache-system') }}'
                            },
                            {
                                value: data[2] + 0.001,
                                name: '{{ translate('system.modules.packageItems.cache-clear.packageItems.useless-images') }}'
                            }
                        ]
                    }
                ]
            });
        }
    </script>
@endsection