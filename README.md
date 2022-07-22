# Helpers for making Hotwired Turbo work with Laravel

<p align="center"><a href="https://solutions.eptic.ro" target="_blank"><img src="https://raw.githubusercontent.com/eptic-solutions/art/master/cover.png" width="400px"/></a></p>
<p align="center">
    <a href="https://packagist.org/packages/eptic/turbo">
        <img src="https://img.shields.io/packagist/v/eptic/turbo.svg?style=flat-square" alt="Latest Version on Packagist" />
    </a>
    <a href="https://github.com/EPTIC-Solutions/turbo/actions?query=workflow%3Arun-tests+branch%3Amaster">
        <img src="https://img.shields.io/github/workflow/status/EPTIC-Solutions/turbo/run-tests?label=tests" alt="GitHub Tests Action Status" />
    </a>
    <a href="https://github.com/EPTIC-Solutions/turbo/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain">
        <img src="https://img.shields.io/github/workflow/status/EPTIC-Solutions/turbo/Check%20&%20fix%20styling?label=code%20style" alt="GitHub Code Style Action Status" />
    </a>
    <a href="https://packagist.org/packages/eptic/turbo">
        <img src="https://img.shields.io/packagist/dt/eptic/turbo.svg?style=flat-square" alt="Total Downloads" />
    </a>
</p>

## Installation

You can install the package via composer:

```bash
composer require eptic/turbo
```

As per the official @Hotwired/Turbo documentation, you will need to add the TurboMiddleware provided in this package to the `web` group inside `Kernel.php` to handle the redirects as Turbo expects them.  
You can read more information about this in the official documentation:  
[Redirecting After a Form Submission](https://turbo.hotwired.dev/handbook/drive#redirecting-after-a-form-submission)

Example:
```php
'web' => [
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    // \Illuminate\Session\Middleware\AuthenticateSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \App\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    -> \Eptic\Turbo\Middleware\TurboMiddleware::class,
],
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="turbo-config"
```

Optionally, you can publish the views used as templates using

```bash
php artisan vendor:publish --tag="turbo-views"
```

You can see the content of the config file in the configs folder.

## Usage

### Turbo Frames
To generate a turbo frame response
```php
return response()->turboFrame()->generic(id: 'gallery', partial: view('pages.galleries._partials.create'), target: '#gallery-create');
```

Check if the request was made inside a turbo-frame:
```php
request()->wantsTurboFrame()
```

### Turbo Streams

To check if a request is a turbo stream request:
```php
request()->expectsTurboStream()
```

To generate a turbo stream, you can use the `turboStream` method on the response object.  
It has all the signatures present in the original documentation from Hotwired:
- Append:
    ```php
    return response()->turboStream()->append(target: 'gallery-create', partial: view('pages.galleries._partials.create'));
    ````
- Prepend:
    ```php
    return response()->turboStream()->prepend(target: 'gallery-create', partial: view('pages.galleries._partials.create'));
    ````
- Replace:
    ```php
    return response()->turboStream()->replace(target: 'gallery-create', partial: view('pages.galleries._partials.create'));
    ````
- Update:
    ```php
    return response()->turboStream()->update(target: 'gallery-create', partial: view('pages.galleries._partials.create'));
    ````
- Remove:
    ```php
    return response()->turboStream()->remove(target: 'gallery-create');
    ````
- Before:
    ```php
    return response()->turboStream()->before(target: 'gallery-create', partial: view('pages.galleries._partials.gallery'));
    ````
- After:
    ```php
    return response()->turboStream()->after(target: 'gallery-create', partial: view('pages.galleries._partials.gallery'));
    ````

If you already have a view that contains the entire template and only want to set the correct content-type so it is recognised as a turbo stream, you can use:
```php
return response()->turboStream()->view(view: 'pages.galleries.create', data: $data);
// Or you can pass in a view directly
return response()->turboStream()->view(view: view('pages.galleries.create', $data));
```
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Cristian Bilu](https://github.com/wizzymore)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
