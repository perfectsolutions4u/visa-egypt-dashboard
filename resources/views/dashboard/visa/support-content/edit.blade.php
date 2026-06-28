@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('dashboard.visa-settings.update') }}" method="POST" class="page-body" id="support-content-app">
        @csrf
        @method('PUT')

        <x-dashboard.partials.breadcrumb title="Mobile Support" :hideFirst="true">
            <li class="breadcrumb-item active">Support Content</li>
        </x-dashboard.partials.breadcrumb>

        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Support Page Content</h5>
                            <span class="text-muted">Shown in the Visa Egypt mobile app Support screen.</span>
                        </div>
                        <div class="card-body">
                            <x-dashboard.form.input-text
                                error-key="title"
                                required
                                :value="old('title', $content['title'])"
                                name="title"
                                label-title="Page Title"
                            />

                            <x-dashboard.form.input-text
                                error-key="subtitle"
                                :value="old('subtitle', $content['subtitle'])"
                                name="subtitle"
                                label-title="Subtitle"
                            />

                            <hr>
                            <h6 class="mb-3">Contact Details</h6>

                            <x-dashboard.form.input-text
                                error-key="phone"
                                :value="old('phone', $content['phone'])"
                                name="phone"
                                label-title="Support Phone"
                            />

                            <x-dashboard.form.input-text
                                error-key="whatsapp"
                                :value="old('whatsapp', $content['whatsapp'])"
                                name="whatsapp"
                                label-title="WhatsApp Number"
                            />

                            <x-dashboard.form.input-text
                                error-key="email"
                                :value="old('email', $content['email'])"
                                name="email"
                                label-title="Support Email"
                            />

                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Frequently Asked Questions</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="addFaq">
                                    <i class="fa fa-plus"></i> Add FAQ
                                </button>
                            </div>

                            <div v-for="(faq, index) in faqs" :key="'faq-' + index" class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>FAQ @{{ index + 1 }}</strong>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeFaq(index)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label>Question</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :name="'faqs[' + index + '][question]'"
                                        v-model="faq.question"
                                        required
                                    >
                                </div>

                                <div class="form-group mb-0">
                                    <label>Answer</label>
                                    <textarea
                                        class="form-control"
                                        rows="3"
                                        :name="'faqs[' + index + '][answer]'"
                                        v-model="faq.answer"
                                        required
                                    ></textarea>
                                </div>
                            </div>

                            <p v-if="faqs.length === 0" class="text-muted">No FAQs yet. Click "Add FAQ" to create one.</p>

                            <x-dashboard.form.submit-button/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js-upper')
    <script src="{{ asset('assets/admin/js/vue.min.js') }}"></script>
    <script>
        new Vue({
            el: '#support-content-app',
            data() {
                return {
                    faqs: @json(old('faqs', $content['faqs'] ?? [])),
                };
            },
            methods: {
                addFaq() {
                    this.faqs.push({ question: '', answer: '' });
                },
                removeFaq(index) {
                    this.faqs.splice(index, 1);
                },
            },
        });
    </script>
@endpush
