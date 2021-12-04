<?php

namespace Eptic\Turbo\Exceptions;

use RuntimeException;

class TurboStreamResponseFailedException extends RuntimeException
{
    public static function missingPartial(): self
    {
        return new self('Missing partial: non "remove" Turbo Streams need a partial.');
    }
}
