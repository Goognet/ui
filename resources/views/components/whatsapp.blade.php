@props([
    'phone'   => null,
    'message' => null,
    'title'   => 'Vamos conversar?',
])

@php
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Support\Whatsapp;

    $attributes = SafeUrl::attributes($attributes);
@endphp

<x-goognet-ui::link
    variant="primary"
    :href="Whatsapp::url($phone, $message)"
    external
    :title="$title"
    {{ $attributes }}
>{{ $slot }}</x-goognet-ui::link>
