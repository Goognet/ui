@props([
    'message'  => null,
    'title'    => null,
    'type'     => null,
    'position' => null,
    'duration' => null,
    'session'  => null,
])

@php
    use Goognet\Ui\Support\SafeUrl;
    use Goognet\Ui\Support\Toast;
    use Goognet\Ui\Ui;

    $attributes = SafeUrl::attributes($attributes);

    $ui = Ui::component('toast');

    /**
     * Either the message passed here, or whatever the last request flashed. A redirect with
     * `->with('success', '…')` is how a form says it worked, and this is what shows it.
     */
    $config = Toast::resolve(
        message: $message,
        title: $title,
        type: $type,
        position: $position ?? $ui->default('position', 'top-end'),
        duration: $duration ?? $ui->default('duration', 4000),
        session: $session,
    );
@endphp

@if (filled($config))
    {{-- Removed by the script the moment it fires; it is an instruction, not content. --}}
    <div
        data-toast="{{ json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) }}"
        hidden
        {{ $attributes }}
    ></div>
@endif
