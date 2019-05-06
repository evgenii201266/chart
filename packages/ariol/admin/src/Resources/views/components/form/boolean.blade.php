<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label class="checkbox-inline">
            <input id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}"
                   name="{{ $name }}" class="styled" type="checkbox" value="1"
                   {{ ($value == 1 || (empty($value) && $checked)) ? 'checked=checked' : null }}>
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>