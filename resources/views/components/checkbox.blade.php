@props([
    'name'     => null,
    'id'       => null,
    'value'    => '1',
    'label'    => null,
    'hint'     => null,
    'error'    => null,
    'checked'  => false,
    'required' => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Field;
    use Goognet\Ui\Support\FormControl;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('checkbox');

    $fieldId = Field::id($id, $name . '-' . $value);

    $message = Field::error($error, $name);

    /** A form sent back keeps what was ticked, the same way the text fields do. */
    $submitted = Field::old($name);

    $checked = $checked || (filled($submitted) && $submitted === (string) $value);

    $described = Field::describedBy([$fieldId . '-hint' => filled($hint), $fieldId . '-error' => filled($message)]);
@endphp

<div class="{{ ClassList::merge($ui->classes('base', 'flex flex-col gap-1.5'), (string) $attributes->get('class')) }}">
    <label for="{{ $fieldId }}" class="{{ $ui->classes('label', FormControl::CHOICE_LABEL) }}">
        <input
            id="{{ $fieldId }}"
            type="checkbox"
            value="{{ $value }}"
            @if (filled($name)) name="{{ $name }}" @endif
            @checked($checked)
            @if ($required) required @endif
            class="{{ $ui->classes('control', FormControl::CHOICE . ' rounded-sm') }}"
            @if (filled($message)) aria-invalid="true" @endif
            @if (filled($described)) aria-describedby="{{ $described }}" @endif
            {{ $attributes->except('class') }}
        />

        <span class="{{ $ui->classes('text', 'text-sm text-neutral-700') }}">{{ $label ?? $slot }}</span>
    </label>

    @if (filled($hint))
        <p id="{{ $fieldId }}-hint" class="{{ $ui->classes('hint', 'ps-6 text-xs text-neutral-500') }}">{{ $hint }}</p>
    @endif

    @if (filled($message))
        <p id="{{ $fieldId }}-error" role="alert" class="{{ $ui->classes('error', 'ps-6 text-xs text-red-600') }}">
            {{ $message }}
        </p>
    @endif
</div>
