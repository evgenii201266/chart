@if (! isset($item['role']) || (isset($item['role']) &&
    ((is_array($item['role']) && in_array(Auth::user()->role->alias, $item['role'])) ||
    Auth::user()->role->alias == $item['role'])))
    @if (isset($item['type']) && $item['type'] == 'divider')
        <li class="navigation-header">
            <span class="sidebar-menu-name">{{ $item['name'] }}</span>
            <i class="icon-menu" data-original-title="{{ $item['name'] }}"></i>
        </li>
    @else
        @if (! empty($item['sub']))
            <li>
                <a class="legitRipple has-ul">
                    {!! !empty($item['icon']) ? $item['icon'] : null !!}
                    <span>
                        <span class="sidebar-menu-name">{{ $item['name'] }}</span>
                        @if (isset($item['count']))
                            <span class="label bg-blue-400">
                                {{ $item['count'] }}
                            </span>
                        @endif
                    </span>
                </a>
                <ul class="hidden-ul">
                    @foreach ($item['sub'] as $item)
                        @include('ariol::includes.menu', $item)
                    @endforeach
                </ul>
            </li>
        @else
            <li {{ Request::is(config('ariol.admin-path') . '/' . $item['url'] . '*') ? 'class=active' : null }}>
                <a href="/{{ config('ariol.admin-path') . '/' . $item['url'] }}" class="legitRipple">
                    {!! !empty($item['icon']) ? $item['icon'] : null !!}
                    <span>
                        <span class="sidebar-menu-name">{{ $item['name'] }}</span>
                        @if (isset($item['count']))
                            <span class="label bg-blue-400">
                                {{ $item['count'] }}
                            </span>
                        @endif
                    </span>
                </a>
            </li>
        @endif
    @endif
@endif