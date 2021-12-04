<?php

namespace Eptic\Turbo;

use Eptic\Turbo\Responses\TurboFrameResponse;
use Eptic\Turbo\Responses\TurboStreamResponse;
use Illuminate\Contracts\View\View;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Http\Request;

class TurboServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('turbo')
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageRegistered()
    {
        $this->bindRequestAndResponseMacros();
    }

    private function bindRequestAndResponseMacros()
    {
        Request::macro('expectsTurboStream', function () {
            return Turbo::turboVisit($this);
        });

        Response::macro('turboStream', function (string $target = null, string $action = null, View $partial = null) {
            if ($target === null) {
                return new TurboStreamResponse();
            }

            return TurboStreamResponse::create($target, $action, $partial);
        });

        ResponseFactory::macro('turboStream',
            function (string $target = null, string $action = null, View $partial = null) {
                if ($target === null) {
                    return new TurboStreamResponse();
                }

                return TurboStreamResponse::create($target, $action, $partial);
            });

        Response::macro('turboFrame', function () {
            return new TurboFrameResponse();
        });

        ResponseFactory::macro('turboFrame', function () {
            return new TurboFrameResponse();
        });

        Response::macro('turboStreamView', function ($view, $data = []) {
            if (! $view instanceof View) {
                $view = view($view, $data);
            }

            return Turbo::makeStream($view->render());
        });

        ResponseFactory::macro('turboStreamView', function ($view, $data = []) {
            if (! $view instanceof View) {
                $view = view($view, $data);
            }

            return Turbo::makeStream($view->render());
        });
    }
}
