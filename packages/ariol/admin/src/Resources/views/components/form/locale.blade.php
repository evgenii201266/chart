<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <input id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}" data-id-address="{{ $name }}"
               class="addresspicker form-control" name="{{ $name }}[]"
               placeholder="{{ $placeholder }}" value="{{ $value[0] }}">
        <input type="hidden" id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}_lat"
               name="{{ $name }}[]"
               value="{{ $value[1] }}">
        <input type="hidden" id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}_lng"
               name="{{ $name }}[]"
               value="{{ $value[2] }}">
    </div>
    <div class="form-group">
        <div id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}_map" class="map-wrapper content-group-sm"></div>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>