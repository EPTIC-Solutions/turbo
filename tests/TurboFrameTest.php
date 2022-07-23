<?php

use Eptic\Turbo\Exceptions\TurboStreamResponseFailedException;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;

beforeEach(function () {
    $viewFactory = app(Factory::class);
    $viewFactory->addLocation('tests/views');
});

it('throws missing partial exception', function () {
    response()->turboFrame()->toResponse(request());
})->throws(TurboStreamResponseFailedException::class, 'Missing partial: non "remove" Turbo Streams need a partial.');

it('generates correct generic turbo frame', function () {
    $expectedOutput = File::get('tests/responses/turbo-frame/turbo-frame-generic.html');

    expect(response()->turboFrame()->generic('test', view('dummy-view'))->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});

it('generates correct generic turbo frame with target', function () {
    $expectedOutput = File::get('tests/responses/turbo-frame/turbo-frame-generic-target.html');

    expect(response()->turboFrame()->generic('test', view('dummy-view'), 'test2')->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});
