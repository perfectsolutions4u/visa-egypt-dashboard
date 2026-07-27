@props([
    'id',
    'name',
    'labelTitle',
    'options' => [],
    'value' => null,
    'errorKey' => null,
    'required' => false,
    'multible' => false,
    'trackBy' => null,
    'optionLable' => null,
])

<div class="form-group row">
    <label  class="col-xl-3 col-md-4" for="{{ $id }}">{{ $labelTitle }}</label>
    <div class="col-xl-8 col-md-7">
        <select class="custom-select select2 w-100 form-control"
                id="{{ $id }}"
                name="{{ $name }}"
                @if($multible) multiple @endif
                @if($required) required @endif>
            <option value="" disabled @if(!$multible) selected @endif>--Select Option--</option>
            @foreach($options as $key => $option)
                @php
                    if ($trackBy && (is_object($option) || is_array($option))) {
                        $optValue = data_get($option, $trackBy);
                        $optLabel = $optionLable
                            ? data_get($option, $optionLable)
                            : $optValue;
                    } else {
                        $optValue = $key;
                        $optLabel = $option;
                    }

                    $selectedValues = is_array($value) ? $value : [$value];
                    $isSelected = $value !== null && in_array((string) $optValue, array_map('strval', $selectedValues), true);
                @endphp
                <option
                    @if($isSelected) selected @endif
                    value="{{ $optValue }}">
                    {{ $trackBy ? $optLabel : Str::headline((string) $optLabel) }}
                </option>
            @endforeach
        </select>
        @if($errorKey)
            @error($errorKey)
            <span class="d-block text-danger">{{ $message }}</span>
            @enderror
            @error($errorKey.'.*')
            <span class="d-block text-danger">{{ $message }}</span>
            @enderror
        @endif
    </div>
</div>
