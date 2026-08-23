@php
    $featuresText = old(
        'features',
        isset($additionalService) && is_array($additionalService->features)
            ? implode("\n", $additionalService->features)
            : ''
    );
@endphp

<x-dashboard.form.input-textarea
    error-key="features"
    name="features"
    id="features"
    label-title="Features (one per line, shown as small tags in the app. A line starting with No is highlighted in red)"
    :value="$featuresText"
/>
