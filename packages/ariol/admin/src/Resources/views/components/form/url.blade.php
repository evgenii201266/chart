<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <input id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}" class="form-control" name="{{ $name }}"
               value="{{ $value }}" type="url" autocomplete="off" placeholder="{{ $placeholder }}"
               {{ !empty($maxLength) ? 'maxlength=' . $maxLength : null }}>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>