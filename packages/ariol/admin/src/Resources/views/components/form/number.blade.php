<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <input id="{{ $name }}" class="form-control" name="{{ $name }}" value="{{ $value }}"
               type="text" autocomplete="off" placeholder="{{ $placeholder }}"
               oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');"
               {{ !empty($maxLength) ? 'maxlength=' . $maxLength : null }}>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>