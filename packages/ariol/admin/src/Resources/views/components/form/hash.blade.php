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
                <div class="sortable_hash">
                    @foreach ($value as $key => $item)
                        <div class="type_array clearfix">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <input class="form-control first" name="{{ $name }}[]" value="{{ $item['key'] }}"
                                                   type="text" autocomplete="off"
                                                   placeholder="{{ !empty($placeholder[0]) ? $placeholder[0] : null }}">
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="input-group">
                                                <input class="form-control" name="{{ $name }}[]" value="{{ $item['val'] }}"
                                                       type="text" autocomplete="off"
                                                       placeholder="{{ !empty($placeholder[1]) ? $placeholder[1] : null }}">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn {{ ($key == 0) ? 'btn-success' : 'btn-danger' }} legitRipple element_arr {{ ($key == 0) ? 'add_more_element_hash' : 'remove_more_element_hash' }}">
                                                        <i class="{{ ($key == 0) ? 'icon-add' : 'icon-trash' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary legitRipple">
                                                        <i class="icon-move"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sortable_hash">
                    <div class="type_array clearfix">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6">
                                        <input class="form-control first" name="{{ $name }}[]" type="text" autocomplete="off"
                                               placeholder="{{ !empty($placeholder[0]) ? $placeholder[0] : 'Название' }}">
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="input-group">
                                            <input class="form-control" name="{{ $name }}[]" type="text" autocomplete="off"
                                                   placeholder="{{ !empty($placeholder[1]) ? $placeholder[1] : 'Значение' }}">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-success legitRipple add_more_element_hash element_arr">
                                                    <i class="icon-add"></i>
                                                </button>
                                                <button type="button" class="btn btn-primary legitRipple">
                                                    <i class="icon-move"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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