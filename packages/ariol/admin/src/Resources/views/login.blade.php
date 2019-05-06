<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Вход в панель управления</title>

        <link rel="icon" href="{{ URL::asset('ariol/assets/favicon.ico') }}">

        <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/core.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/components.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('ariol/assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    </head>
    <body class="login-container login-cover">
        <div id="admin-path" class="page-container" data-admin-path="/{{ config('ariol.admin-path') }}">
            <div class="page-content">
                <div class="content-wrapper">
                    <div class="content pb-20">
                        <form id="admin-auth" action="{{ url('/' . config('ariol.admin-path') . '/login') }}" method="post" class="form-validate">
                            {{ csrf_field() }}
                            <div class="panel panel-body login-form">
                                <div class="text-center">
                                    <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>
                                    <h5 class="content-group">Авторизация</h5>
                                </div>
                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" class="form-control" placeholder="E-mail" name="email" required="required">
                                    <div class="form-control-feedback">
                                        <i class="icon-user text-muted"></i>
                                    </div>
                                </div>
                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="password" class="form-control" placeholder="Пароль" name="password" required="required">
                                    <div class="form-control-feedback">
                                        <i class="icon-lock2 text-muted"></i>
                                    </div>
                                </div>
                                <div class="form-group login-options">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" class="styled" name="remember" checked="checked">
                                                Запомнить меня
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button id="auth" type="submit" class="btn bg-slate btn-block">
                                        Войти <i class="icon-arrow-right14 position-right"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ URL::asset('ariol/assets/js/plugins/loaders/pace.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/core/libraries/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/core/libraries/bootstrap.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/loaders/blockui.min.js') }}"></script>

        <script src="{{ URL::asset('ariol/assets/js/plugins/forms/validation/validate.min.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

        <script src="{{ URL::asset('ariol/assets/js/core/app.js') }}"></script>
        <script src="{{ URL::asset('ariol/assets/js/plugins/notifications/noty.min.js') }}"></script>

        <script src="{{ URL::asset('ariol/assets/js/plugins/ui/ripple.min.js') }}"></script>

        <script src="{{ URL::asset('ariol/assets/js/plugins/forms/wizards/form_wizard/form.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('.styled').uniform({
                    radioClass: 'choice'
                });

                $('body').on('click', ':checkbox', function () {
                    var $checkbox = $(this).attr('checked', this.checked).prop('checked', this.checked);
                    $.uniform.update($checkbox);
                });

                $('#admin-auth').ajaxForm({
                    beforeSend: function() {
                        $('#auth').prop('disabled', true);
                    },
                    success: function(result) {
                        if (result.status) {
                            window.location.href = $('#admin-path').attr('data-admin-path');
                        } else {
                            $('#auth').prop('disabled', false);

                            noty({
                                width: 200,
                                text: result.message,
                                type: 'error',
                                dismissQueue: true,
                                timeout: 4000,
                                layout: 'topRight'
                            });
                        }
                    }
                });
            });
        </script>
    </body>
</html>