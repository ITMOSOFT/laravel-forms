<?php

namespace ItmoSoft\Forms\Exceptions;

use RuntimeException;

class FormPayloadTooLargeException extends RuntimeException
{
    public static function maximum(int $bytes): self
    {
        return new self("Submission payload exceeds the configured {$bytes}-byte limit.");
    }
}
