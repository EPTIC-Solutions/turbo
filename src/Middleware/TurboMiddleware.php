<?php

namespace Eptic\Turbo\Middleware;

use Closure;
use Eptic\Turbo\Helpers\RouteRedirectGuesser;
use Eptic\Turbo\Turbo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class TurboMiddleware
{
    /** @var RouteRedirectGuesser */
    private RouteRedirectGuesser $redirectGuesser;

    public function __construct(RouteRedirectGuesser $redirectGuesser)
    {
        $this->redirectGuesser = $redirectGuesser;
    }

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        return $this->turboResponse($next($request), $request);
    }

    /**
     * @param  mixed  $response
     * @param  Request  $request
     * @return mixed
     */
    private function turboResponse(mixed $response, Request $request): mixed
    {
        if (! Turbo::turboVisit($request)) {
            return $response;
        }

        if (! $response instanceof RedirectResponse) {
            return $response;
        }

        // Turbo expects a 303 redirect. We are also changing the default behavior of Laravel's failed
        // validation redirection to send the user to a page where the form of the current resource
        // is rendered (instead of just "back"), since Frames could have been used in many pages.

        $response->setStatusCode(303);

        if ($response->exception instanceof ValidationException && ! $response->exception->redirectTo) {
            $response->setTargetUrl(
                $this->guessRedirectingRoute($request) ?: $response->getTargetUrl()
            );
        }

        return $response;
    }

    /**
     * @param  Request  $request
     * @return string|null
     */
    private function guessRedirectingRoute(Request $request): ?string
    {
        $route = $request->route();
        $name = optional($route)->getName();

        if (! $route || ! $name) {
            return null;
        }

        $formRouteName = $this->redirectGuesser->guess($name);

        // If the guessed route doesn't exist, send it back to wherever Laravel defaults to.

        if (! Route::has($formRouteName)) {
            return null;
        }

        return route($formRouteName, optional($route)->parameters() + request()->query());
    }
}
