<div class="navbar navbar-default navbar-fixed-top header-highlight">
    <div class="navbar-header">
        <a class="navbar-brand" href="/">
            <img src="{{ URL::asset('ariol/assets/images/custom/admin-logo.png') }}" alt="">
        </a>
        <ul class="nav navbar-nav visible-xs-block">
            <li>
                <a class="sidebar-mobile-main-toggle">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav">
            <li>
                <a class="sidebar-control sidebar-main-toggle hidden-xs">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>
        </ul>
        @if (config('ariol.multi-languages'))
            <ul class="nav navbar-nav navbar-right m-r-50">
                <li class="dropdown language-switch">
                    <a id="current-language" class="dropdown-toggle" data-toggle="dropdown">
                        @include('ariol::includes.current-language', [
                            'language' => $currentLanguage
                        ])
                    </a>
                    <ul id="admin-languages" class="dropdown-menu">
                        @include('ariol::includes.languages', [
                            'languages' => $activeLanguages
                        ])
                    </ul>
                </li>
            </ul>
        @endif
    </div>
</div>