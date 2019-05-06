@if (! empty($key))
    <tr>
        <td>
            @for ($i = 0; $i < $subpoint; $i++)
                <i class="icon-circle-small"></i>
            @endfor
            @if (in_array($key, ['packageTitle', 'packageSubTitle']))
                <span class="f-w-600">{{ $value }}</span>
            @else
                <span>{{ $value }}</span>
            @endif
        </td>
        <td>
            <input type="text" autocomplete="off"
                   value="{{ $value != $translate || (!preg_match('/[А-Яа-яЁё]/u', $value) && $language != 'by') ? $translate : null }}"
                   name="{{ $field }}" placeholder="{{ translate('system.modules.packageItems.localization.packageItems.enter-a-translation') }}"
                   class="form-control {{ in_array($key, ['packageTitle', 'packageSubTitle']) ? 'placeholder-bold f-w-600' : null }}">
        </td>
    </tr>
@endif