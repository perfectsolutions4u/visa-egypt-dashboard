@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.tours.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Tour" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.tours.index') }}">Tours</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="tours">
                            @foreach(config('translatable.supported_locales') as $localKey => $local)
                                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                                     id="{{ 'tours-'.$localKey }}" role="tabpanel"
                                     aria-labelledby="{{ 'tours-'.$localKey }}-tab">
                                    <x-dashboard.form.input-text error-key="{{$localKey}}.title"
                                                                 :value="old($localKey.'.title')"
                                                                 name="{{$localKey}}[title]" id="{{$localKey}}-title"
                                                                 label-title="Title"/>


                                    <x-dashboard.form.input-editor error-key="{{$localKey}}.overview"
                                                                   name="{{$localKey}}[overview]"
                                                                   id="{{$localKey}}-overview" label-title="Overview"/>

                                    <x-dashboard.form.input-editor error-key="{{$localKey}}.highlights"
                                                                   name="{{$localKey}}[highlights]"
                                                                   id="{{$localKey}}-highlights"
                                                                   label-title="Highlights"/>

                                    <x-dashboard.form.input-text error-key="{{$localKey}}.included"
                                                                 name="{{$localKey}}[included]" class="tags-input"
                                                                 id="{{$localKey}}-included" label-title="Included"/>

                                    <x-dashboard.form.input-text error-key="{{$localKey}}.excluded"
                                                                 name="{{$localKey}}[excluded]" class="tags-input"
                                                                 id="{{$localKey}}-excluded" label-title="Excluded"/>

                                    <x-dashboard.form.input-text error-key="{{$localKey}}.duration"
                                                                 name="{{$localKey}}[duration]"
                                                                 id="{{$localKey}}-duration" label-title="Duration"/>

                                    <x-dashboard.form.input-text error-key="{{$localKey}}.type"
                                                                 name="{{$localKey}}[type]" id="{{$localKey}}-type"
                                                                 label-title="Type"/>

                                    <x-dashboard.form.input-text error-key="{{$localKey}}.run" name="{{$localKey}}[run]"
                                                                 id="{{$localKey}}-run" label-title="Run"/>

                                    <x-dashboard.form.input-text error-key="{{$localKey}}.pickup_time"
                                                                 name="{{$localKey}}[pickup_time]"
                                                                 id="{{$localKey}}-pickup_time"
                                                                 label-title="PickupTime"/>


                                </div>
                            @endforeach
                        </x-dashboard.form.language-multi-tab-card>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>


                {{--Tour Days--}}
                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="tour-days">
                            @foreach(config('translatable.supported_locales') as $localKey => $local)
                                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                                     id="{{ 'tour-days-'.$localKey }}" role="tabpanel"
                                     aria-labelledby="{{ 'tour-day-'.$localKey }}-tab">
                                    <h4>Tour Days</h4>
                                    <a href="javascript:;"
                                       data-remove-text="Remove Day"
                                       data-name="days"
                                       data-local="{{ $localKey }}"
                                       data-tab-id="tour-days"
                                       data-locals="{{ implode(',', array_keys(config('translatable.supported_locales'))) }}"
                                       class="text-center mb-4 btn btn-outline-primary w-100 add-new-variant">
                                        <i class="fa fa-plus"></i> Add Day
                                    </a>
                                    <div class="row color-picks">
                                        <x-dashboard.form.input-text error-key="days.0.{{$localKey}}.title"
                                                                     name="days[0][{{$localKey}}][title]"
                                                                     id="days-1-{{$localKey}}-title" label-title="Title"/>

                                        <x-dashboard.form.input-editor error-key="days.0.{{$localKey}}.description"
                                                                       name="days[0][{{$localKey}}][description]"
                                                                       id="days-1-{{$localKey}}-description"
                                                                       label-title="Description"/>
                                        <hr/>
                                    </div>
                                </div>
                            @endforeach
                        </x-dashboard.form.language-multi-tab-card>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <div class="card tab2-card">
                    <div class="card-body needs-validation add-product-form">
                        <x-dashboard.form.multi-tab-card
                            :tabs="['basic', 'media', 'pricing']"
                            tab-id="basic-media-pricing">
                            <div class="tab-pane fade active show"
                                 id="{{ 'basic-media-pricing-0' }}" role="tabpanel"
                                 aria-labelledby="{{ 'basic-media-pricing-0' }}-tab">


                                <x-dashboard.form.input-text error-key="slug"
                                                             name="slug" id="slug"
                                                             label-title="Slug"/>

                                <x-dashboard.form.input-text error-key="display_order"
                                                             name="display_order"
                                                             id="display_order"
                                                             label-title="Display Order"/>

                                <x-dashboard.form.input-checkbox resource-name="Tour" error-key="enabled" name="enabled"
                                                                 id="enabled"
                                                                 label-title="Enabled"/>

                                <x-dashboard.form.input-checkbox resource-name="Tour" error-key="featured"
                                                                 name="featured" id="featured"
                                                                 label-title="Featured"/>


                                <x-dashboard.form.input-text :required="true" error-key="code" name="code" id="code"
                                                             label-title="Code"/>


                                <x-dashboard.form.input-text error-key="duration_in_days" name="duration_in_days" id="duration_in_days"
                                                             label-title="Duration in days"/>

                                <x-dashboard.form.input-select
                                    name="categories[]"
                                    multible
                                    :options="$relations['categories']"
                                    track-by="id"
                                    option-lable="title"
                                    label-title="Tour Category"
                                    id="categories"
                                    error-key="categories"/>

                                <x-dashboard.form.input-select
                                    name="options[]"
                                    multible
                                    :options="$relations['options']"
                                    track-by="id"
                                    option-lable="name"
                                    label-title="Tour Options"
                                    id="options"
                                    error-key="options"/>


                                <x-dashboard.form.input-select
                                    name="destinations[]"
                                    multible
                                    :options="$relations['destinations']"
                                    track-by="id"
                                    option-lable="title"
                                    label-title="Tour Destinations"
                                    id="destinations"
                                    error-key="destinations"/>

                            </div>

                            <div class="tab-pane fade"
                                 id="{{ 'basic-media-pricing-1' }}" role="tabpanel"
                                 aria-labelledby="{{ 'basic-media-pricing-1' }}-tab">
                                <x-dashboard.form.media title="Add Featured Image"
                                                        :images="old('gallery')"
                                                        name="featured_image"/>

                                <x-dashboard.form.media title="Add Gallery" :multiple="true"
                                                        :images="old('gallery')"
                                                        name="gallery[]"/>
                            </div>

                            <div class="tab-pane fade"
                                 id="{{ 'basic-media-pricing-2' }}" role="tabpanel"
                                 aria-labelledby="{{ 'basic-media-pricing-2' }}-tab">

                                <x-dashboard.form.input-text error-key="adult_price" name="adult_price" id="adult_price"
                                                             label-title="Adult Price"/>

                                <x-dashboard.form.input-text error-key="child_price" name="child_price" id="child_price"
                                                             label-title="Child Price"/>

                                <x-dashboard.form.input-text error-key="infant_price" name="infant_price" id="infant_price"
                                                             label-title="Infant Price"/>

                                <a href="javascript:;"
                                   data-name="pricing_groups"
                                   class="add-new-variant text-center mb-4 btn btn-outline-primary w-100">
                                    <i class="fa fa-plus"></i> Add Group Pricing
                                </a>

                                <div class="row color-picks">

                                    <x-dashboard.form.input-number error-key="pricing_groups.0.from"
                                                                   name="pricing_groups[0][from]"
                                                                   :id="'from-1'"
                                                                   label-title="From"/>

                                    <x-dashboard.form.input-number error-key="pricing_groups.0.to"
                                                                   name="pricing_groups[0][to]"
                                                                   :id="'to-1'"
                                                                   label-title="To"/>

                                    <x-dashboard.form.input-text  error-key="pricing_groups.0.price"
                                                                  name="pricing_groups[0][price]"
                                                                  id="price-1" label-title="Adult Price"/>


                                    <x-dashboard.form.input-text  error-key="pricing_groups.0.child_price"
                                                                  name="pricing_groups[0][child_price]"
                                                                  id="child_price-1" label-title="Child Price"/>
                                    <hr>
                                </div>


                            </div>

                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <!--Start SEO-->
                <x-dashboard.form.seo-form/>
                <!--End SEO-->

            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
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
