<label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
<select name="{{ $name }}"
    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
    @foreach ($options as $option)
        <option value="{{ $option }}" @if ($selected == $option || old($name) == $option) selected @endif>
            {{ $option }}
        </option>
    @endforeach
</select>
