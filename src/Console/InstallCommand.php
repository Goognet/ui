<?php

declare(strict_types = 1);

namespace Goognet\Ui\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'goognet-ui:install')]
final class InstallCommand extends Command
{
    public const string CSS_IMPORT = "@import '../../vendor/goognet/ui/resources/css/ui.css';";

    public const string JS_IMPORT = "import { initUi } from '../../vendor/goognet/ui/resources/js';";

    /** @var list<string> */
    private const array NPM_PACKAGES = ['swiper', 'fslightbox', 'countup.js', 'imask'];

    protected $signature = 'goognet-ui:install
        {--force : Overwrite config/goognet-ui.php if it already exists}
        {--css=resources/css/app.css : The stylesheet that imports Tailwind}
        {--js=resources/js/app.js : The JavaScript entry point}';

    protected $description = 'Publish the goognet/ui config and wire its CSS and JavaScript into the app';

    public function handle(Filesystem $files): int
    {
        $this->publishConfig();

        $this->wireStylesheet($files, base_path((string) $this->option('css')));

        $this->wireScript($files, base_path((string) $this->option('js')));

        $this->checkNpmPackages($files);

        $this->newLine();
        $this->components->info('goognet/ui installed. Run `npm run build` to see the components.');

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        if (file_exists(config_path('goognet-ui.php')) && ! $this->option('force')) {
            $this->components->twoColumnDetail('config/goognet-ui.php', '<fg=yellow>kept, already exists (use --force)</>');

            return;
        }

        $this->callSilently('vendor:publish', ['--tag' => 'goognet-ui-config', '--force' => true]);

        $this->components->twoColumnDetail('config/goognet-ui.php', '<fg=green>published</>');
    }

    private function wireStylesheet(Filesystem $files, string $path): void
    {
        $label = $this->relative($path);

        if (! $files->exists($path)) {
            $this->manual($label, 'not found', self::CSS_IMPORT);

            return;
        }

        $css = $files->get($path);

        if (str_contains($css, 'goognet/ui/resources/css/ui.css')) {
            $this->components->twoColumnDetail($label, '<fg=yellow>already imports the package</>');

            return;
        }

        /** After Tailwind, never before: the package's @theme and utilities need Tailwind loaded. */
        $wired = preg_replace('/^(@import\s+[\'"]tailwindcss[\'"];[^\n]*\n)/m', "$1" . self::CSS_IMPORT . "\n", $css, 1, $count);

        if ($count === 0 || ! is_string($wired)) {
            $this->manual($label, "no @import 'tailwindcss' to anchor on", self::CSS_IMPORT);

            return;
        }

        $files->put($path, $wired);

        $this->components->twoColumnDetail($label, '<fg=green>import added</>');
    }

    private function wireScript(Filesystem $files, string $path): void
    {
        $label = $this->relative($path);

        if (! $files->exists($path)) {
            $this->manual($label, 'not found', self::JS_IMPORT . "\n\ninitUi();");

            return;
        }

        $js = $files->get($path);

        if (str_contains($js, 'goognet/ui/resources/js')) {
            $this->components->twoColumnDetail($label, '<fg=yellow>already imports the package</>');

            return;
        }

        $files->put($path, self::JS_IMPORT . "\n" . rtrim($js) . "\n\ninitUi();\n");

        $this->components->twoColumnDetail($label, '<fg=green>initUi() added</>');
    }

    /** Reported, not installed: running a package manager in someone's project is theirs to decide. */
    private function checkNpmPackages(Filesystem $files): void
    {
        $manifest = base_path('package.json');

        /** @var array<string, mixed> $package */
        $package = $files->exists($manifest) ? (array) json_decode($files->get($manifest), true) : [];

        $declared = array_keys([...(array) ($package['dependencies'] ?? []), ...(array) ($package['devDependencies'] ?? [])]);

        $missing = array_values(array_diff(self::NPM_PACKAGES, $declared));

        if ($missing === []) {
            $this->components->twoColumnDetail('package.json', '<fg=green>' . implode(', ', self::NPM_PACKAGES) . ' present</>');

            return;
        }

        $this->components->twoColumnDetail('package.json', '<fg=red>missing ' . implode(', ', $missing) . '</>');
        $this->line('    <fg=gray>npm install ' . implode(' ', $missing) . '</>');
    }

    private function manual(string $label, string $reason, string $snippet): void
    {
        $this->components->twoColumnDetail($label, '<fg=red>' . $reason . ', add it by hand</>');
        $this->line('    <fg=gray>' . str_replace("\n", "\n    ", $snippet) . '</>');
    }

    private function relative(string $path): string
    {
        return ltrim(str_replace(base_path(), '', $path), '/');
    }
}
