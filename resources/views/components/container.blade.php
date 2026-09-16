@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);
@endphp

<div {{ $attributes->class(['mx-auto w-full max-w-7xl px-5 md:px-8 lg:px-12']) }}>{{ $slot }}</div>
