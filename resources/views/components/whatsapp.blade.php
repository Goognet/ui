@props([
    'phone'   => null,
    'message' => null,
    'title'   => 'Vamos conversar?',
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Support\Whatsapp;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('whatsapp');
@endphp

<x-goognet-ui::link
    variant="primary"
    :href="Whatsapp::url($phone, $message)"
    external
    :title="$title"
    :class="ClassList::merge($ui->classes('base', ''), (string) $attributes->get('class'))"
    {{ $attributes->except('class') }}
>{{ $slot }}</x-goognet-ui::link>
