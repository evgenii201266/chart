<li data-language-select="{{ $language['code'] }}">
    <a {{ !$remove ? 'class=no-remove' : null }}>
        <div class="radio radio-language-default" data-container="body" data-toggle="tooltip"
             title="{{ translate('system.modules.packageItems.localization.packageItems.tooltip-default-language') }}">
            <label>
                <input type="radio" name="default-language" class="styled" value="{{ $language['code'] }}"
                       {{ $language['default'] == 'on' ? 'checked=checked' : null }}>
            </label>
        </div>
        <img src="{{ URL::asset('languages/' . $language['code'] . '.svg') }}"
             class="localization-language" alt="{{ $language['code'] }}">
        <span class="localization-name">{{ $language['name'] }}</span>
        @if ($remove)
            <span class="label localization-remove localization-label-not-background"
                  title="{{ translate('system.modules.packageItems.localization.packageItems.tooltip-remove-language') }}"
                  data-container="body" data-toggle="tooltip">
                <i class="icon-cross2"></i>
            </span>
        @endif
        <span class="label localization-label-not-background"
              title="{{ translate('system.modules.packageItems.localization.packageItems.tooltip-active-language') }}"
              data-container="body" data-toggle="tooltip">
            <label class="no-margin-bottom language-checkbox">
                <input type="checkbox" class="styled"
                       {{ $language['active'] == 'on' ? 'checked="checked"' : null }}>
            </label>
        </span>
    </a>
</li>