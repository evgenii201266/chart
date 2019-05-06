<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <div class="input-group">
            <span class="input-group-addon"><i class="icon-calendar"></i></span>
            <input id="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}"
                   style="cursor: pointer;" class="form-control pickadate"
                   value="{{ $value }}" name="{{ $name }}" type="text"
                   autocomplete="off" placeholder="Выберите дату">
        </div>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>