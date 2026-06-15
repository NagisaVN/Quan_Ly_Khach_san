<!-- Reusable Form Group Component -->
<div class="mb-3">
    @if (isset($label))
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if (isset($required) && $required)
        <span class="text-danger">*</span>
        @endif
    </label>
    @endif
    
    @php
        $inputClass = 'form-control';
        if ($errors->has($name)) {
            $inputClass .= ' is-invalid';
        }
    @endphp
    
    @if ($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }}"
            @if (isset($rows)) rows="{{ $rows }}" @endif
            @if (isset($placeholder)) placeholder="{{ $placeholder }}" @endif
            @if (isset($required) && $required) required @endif
        >{{ old($name, $value ?? '') }}</textarea>
    @elseif ($type === 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }}"
            @if (isset($required) && $required) required @endif
        >
            <option value="">-- Chọn --</option>
            @if (isset($options))
                @foreach ($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @if (old($name, $value ?? '') == $optionValue) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @endif
        </select>
    @else
        <input
            type="{{ $type ?? 'text' }}"
            id="{{ $name }}"
            name="{{ $name }}"
            class="{{ $inputClass }}"
            @if (isset($placeholder)) placeholder="{{ $placeholder }}" @endif
            @if (isset($required) && $required) required @endif
            @if (isset($pattern)) pattern="{{ $pattern }}" @endif
            @if (isset($min)) min="{{ $min }}" @endif
            @if (isset($max)) max="{{ $max }}" @endif
            value="{{ old($name, $value ?? '') }}"
        >
    @endif
    
    @if ($errors->has($name))
    <div class="invalid-feedback d-block">
        {{ $errors->first($name) }}
    </div>
    @endif
    
    @if (isset($help))
    <small class="form-text text-muted d-block mt-1">{{ $help }}</small>
    @endif
</div>
