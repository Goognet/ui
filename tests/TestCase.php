<?php

declare(strict_types = 1);

namespace Goognet\Ui\Tests;

use AndreiIonita\BladeRemixIcon\BladeRemixIconServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Goognet\Ui\UiServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use InteractsWithViews;

    private static bool $viewsCleared = false;

    /**
     * Compiled views are reused when the source mtime is not newer, and an edit landing in the
     * same second as the last run reads the stale compiled copy. Once per process is enough.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$viewsCleared) {
            $this->artisan('view:clear');

            self::$viewsCleared = true;
        }
    }

    /**
     * The pages a site built on the package has: a home, a privacy policy and the catalogue. They
     * run through the `web` group, so cookie encryption applies exactly as it would in an app.
     */
    /** The `web` group encrypts cookies and starts a session, and both need a key. */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(str_repeat('k', 32)));
    }

    protected function defineRoutes($router): void
    {
        $page = fn (string $body): string => Blade::render('<!DOCTYPE html><html lang="pt-BR"><head><title>Teste</title></head><body>' . $body . '</body></html>');

        Route::middleware('web')->group(function () use ($page): void {
            Route::get('/', fn (): string => $page('<x-ui.footer /><x-ui.cookie-consent />'))->name('home');
            Route::get('/politica-de-privacidade', fn (): string => $page('<x-ui.footer :callout="false" />'))->name('privacy');
        });
    }

    /**
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeRemixIconServiceProvider::class,
            UiServiceProvider::class,
        ];
    }
}
