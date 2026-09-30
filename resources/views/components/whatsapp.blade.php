@props([
    'phone'   => null,
    'message' => null,
    'title'   => 'Vamos conversar?',
    'as'      => null,
    'variant' => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Support\Whatsapp;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('whatsapp');

    /** `link` keeps the conversation inline in a sentence; `button` gives it the weight of an action. */
    $as ??= $ui->default('as', 'link');

    $isButton = $as === 'button';

    /** The variant travels to whichever component renders, and each one owns the name's meaning. */
    $variant ??= $ui->default('variant', 'primary');
@endphp

<x-dynamic-component
    :component="$isButton ? 'goognet-ui::button' : 'goognet-ui::link'"
    :variant="$variant"
    :href="Whatsapp::url($phone, $message)"
    external
    :title="$title"
    :class="ClassList::merge($ui->classes('base', ''), (string) $attributes->get('class'))"
    {{ $attributes->except('class') }}
>{{ $slot }}</x-dynamic-component>
