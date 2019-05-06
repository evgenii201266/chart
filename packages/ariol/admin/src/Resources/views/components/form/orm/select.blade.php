<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <select data-live-search="true" class="form-control" name="{{ $name }}"
                id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            @if ($nullable)
                <option {{ ($selected === 0) ? 'selected=selected' : null }}></option>
            @endif
            @foreach ($values as $value)
                <option {{ ($value['id'] == $selected) ? 'selected' : null }}
                        value="{{ $value['id'] }}">{{ $value[$fieldName] }}</option>
            @endforeach
        </select>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>