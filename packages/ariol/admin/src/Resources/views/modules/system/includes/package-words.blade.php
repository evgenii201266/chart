@foreach ($words as $key => $value)
    @if (is_array($value))
        @include('ariol::modules.system.includes.package-word', [
            'language' => $language,
            'subpoint' => $subpoint++,
            'key' => isset($value['packageSubTitle']) ? 'packageSubTitle' : null,
            'value' => isset($value['packageSubTitle']) ? $value['packageSubTitle'] : null,
            'translate' => isset($package[$key]['packageSubTitle']) ? $package[$key]['packageSubTitle'] : null,
            'field' => $field . '[' . $key . '][' . (! empty($value['packageSubTitle']) ? 'packageSubTitle' : null) . ']'
        ])
        @include('ariol::modules.system.includes.package-words', [
            'subpoint' => $subpoint++,
            'package' => !empty($value['packageItems'])
                ? (
                    isset($package[$key]['packageItems'])
                        ? $package[$key]['packageItems']
                        : []
                )
                : (isset($package[$key]) ? $package[$key] : []),
            'words' => !empty($value['packageItems']) ? $value['packageItems'] : $value,
            'field' => $field . '[' . $key . ']' . (! empty($value['packageItems']) ? '[packageItems]' : null)
        ])
        @php
            $subpoint = $subpoint - 2;
        @endphp
    @else
        @include('ariol::modules.system.includes.package-word', [
            'key' => $key,
            'value' => $value,
            'language' => $language,
            'subpoint' => $subpoint,
            'field' => $field . '[' . $key . ']',
            'translate' => isset($package[$key]) ? $package[$key] : null
        ])
    @endif
@endforeach