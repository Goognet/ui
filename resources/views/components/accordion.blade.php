@props([
    'name'  => null,
    'label' => null,
    'faq'   => [],
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('accordion');

    $questions = collect($faq)->mapWithKeys(fn (mixed $answer, mixed $question): array => [(string) $question => (string) $answer]);

    /** Google rejects markup inside an answer, so the FAQ shorthand is plain text and the slot emits no schema. */
    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $questions
            ->map(fn (string $answer, string $question): array => [
                '@type'          => 'Question',
                'name'           => $question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
            ])
            ->values()
            ->all(),
    ];
@endphp

<div
    @if (filled($label)) role="group" aria-label="{{ $label }}" @endif
    class="{{ ClassList::merge($ui->classes('base', 'divide-y divide-neutral-200 border-y border-neutral-200'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    @if ($questions->isNotEmpty())
        @foreach ($questions as $question => $answer)
            <x-goognet-ui::accordion-item :label="$question">{{ $answer }}</x-goognet-ui::accordion-item>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>

@if ($questions->isNotEmpty())
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE) !!}
    </script>
@endif
