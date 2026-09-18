@props([
    'headers' => [],
    'rows'    => [],
    'caption' => null,
    'striped' => false,
    'size'    => null,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Support\Table;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('table');

    $size ??= $ui->default('size', 'base');

    $columns = Table::columns($headers);

    $sizes = $ui->sizes([
        'sm'   => 'px-3 py-2 text-xs',
        'base' => 'px-4 py-3 text-sm',
        'lg'   => 'px-5 py-4 text-base',
    ]);

    $cellPadding = $sizes[$size] ?? $sizes['base'];
@endphp

{{-- A table is the one block allowed to be wider than the page: it scrolls on its own
     instead of pushing the layout sideways. --}}
<div
    class="{{ ClassList::merge($ui->classes('base', 'w-full overflow-x-auto rounded-surface border border-neutral-200'), (string) $attributes->get('class')) }}"
    {{ $attributes->except('class') }}
>
    <table class="{{ $ui->classes('table', 'w-full border-collapse text-neutral-700') }}">
        @if (filled($caption))
            <caption class="{{ $ui->classes('caption', 'border-b border-neutral-200 bg-neutral-50 ' . $cellPadding . ' text-start font-control text-neutral-900') }}">
                {{ $caption }}
            </caption>
        @endif

        @if ($columns !== [])
            <thead class="{{ $ui->classes('head', 'bg-neutral-50') }}">
                <tr>
                    @foreach ($columns as $column)
                        <th
                            scope="col"
                            class="{{ $ui->classes('header', 'border-b border-neutral-200 font-control whitespace-nowrap text-neutral-900 ' . Table::alignment($column['align']) . ' ' . $cellPadding) }}"
                        >
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="{{ $ui->classes('body', 'divide-y divide-neutral-100') }}">
            @forelse ($rows as $row)
                <tr class="{{ $ui->classes('row', $striped ? 'even:bg-neutral-50' : '') }}">
                    @foreach (Table::cells($row, $columns) as $index => $cell)
                        <td class="{{ $ui->classes('cell', Table::alignment($columns[$index]['align'] ?? 'start') . ' ' . $cellPadding) }}">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @empty
                {{ $slot }}
            @endforelse
        </tbody>
    </table>
</div>
