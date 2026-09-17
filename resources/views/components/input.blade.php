@props([
    'name'         => null,
    'id'           => null,
    'type'         => 'text',
    'label'        => null,
    'hint'         => null,
    'error'        => null,
    'icon'         => null,
    'value'        => null,
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

    $ui = Ui::component('input');

    $size ??= $ui->default('size', 'base');

    $inputType = FormControl::type($type);

    /** A repopulated password field would put the typed secret back into the HTML. */
    $value ??= $inputType === 'password' ? null : Field::old($name);

    $fieldId = Field::id($id, $name);

    $message = Field::error($error, $name);

    $sizes = $ui->sizes(FormControl::SIZES);

    $sizeClass = $sizes[$size] ?? $sizes['base'];

    $iconSizes = ['sm' => 'size-4', 'base' => 'size-4', 'lg' => 'size-5'];

    $iconClass = $ui->classes('icon', ($iconSizes[$size] ?? $iconSizes['base']) . ' pointer-events-none absolute inset-y-0 start-3 my-auto text-neutral-400');

    $classes = ClassList::merge(
        $ui->classes('control', implode(' ', array_filter([
            FormControl::BASE,
            $sizeClass,
            filled($icon) ? 'ps-10' : null,
            filled($message) ? FormControl::INVALID : FormControl::VALID,
        ]))),
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
    <div class="{{ $ui->classes('wrapper', 'relative') }}">
        @if (filled($icon))
            {{ is_string($icon) ? svg($icon, $iconClass) : $icon }}
        @endif

        <input
            id="{{ $fieldId }}"
            type="{{ $inputType }}"
            @if (filled($value)) value="{{ $value }}" @endif
            @if (filled($name)) name="{{ $name }}" @endif
            @if ($required) required @endif
            class="{{ $classes }}"
            @if (filled($message)) aria-invalid="true" @endif
            @if (filled($described = Field::describedBy([$fieldId . '-hint' => filled($hint), $fieldId . '-error' => filled($message)]))) aria-describedby="{{ $described }}" @endif
            {{ $attributes->except('class') }}
        />
    </div>
</x-goognet-ui::field>
