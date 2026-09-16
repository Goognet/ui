@props([
    'name'  => null,
    'label' => 'Abas',
])

@php
    use Goognet\Ui\Support\SafeUrl;

    $attributes = SafeUrl::attributes($attributes);

    /** Radios only group when they share a name, and `@aware` cannot read a `@props` default. */
    if (blank($name)) {
        throw new InvalidArgumentException('The tabs component requires a name, which groups its radios: name="produto".');
    }
@endphp

{{-- Radios, not buttons: arrow keys and checked state come from the browser, so panels toggle in CSS alone. --}}
<div role="radiogroup" aria-label="{{ $label }}" {{ $attributes->class(['flex flex-wrap items-end']) }}>
    <span aria-hidden="true" class="order-1 -mt-0.5 w-full border-b border-neutral-200"></span>

    {{ $slot }}
</div>
