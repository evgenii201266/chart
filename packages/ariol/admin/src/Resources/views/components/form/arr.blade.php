<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    <div class="form-group">
        <label for="{{ (! empty($groupTab) ? $groupTab . '_' : null) . $name }}">
            <strong>{{ $label }}</strong>
            @if ($require)
                <span class="text-danger-800">*</span>
            @endif
        </label>
        <div class="array_type_data">
            @if ($value && is_array($value))
                @foreach ($value as $key => $item)
                    <div class="type_array">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <input class="form-control" name="{{ $name }}[]" value="{{ $item }}"
                                           type="text" autocomplete="off" placeholder="{{ $placeholder }}">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn legitRipple {{ ($key == 0) ? 'btn-success add_more_element_arr' : 'btn-danger remove_more_element_arr' }}">
                                            <i class="element_arr {{ ($key == 0) ? 'icon-add' : 'icon-trash' }}"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="type_array">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="input-group">
                                <input class="form-control" name="{{ $name }}[]" type="text"
                                       autocomplete="off" placeholder="{{ $placeholder }}">
                                <span class="input-group-btn">
                                        <button type="button" class="btn btn-success legitRipple add_more_element_arr element_arr">
                                            <i class="icon-add"></i>
                                        </button>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if (! empty($description))
            <span class="help-block">{{ $description }}</span>
        @endif
    </div>
</div>