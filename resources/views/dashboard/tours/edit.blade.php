@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.tours.update', $tour) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Tour" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.tours.index') }}">Tours</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert />
                <div class="w-100 text-right" style="color: blue; margin: 16px 0">
                    <a style="color: blue; margin: 16px 0" target="_blank" title="Visit On Site"
                        href="{{ $tour->site_url }}">{{ $tour->site_url }}</a>
                </div>
                {{-- Tour Basic Information --}}
                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="tours">


                            @foreach (config('translatable.supported_locales') as $localKey => $local)
                                <div @class([
                                    'tab-pane fade',
                                    'active show' => $localKey == config('app.locale'),
                                ]) id="{{ 'tours-' . $localKey }}" role="tabpanel"
                                    aria-labelledby="{{ 'tours-' . $localKey }}-tab">

                                    <x-dashboard.form.input-text error-key="{{ $localKey }}.title"
                                        name="{{ $localKey }}[title]" :value="$tour->translateOrNew($localKey)->title" id="{{ $localKey }}-title"
                                        label-title="Title" />

                                    <x-dashboard.form.input-editor error-key="{{ $localKey }}.overview"
                                        name="{{ $localKey }}[overview]" :value="$tour->translateOrNew($localKey)->overview"
                                        id="{{ $localKey }}-overview" label-title="Overview" />

                                    <x-dashboard.form.input-editor error-key="{{ $localKey }}.highlights"
                                        name="{{ $localKey }}[highlights]" :value="$tour->translateOrNew($localKey)->highlights"
                                        id="{{ $localKey }}-highlights" label-title="Highlights" />

                                    <x-dashboard.form.input-text error-key="{{ $localKey }}.included"
                                        name="{{ $localKey }}[included]" class="tags-input" :value="$tour->translateOrNew($localKey)->included"
                                        id="{{ $localKey }}-included" label-title="Included" />

                                    <x-dashboard.form.input-text error-key="{{ $localKey }}.excluded"
                                        name="{{ $localKey }}[excluded]" class="tags-input" :value="$tour->translateOrNew($localKey)->excluded"
                                        id="{{ $localKey }}-excluded" label-title="Excluded" />

                                    <x-dashboard.form.input-text error-key="{{ $localKey }}.duration"
                                        name="{{ $localKey }}[duration]" :value="$tour->translateOrNew($localKey)->duration"
                                        id="{{ $localKey }}-duration" label-title="Duration" />

                                    <x-dashboard.form.input-text error-key="{{ $localKey }}.type"
                                        name="{{ $localKey }}[type]" :value="$tour->translateOrNew($localKey)->type" id="{{ $localKey }}-type"
                                        label-title="Type" />

                                    <x-dashboard.form.input-text error-key="{{ $localKey }}.run"
                                        name="{{ $localKey }}[run]" :value="$tour->translateOrNew($localKey)->run" id="{{ $localKey }}-run"
                                        label-title="Run" />

                                    <x-dashboard.form.input-text error-key="{{ $localKey }}.pickup_time"
                                        name="{{ $localKey }}[pickup_time]" :value="$tour->translateOrNew($localKey)->pickup_time"
                                        id="{{ $localKey }}-pickup_time" label-title="PickupTime" />


                                </div>
                            @endforeach
                        </x-dashboard.form.language-multi-tab-card>
                        <x-dashboard.form.submit-button />
                    </div>
                </div>

                {{-- Tour Days --}}
                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="tour-days">
                            @foreach (config('translatable.supported_locales') as $localKey => $local)
                                <div @class([
                                    'tab-pane fade',
                                    'active show' => $localKey == config('app.locale'),
                                ]) id="{{ 'tour-days-' . $localKey }}" role="tabpanel"
                                    aria-labelledby="{{ 'tour-day-' . $localKey }}-tab">
                                    <h2>Tour Days</h2>
                                    <a href="javascript:;" data-remove-text="Remove Day" data-name="days"
                                        data-local="{{ $localKey }}" data-tab-id="tour-days"
                                        data-locals="{{ implode(',', array_keys(config('translatable.supported_locales'))) }}"
                                        class="text-center mb-4 btn btn-outline-primary w-100 add-new-variant">
                                        <i class="fa fa-plus"></i> Add Day
                                    </a>
                                    @foreach ($tour->days->isEmpty() ? [new \App\Models\TourDay()] : $tour->days as $day)
                                        <div class="row color-picks">
                                            <x-dashboard.form.input-text
                                                error-key="days.{{ $loop->index }}.{{ $localKey }}.title"
                                                name="days[{{ $loop->index }}][{{ $localKey }}][title]"
                                                :value="$day->translateOrNew($localKey)->title"
                                                id="days-{{ $loop->iteration }}-{{ $localKey }}-title"
                                                label-title="Title" />

                                            <x-dashboard.form.input-editor
                                                error-key="days.{{ $loop->index }}.{{ $localKey }}.description"
                                                name="days[{{ $loop->index }}][{{ $localKey }}][description]"
                                                :value="$day->translateOrNew($localKey)->description"
                                                id="days-{{ $loop->iteration }}-{{ $localKey }}-description"
                                                label-title="Description" />


                                            <a href="javascript:;"
                                                class="remove-variant text-center mb-4 btn btn-outline-primary w-100">
                                                <i class="fa fa-trash"></i> Remove Day
                                            </a>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </x-dashboard.form.language-multi-tab-card>
                        <x-dashboard.form.submit-button />
                    </div>
                </div>
                {{-- Tour Pricing & Gallery --}}
                <div class="card tab2-card">
                    <div class="card-body needs-validation add-product-form">
                        <x-dashboard.form.multi-tab-card :tabs="['basic', 'media', 'pricing']" tab-id="basic-media-pricing">
                            <div class="tab-pane fade active show" id="{{ 'basic-media-pricing-0' }}" role="tabpanel"
                                aria-labelledby="{{ 'basic-media-pricing-0' }}-tab">

                                <x-dashboard.form.input-text error-key="slug" name="slug" :value="$tour->slug"
                                    id="slug" label-title="Slug" />
                                <x-dashboard.form.input-text error-key="display_order" name="display_order"
                                    :value="$tour->display_order" id="display_order" label-title="Display Order" />
                                <x-dashboard.form.input-checkbox resource-name="Tour" error-key="enabled" name="enabled"
                                    id="enabled" :value="$tour->enabled" label-title="Enabled" />

                                <x-dashboard.form.input-checkbox resource-name="Tour" error-key="featured"
                                    name="featured" id="featured" :value="$tour->featured" label-title="Featured" />

                                <x-dashboard.form.input-text :required="true" :value="$tour->code" error-key="code"
                                    name="code" id="code" label-title="Code" />

                                <x-dashboard.form.input-text error-key="duration_in_days" name="duration_in_days"
                                    id="duration_in_days" :value="$tour->duration_in_days" label-title="Duration in days" />

                                <x-dashboard.form.input-select :value="$tour->categories->pluck('id')->toArray()" name="categories[]" multible
                                    :options="$relations['categories']" track-by="id" option-lable="title" label-title="Tour Category"
                                    id="categories" error-key="categories" />

                                <x-dashboard.form.input-select :value="$tour->options->pluck('id')->toArray()" name="options[]" multible
                                    :options="$relations['options']" track-by="id" option-lable="name" label-title="Tour Options"
                                    id="options" error-key="options" />


                                <x-dashboard.form.input-select :value="$tour->destinations->pluck('id')->toArray()" name="destinations[]" multible
                                    :options="$relations['destinations']" track-by="id" option-lable="title"
                                    label-title="Tour Destinations" id="destinations" error-key="destinations" />

                            </div>

                            <div class="tab-pane fade" id="{{ 'basic-media-pricing-1' }}" role="tabpanel"
                                aria-labelledby="{{ 'basic-media-pricing-1' }}-tab">
                                <x-dashboard.form.media title="Add Featured Image" :images="$tour->featured_image"
                                    name="featured_image" />

                                <x-dashboard.form.media title="Add Gallery" :multiple="true" :images="$tour->gallery"
                                    name="gallery[]" />
                            </div>

                            <div class="tab-pane fade" id="{{ 'basic-media-pricing-2' }}" role="tabpanel"
                                aria-labelledby="{{ 'basic-media-pricing-2' }}-tab">

                                <x-dashboard.form.input-text error-key="adult_price" name="adult_price" id="adult_price"
                                    :value="$tour->adult_price" label-title="Adult Price" />

                                <x-dashboard.form.input-text error-key="child_price" name="child_price" id="child_price"
                                    :value="$tour->child_price" label-title="Child Price" />

                                <x-dashboard.form.input-text error-key="infant_price" :value="$tour->infant_price"
                                    name="infant_price" id="infant_price" label-title="Infant Price" />

                                <a href="javascript:;" data-name="pricing_groups"
                                    class="add-new-variant text-center mb-4 btn btn-outline-primary w-100">
                                    <i class="fa fa-plus"></i> Add Group Pricing
                                </a>

                                @foreach ($tour->pricing_groups as $pricing_group)
                                    <div class="row color-picks">

                                        <x-dashboard.form.input-number error-key="pricing_groups.from"
                                            name="pricing_groups[{{ $loop->index }}][from]"
                                            id="from{{ $loop->index }}" :value="intval($pricing_group['from'])" label-title="From" />

                                        <x-dashboard.form.input-number error-key="pricing_groups.to"
                                            name="pricing_groups[{{ $loop->index }}][to]" id="to{{ $loop->index }}"
                                            :value="intval($pricing_group['to'])" label-title="To" />

                                        <x-dashboard.form.input-text error-key="pricing_groups.price"
                                            name="pricing_groups[{{ $loop->index }}][price]" :value="floatval($pricing_group['price'])"
                                            id="price{{ $loop->index }}" label-title="Adult Price" />


                                        <x-dashboard.form.input-text error-key="pricing_groups.child_price"
                                            name="pricing_groups[{{ $loop->index }}][child_price]" :value="floatval($pricing_group['child_price'])"
                                            id="child_price{{ $loop->index }}" label-title="Child Price" />
                                        @if ($loop->iteration > 1)
                                            <a href="javascript:;"
                                                class="remove-variant text-center mb-4 btn btn-outline-danger w-100">
                                                <i class="fa fa-trash"></i> Remove Group Pricing
                                            </a>
                                        @endif
                                        <hr>
                                    </div>
                                @endforeach
                            </div>

                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button />
                    </div>
                </div>
            </div>

            <!--Start SEO-->
            <x-dashboard.form.seo-form :seo="$tour->seo"/>
            <!--End SEO-->
        </div>


    </form>

    <x-dashboard.partials.resource-translation model="Tour" :id="$tour->id" />
@endsection
@push('js')
<script>
        function Check(input) {
            var parent = input.parentNode;
            var siblings = Array.from(parent.childNodes).filter(function(node) {
                return node.nodeType === 1 && node !== input;
            });
            var thirdSibling = siblings[2];
            var inputs = thirdSibling.querySelectorAll('input[type="checkbox"]');
            var isChecked = input.checked;
            inputs.forEach(function(input) {
                input.checked = isChecked;
            });
        }

</script>

@endpush
