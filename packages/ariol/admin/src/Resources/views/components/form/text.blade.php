<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <textarea class="form-control" id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}" name="{{ $name }}"
                  autocomplete="off" placeholder="{{ $placeholder }}"
                  {{ !empty($maxLength) ? 'maxlength=' . $maxLength : null }}
                  {{ !empty($readonly) ? 'readonly' : null }}>{{ $value }}</textarea>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>