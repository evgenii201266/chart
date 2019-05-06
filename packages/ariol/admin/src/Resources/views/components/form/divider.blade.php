<div class="col-xs-12 col-md-{{ $column[0] }} col-md-offset-{{ $column[1] }} col-md-offset-right-{{ $column[2] }}">
    @if ($type == 'string')
        <div class="text-bold text-{{ $align }}"
             style="margin-top: {{ ($margin - 20) }}px; margin-bottom: {{ $margin }}px; font-size: {{ $size }}pt; color: {{ $color }};">
            <strong>{{ $label }}</strong>
        </div>
    @else
        <hr style="margin-top: {{ $margin }}px; margin-bottom: {{ $margin }}px; border-top: {{ $size }}px solid {{ $color }};">
    @endif
</div>