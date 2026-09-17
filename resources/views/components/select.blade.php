@props([
    'name'         => null,
    'id'           => null,
    'label'        => null,
    'hint'         => null,
    'error'        => null,
    'options'      => [],
    'selected'     => null,
    'placeholder'  => null,
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

    $ui = Ui::component('select');

    $size ??= $ui->default('size', 'base');

    $selected ??= Field::old($name);

    $fieldId = Field::id($id, $name);

    $message = Field::error($error, $name);

    $sizes = $ui->sizes(FormControl::SIZES);

    $choices = FormControl::options($options);

    $classes = ClassList::merge(
        $ui->classes('control', implode(' ', [
            FormControl::BASE,
            'cursor-pointer appearance-none pe-10',
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
    <div class="{{ $ui->classes('wrapper', 'relative') }}">
        <select
            id="{{ $fieldId }}"
            @if (filled($name)) name="{{ $name }}" @endif
            @if ($required) required @endif
            class="{{ $classes }}"
            @if (filled($message)) aria-invalid="true" @endif
            @if (filled($described = Field::describedBy([$fieldId . '-hint' => filled($hint), $fieldId . '-error' => filled($message)]))) aria-describedby="{{ $described }}" @endif
            {{ $attributes->except('class') }}
        >
            @if (filled($placeholder))
                <option value="" @selected(blank($selected))>{{ $placeholder }}</option>
            @endif

            @foreach ($choices as $choice)
                <option value="{{ $choice['value'] }}" @selected((string) $choice['value'] === (string) $selected)>
                    {{ $choice['label'] }}
                </option>
            @endforeach

            {{ $slot }}
        </select>

        {{ svg('heroicon-m-chevron-down', $ui->classes('chevron', 'pointer-events-none absolute inset-y-0 end-3 my-auto size-4 text-neutral-400')) }}
    </div>
</x-goognet-ui::field>
