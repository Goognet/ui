<?php

declare(strict_types = 1);

use Goognet\Ui\Console\InstallCommand;

beforeEach(function (): void {
    $this->paths = [
        'config' => config_path('goognet-ui.php'),
        'css'    => resource_path('css/app.css'),
        'js'     => resource_path('js/app.js'),
        'npm'    => base_path('package.json'),
    ];

    $this->originals = collect($this->paths)->map(fn (string $path): ?string => is_file($path) ? file_get_contents($path) : null);

    @mkdir(resource_path('css'), 0755, true);
    @mkdir(resource_path('js'), 0755, true);

    file_put_contents($this->paths['css'], "@import 'tailwindcss';\n@plugin '@tailwindcss/forms';\n");
    file_put_contents($this->paths['js'], "import './bootstrap';\n");
    file_put_contents($this->paths['npm'], json_encode(['devDependencies' => ['swiper' => '^12.0']]));
    @unlink($this->paths['config']);
});

afterEach(function (): void {
    foreach ($this->paths as $key => $path) {
        $original = $this->originals[$key];

        $original === null ? @unlink($path) : file_put_contents($path, $original);
    }
});

it('publishes the config and wires the stylesheet and the script', function (): void {
    $this->artisan('goognet-ui:install')->assertSuccessful();

    expect($this->paths['config'])->toBeFile()
        ->and(file_get_contents($this->paths['css']))->toBe("@import 'tailwindcss';\n" . InstallCommand::CSS_IMPORT . "\n@plugin '@tailwindcss/forms';\n")
        ->and(file_get_contents($this->paths['js']))->toBe(InstallCommand::JS_IMPORT . "\nimport './bootstrap';\n\ninitUi();\n");
});

it('changes nothing on a second run', function (): void {
    $this->artisan('goognet-ui:install')->assertSuccessful();

    $css = file_get_contents($this->paths['css']);
    $js  = file_get_contents($this->paths['js']);

    $this->artisan('goognet-ui:install')->assertSuccessful();

    expect(file_get_contents($this->paths['css']))->toBe($css)
        ->and(file_get_contents($this->paths['js']))->toBe($js)
        ->and(substr_count($css, 'ui.css'))->toBe(1);
});

it('keeps an existing config unless forced', function (): void {
    file_put_contents($this->paths['config'], "<?php return ['prefix' => 'meu-'];");

    $this->artisan('goognet-ui:install')->assertSuccessful();
    expect(file_get_contents($this->paths['config']))->toContain('meu-');

    $this->artisan('goognet-ui:install', ['--force' => true])->assertSuccessful();
    expect(file_get_contents($this->paths['config']))->not->toContain('meu-');
});

it('reports the npm packages it needs without installing them', function (): void {
    $this->artisan('goognet-ui:install')
        ->expectsOutputToContain('missing fslightbox')
        ->expectsOutputToContain('npm install fslightbox')
        ->assertSuccessful();

    expect(json_decode((string) file_get_contents($this->paths['npm']), true)['devDependencies'])->toBe(['swiper' => '^12.0']);
});

it('leaves a stylesheet with no tailwind import untouched and prints the line to add', function (): void {
    file_put_contents($this->paths['css'], "body { color: red; }\n");

    $this->artisan('goognet-ui:install')
        ->expectsOutputToContain("no @import 'tailwindcss' to anchor on")
        ->assertSuccessful();

    expect(file_get_contents($this->paths['css']))->toBe("body { color: red; }\n");
});

it('takes other entry points by option', function (): void {
    file_put_contents(resource_path('css/site.css'), "@import \"tailwindcss\";\n");

    $this->artisan('goognet-ui:install', ['--css' => 'resources/css/site.css'])->assertSuccessful();

    expect(file_get_contents(resource_path('css/site.css')))->toContain(InstallCommand::CSS_IMPORT);

    unlink(resource_path('css/site.css'));
});
