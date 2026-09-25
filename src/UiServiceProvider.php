<?php

declare(strict_types = 1);

namespace Goognet\Ui;

use Goognet\Ui\Console\InstallCommand;
use Goognet\Ui\Customization\Customizations;
use Goognet\Ui\Support\Catalogue;
use Goognet\Ui\Support\ConsentCookie;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

final class UiServiceProvider extends ServiceProvider
{
    public const string NAMESPACE = 'goognet-ui';

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/goognet-ui.php', self::NAMESPACE);

        $this->app->singleton(Customizations::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', self::NAMESPACE);

        /**
         * Every string the components write on their own. The package ships pt_BR, en and es,
         * and the site adds a locale by dropping a file in `lang/vendor/goognet-ui`. What the
         * visitor reads follows `app.locale`, so a multilingual site only has to set it.
         */
        $this->loadTranslationsFrom(__DIR__ . '/../lang', self::NAMESPACE);

        /** The consent cookie is written by the browser in plain text; decrypting it would read as absent. */
        EncryptCookies::except(ConsentCookie::name());

        $this->registerCatalogueRoute();

        $this->callAfterResolving(BladeCompiler::class, function (): void {
            Blade::anonymousComponentNamespace(self::NAMESPACE . '::components', self::NAMESPACE);

            $this->registerPrefixedAliases();
        });

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);

            $this->publishes([
                __DIR__ . '/../config/goognet-ui.php' => config_path('goognet-ui.php'),
            ], 'goognet-ui-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/' . self::NAMESPACE),
            ], 'goognet-ui-views');

            $this->publishes([
                __DIR__ . '/../lang' => lang_path('vendor/' . self::NAMESPACE),
            ], 'goognet-ui-lang');
        }
    }

    /**
     * Registered in every environment and refused at request time, so switching the flag in a
     * running app needs no route cache rebuild — and a cached route list from a development
     * machine still answers 404 in production.
     */
    private function registerCatalogueRoute(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware('web')
            ->get((string) config(self::NAMESPACE . '.catalogue.path', 'dev/components'), function (): Factory | View {
                abort_unless(Catalogue::enabled(), 404);

                /** @phpstan-ignore argument.type (a package view namespace is registered at runtime; Larastan only sees the app's) */
                return view('goognet-ui::catalogue');
            })
            ->name('goognet-ui.catalogue')
            ->defaults('sitemap', false);
    }

    /**
     * The public tag is an alias onto the view, not a second registration: a published override
     * in `resources/views/vendor/goognet-ui` is picked up under either name.
     */
    private function registerPrefixedAliases(): void
    {
        $prefix = (string) config(self::NAMESPACE . '.prefix');

        if ($prefix === '') {
            return;
        }

        foreach ($this->componentNames() as $name) {
            Blade::component(self::NAMESPACE . '::components.' . $name, $prefix . $name);
        }
    }

    /**
     * @return list<string>
     */
    private function componentNames(): array
    {
        return array_map(
            fn (string $file): string => basename($file, '.blade.php'),
            glob(__DIR__ . '/../resources/views/components/*.blade.php') ?: [],
        );
    }
}
