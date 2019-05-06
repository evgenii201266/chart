<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <input class="form-control autocomplete" id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}"
               name="{{ $name }}" value="{{ $outputValue }}" type="text" autocomplete="off"
               placeholder="" data-model="{{ $model }}" data-saving_field="{{ $savingField }}"
               data-output_field="{{ $outputField }}">
        <input class="form-control" name="{{ $name }}" value="{{ $value }}" type="hidden">
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>