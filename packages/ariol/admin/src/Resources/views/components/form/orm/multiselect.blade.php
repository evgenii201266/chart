<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <div class="multi-select-full">
            <select class="multiselect-select-all-filtering" name="{{ $name }}[]"
                    id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}" multiple>
                @if ($nullable)
                    <option {{ ($selected === 0) ? 'selected=selected' : null }}></option>
                @endif
                @foreach ($values as $value)
                    <option {{ in_array($value->id, $selected) ? 'selected' : null }}
                            value="{{ $value->id }}">{{ $value->$fieldName }}</option>
                @endforeach
            </select>
        </div>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>