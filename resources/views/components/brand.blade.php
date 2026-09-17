@props([
    'logo'     => null,
    'name'     => null,
    'alt'      => null,
    'href'     => null,
    'external' => false,
])

@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;
    use Illuminate\Support\Facades\Vite;
    use Illuminate\View\ComponentAttributeBag;
    use Illuminate\View\ComponentSlot;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('brand');

    $logo ??= config('goognet-ui.company.logo') ?: null;

    $isMarkup = $logo instanceof ComponentSlot;

    $safeLogo = $isMarkup ? null : SafeUrl::media($logo);

    $isRemote = filled($safeLogo) && preg_match('#^([a-z][a-z0-9+.\-]*:|//)#i', $safeLogo) === 1;

    $isLocal = filled($safeLogo) && ! $isRemote && ! str_contains($safeLogo, '..');

    $path = $isLocal ? (str_contains($safeLogo, '/') ? ltrim($safeLogo, '/') : 'resources/images/' . $safeLogo) : null;

    $source = $isRemote ? $safeLogo : ($isLocal ? Vite::asset((string) $path) : null);

    $hasImage = $isMarkup || filled($source);

    /** With no mark at all the name is the mark, so it stops being optional. */
    $name ??= $hasImage ? null : config('goognet-ui.company.name');

    $hasName = filled($name);

    /** Beside a spelled-out name the mark is decoration; an alt would make a screen reader say it twice. */
    $alt ??= $hasName ? '' : config('goognet-ui.company.name');

    $safeHref = SafeUrl::href($href);

    /** `target` and `rel` are invalid on an `<a>` with no `href`, which is what a refused URL leaves. */
    $opensInNewTab = filled($safeHref) && ($external || $attributes->get('target') === '_blank');

    if (blank($safeHref)) {
        $attributes = $attributes->except('target');
    }

    $needsWrapper = $hasName || $isMarkup;

    $wrapperClasses = $ui->classes('base', $hasName ? 'inline-flex items-center gap-2.5' : 'inline-flex');

    /** Attributes follow what is drawn: onto the `<img>` when there is one, onto the wrapper when not. */
    $rendersImage = ! $isMarkup && filled($source);

    $wrapperAttributes = $rendersImage ? new ComponentAttributeBag() : $attributes;

    $wrapperClass = ClassList::merge($wrapperClasses, (string) $wrapperAttributes->get('class'));

    $anchorAttributes = $wrapperAttributes->except('class')->merge([
        'href'   => $safeHref,
        'target' => $opensInNewTab ? '_blank' : null,
        'rel'    => $opensInNewTab ? 'noopener noreferrer' : null,
        'class'  => $wrapperClass,
    ]);

    /** The mark is the root when nothing wraps it, so that is where a laid-over base class belongs. */
    $imageAttributes = $needsWrapper
        ? $attributes
        : $attributes->except('class')->merge(['class' => ClassList::merge($ui->classes('base', ''), (string) $attributes->get('class'))]);

    $webpPath = $isLocal && preg_match('/\.(jpe?g|png)$/i', (string) $path) === 1
        ? preg_replace('/\.(jpe?g|png)$/i', '.webp', (string) $path)
        : null;
@endphp

@if (filled($href))
    <a {{ $anchorAttributes }}>
@elseif ($needsWrapper)
    <span class="{{ $wrapperClass }}" {{ $wrapperAttributes->except('class') }}>
@endif

@if ($isMarkup)
    <span {{ $logo->attributes->class([$ui->classes('mark', 'inline-flex shrink-0 items-center justify-center')]) }}>{{ $logo }}</span>
@elseif (filled($webpPath))
    <picture>
        <source srcset="{{ Vite::asset($webpPath) }}" type="image/webp" />
        <img src="{{ $source }}" alt="{{ $alt }}" {{ $imageAttributes }} />
    </picture>
@elseif (filled($source))
    <img src="{{ $source }}" alt="{{ $alt }}" {{ $imageAttributes }} />
@endif

@if ($hasName)
    <span class="{{ $ui->classes('name', 'font-semibold tracking-tight whitespace-nowrap') }}">{{ $name }}</span>
@endif

@if (filled($href))
    </a>
@elseif ($needsWrapper)
    </span>
@endif
