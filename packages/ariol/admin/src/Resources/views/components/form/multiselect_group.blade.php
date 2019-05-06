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
                @foreach ($values as $key => $value)
                    <optgroup label="{{ $value['name'] }}">
                        @foreach ($value['values'] as $id => $val)
                            <option {{ !empty($selected) && in_array($id, json_decode($selected)) ? 'selected=selected' : null }}
                                    value="{{ $id }}">{{ $val }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>