@foreach ($languages as $language)
    <li @if ($language['code'] == Localization::getAdminLocale()) class="active" @endif
        data-language-code="{{ $language['code'] }}">
        <a>
            <img src="{{ URL::asset("languages/{$language['code']}.svg") }}" alt="{{ $language['code'] }}">
            {{ $language['name'] }}
        </a>
    </li>
@endforeach