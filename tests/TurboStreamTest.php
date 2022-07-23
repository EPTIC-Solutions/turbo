<?php

use Eptic\Turbo\Exceptions\TurboStreamResponseFailedException;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;

beforeEach(function () {
    $viewFactory = app(Factory::class);
    $viewFactory->addLocation('tests/views');
});

it('throws missing partial exception', function () {
    $this->assertThrows(
        function () {
            response()->turboStream()->toResponse(request());
        },
        TurboStreamResponseFailedException::class,
        'Missing partial: non "remove" Turbo Streams need a partial.'
    );
});

it('generates correct turbo stream - append', function () {
    $expectedOutput = File::get('tests/responses/turbo-stream/append.html');

    expect(response()->turboStream()->append('test', view('dummy-view'))->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});

it('generates correct turbo stream - remove', function () {
    $expectedOutput = File::get('tests/responses/turbo-stream/remove.html');

    $response = response()->turboStream()->remove('test')->toResponse(request())->getContent();

    expect($response)->toBe($expectedOutput);
});

it('generates correct turbo stream - replace', function () {
    $expectedOutput = File::get('tests/responses/turbo-stream/replace.html');

    expect(response()->turboStream()->replace('test', view('dummy-view'))->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});

it('generates correct turbo stream - prepend', function () {
    $expectedOutput = File::get('tests/responses/turbo-stream/prepend.html');

    expect(response()->turboStream()->prepend('test', view('dummy-view'))->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});

it('generates correct turbo stream - before', function () {
    $expectedOutput = File::get('tests/responses/turbo-stream/before.html');

    expect(response()->turboStream()->before('test', view('dummy-view'))->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});

it('generates correct turbo stream - after', function () {
    $expectedOutput = File::get('tests/responses/turbo-stream/after.html');

    expect(response()->turboStream()->after('test', view('dummy-view'))->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});

it('generates correct turbo stream - update', function () {
    $expectedOutput = File::get('tests/responses/turbo-stream/update.html');

    expect(response()->turboStream()->update('test', view('dummy-view'))->toResponse(request())->getContent())
        ->toBe($expectedOutput);
});

it('checks for turbo stream header', function () {
    $request = new \Illuminate\Http\Request(server: [
       'HTTP_ACCEPT' => \Eptic\Turbo\Turbo::TURBO_STREAM_FORMAT,
   ]);

    expect($request->expectsTurboStream())->toBeTrue();
});
