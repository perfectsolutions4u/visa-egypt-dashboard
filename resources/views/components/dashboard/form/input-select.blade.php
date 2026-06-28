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
                    // For simple key-value arrays (like cities, trip types)
                    $optValue = $key;
                    $optLabel = $option;
                @endphp
                <option
                    @if($value)
                        @if($multible ?
                            (is_array($value) && in_array($optValue, $value)) :
                             $value == $optValue)
                            selected
                        @endif
                    @endif
                    value="{{ $optValue }}">
                    {{ Str::headline($optLabel) }}
                </option>
            @endforeach
        </select>
        @if($errorKey)
            @error($errorKey)
            <span class="d-block text-danger">{{ $message }}</span>
            @enderror
        @endif
    </div>
</div>
