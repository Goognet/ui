@php
    use Goognet\Ui\Support\ClassList;
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Ui;

    $ui = Ui::component('container');

    $attributes = SafeUrl::attributes($attributes);

    $classes = ClassList::merge(
        $ui->classes('base', 'mx-auto w-full max-w-page px-5 md:px-8 lg:px-12'),
        (string) $attributes->get('class'),
    );
@endphp

<div class="{{ $classes }}" {{ $attributes->except('class') }}>{{ $slot }}</div>
