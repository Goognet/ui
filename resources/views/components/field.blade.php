@props([
    'id'       => null,
    'name'     => null,
    'label'    => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Field;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('field');

    $fieldId = Field::id($id, $name);

    $message = Field::error($error, $name);
@endphp

<div
    class="{{ ClassList::merge($ui->classes('base', 'flex flex-col gap-1.5'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    @if (filled($label))
        <label for="{{ $fieldId }}" class="{{ $ui->classes('label', 'text-sm font-control text-neutral-800') }}">
            {{ $label }}

            @if ($required)
                <span class="{{ $ui->classes('required', 'text-red-600') }}" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if (filled($hint))
        <p id="{{ $fieldId }}-hint" class="{{ $ui->classes('hint', 'text-xs text-neutral-500') }}">{{ $hint }}</p>
    @endif

    @if (filled($message))
        {{-- `role="alert"` is what makes a message that appears after a failed submit be read out. --}}
        <p id="{{ $fieldId }}-error" role="alert" class="{{ $ui->classes('error', 'text-xs text-red-600') }}">
            {{ $message }}
        </p>
    @endif
</div>
