<div class="sidebar sidebar-main">
    <div class="sidebar-content">
        <div class="sidebar-user-material">
            <div class="category-content">
                <div class="sidebar-user-material-content">
                    <a href="/{{ config('ariol.admin-path') }}/users">
                        <img src="{{ URL::asset('ariol/assets/images/custom/admin.svg') }}" class="img-responsive">
                    </a>
                    <h6>{{ Auth::user()->name }}</h6>
                </div>
                <div class="sidebar-user-material-menu">
                    <a href="#user-nav" data-toggle="collapse">
                        <span>Мой аккаунт</span> <i class="caret"></i>
                    </a>
                </div>
            </div>
            <div class="navigation-wrapper collapse" id="user-nav">
                <ul class="navigation">
                    <li>
                        <a href="{{ url('/admin/logout') }}">
                            <i class="icon-switch2 position-left"></i> <span>Выйти</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="sidebar-category sidebar-category-visible">
            <div id="sidebar-menu" class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">
                    @foreach (\config\menu::getMenu() as $item)
                        @include('ariol::includes.menu', $item)
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>