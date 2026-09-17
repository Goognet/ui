@props([
    'name'         => null,
    'id'           => null,
    'label'        => null,
    'hint'         => null,
    'error'        => null,
    'rows'         => 4,
    'size'         => null,
    'required'     => false,
    'controlClass' => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\Field;
    use Goognet\Ui\Support\FormControl;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('textarea');

    $size ??= $ui->default('size', 'base');

    $value = Field::old($name);

    $fieldId = Field::id($id, $name);

    $message = Field::error($error, $name);

    /** A textarea grows with its rows, so the control sizes give it padding and type only. */
    $sizes = $ui->sizes(FormControl::TEXT_SIZES);

    $classes = ClassList::merge(
        $ui->classes('control', implode(' ', [
            FormControl::BASE,
            'min-h-(--spacing-control) py-2.5 field-sizing-content',
            $sizes[$size] ?? $sizes['base'],
            filled($message) ? FormControl::INVALID : FormControl::VALID,
        ])),
        (string) $controlClass,
    );
@endphp

<x-goognet-ui::field
    :id="$fieldId"
    :label="$label"
    :hint="$hint"
    :error="$message"
    :required="$required"
    :class="ClassList::merge($ui->classes('base', ''), (string) $attributes->get('class'))"
>
    <textarea
        id="{{ $fieldId }}"
        rows="{{ (int) $rows }}"
        @if (filled($name)) name="{{ $name }}" @endif
        @if ($required) required @endif
        class="{{ $classes }}"
        @if (filled($message)) aria-invalid="true" @endif
        @if (filled($described = Field::describedBy([$fieldId . '-hint' => filled($hint), $fieldId . '-error' => filled($message)]))) aria-describedby="{{ $described }}" @endif
        {{ $attributes->except('class') }}
    >{{ $slot->isNotEmpty() ? $slot : $value }}</textarea>
</x-goognet-ui::field>
