@props([
    'name',
    'labelTitle',
    'items' => [],
    'errorKey' => null,
    'addLabel' => 'Add item',
])

@php
    $rows = old($name, $items);
    if (! is_array($rows) || count($rows) === 0) {
        $rows = [['title' => '', 'description' => '']];
    }
@endphp

<div class="form-group row visa-item-list" data-field="{{ $name }}">
    <label class="col-xl-3 col-md-4">{{ $labelTitle }}</label>
    <div class="col-xl-8 col-md-7">
        <div class="visa-item-rows">
            @foreach($rows as $index => $row)
                <div class="visa-item-row border rounded p-3 mb-2 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="visa-item-index">Item {{ $index + 1 }}</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger visa-item-remove">
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="mb-2">
                        <label class="form-label mb-1">Title</label>
                        <input
                            type="text"
                            class="form-control"
                            name="{{ $name }}[{{ $index }}][title]"
                            value="{{ $row['title'] ?? '' }}"
                            placeholder="Title"
                        >
                    </div>
                    <div>
                        <label class="form-label mb-1">Description</label>
                        <textarea
                            class="form-control"
                            name="{{ $name }}[{{ $index }}][description]"
                            rows="2"
                            placeholder="Description"
                        >{{ $row['description'] ?? '' }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-sm btn-outline-primary visa-item-add mt-1">
            <i class="fa fa-plus"></i> {{ $addLabel }}
        </button>

        @if($errorKey)
            @error($errorKey)
                <span class="d-block text-danger mt-1">{{ $message }}</span>
            @enderror
            @error($errorKey.'.*')
                <span class="d-block text-danger mt-1">{{ $message }}</span>
            @enderror
        @endif
    </div>
</div>
