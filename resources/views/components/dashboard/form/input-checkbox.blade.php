@props([
    'labelTitle'=> '',
    'id'=> Str::random(),
    'name',
    'value' => null,
    'required' => false,
    'resourceName' => '',
    'resourceDesc' => 'Enable',
    'class'=>'',
    'errorKey' => null,
])
<div class="form-group row align-items-center">
    <label for="{{ Str::kebab($id) }}" class="@if (!empty($class)) {{$class}} @else col-xl-3 col-md-4 @endif">{{ $labelTitle }}</label>
    <div class="@if (!empty($class)) {{$class}}  @else col-xl-6 col-md-6  @endif">
        <div class="checkbox checkbox-primary ">
            <input type="checkbox"
                   id="{{ Str::kebab($id) }}"
                   name="{{ $name }}"
                   data-original-title="{{ $labelTitle }}"
                   title="{{ $labelTitle }}"
                   @checked($value || ($errorKey && old($errorKey)) || ($errorKey && !is_null(old($errorKey))))
                   @if($required) required @endif >
                <label  for="{{ Str::kebab($id) }}" @if (!empty($class))
                class="arf"

                @endif>
                    @if(!empty($resourceName))
                        {{$resourceDesc}} This {{ $resourceName }}
                    @else
                        {{$resourceDesc}}
                    @endif
                </label>
        </div>
        @if($errorKey)
            @error($errorKey)
            <span class="d-block text-danger">{{ $message }}</span>
            @enderror
        @endif
    </div>
</div>
