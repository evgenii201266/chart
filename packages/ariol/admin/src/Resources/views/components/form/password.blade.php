<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="row">
        <div class="form-group col-sm-6">
            <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
                <strong>{{ $label }}</strong>
                @if ($require)
                    <span class="text-danger-800">*</span>
                @endif
            </label>
            <input class="form-control" name="{{ $name }}[]"
                   type="text" autocomplete="off" placeholder="{{ $placeholder }}">
            @if (! empty($description))
                <span class="help-block">{{ $description }}</span>
            @endif
        </div>
        <div class="form-group col-sm-6">
            <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
                {{ translate('admin.modules.packageItems.users.packageItems.repeat-password') }}
            </label>
            <input class="form-control" name="{{ $name }}[]"
                   type="text" autocomplete="off" placeholder="{{ $placeholder }}">
        </div>
    </div>
</div>