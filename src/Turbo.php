<?php

namespace Eptic\Turbo;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class Turbo
{
    public const TURBO_STREAM_FORMAT = 'text/vnd.turbo-stream.html';

    /**
     * @param  Request  $request
     * @return bool
     */
    public static function turboVisit(Request $request): bool
    {
        return Str::contains($request->header('Accept', ''), static::TURBO_STREAM_FORMAT);
    }

    /**
     * Create a new response instance for the given content with the correct Turbo Stream content type.
     *
     * @param  string  $content
     * @param  int  $status
     * @return Response
     */
    public static function makeStream(string $content, int $status = 200): Response
    {
        return response($content, $status, ['Content-Type' => static::TURBO_STREAM_FORMAT]);
    }
}
