@foreach ($listLanguages as $code => $language)
    <option value="{{ $code }}" @if (! empty($selected) && $code == $selected) selected @endif>
        {{ $language }}
    </option>
@endforeach