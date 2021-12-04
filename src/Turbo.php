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

    public static function makeStream(string $content, $status = 200): Response
    {
        return response($content, $status, ['Content-Type' => static::TURBO_STREAM_FORMAT]);
    }

    public static function makeFrame(string $content, $status = 200): Response
    {
        return response($content, $status);
    }
}
