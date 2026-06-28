@php
    $featuresText = old(
        'features',
        isset($servicePackage) && is_array($servicePackage->features)
            ? implode("\n", $servicePackage->features)
            : ''
    );
@endphp

<x-dashboard.form.input-textarea
    error-key="features"
    name="features"
    id="features"
    label-title="Features (one per line)"
    :value="$featuresText"
/>
