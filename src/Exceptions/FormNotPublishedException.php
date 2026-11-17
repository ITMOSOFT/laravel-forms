<?php

namespace ItmoSoft\Forms\Exceptions;

use RuntimeException;

class FormNotPublishedException extends RuntimeException
{
    public static function forForm(int|string $id): self
    {
        return new self("Form [{$id}] is not published and cannot accept submissions.");
    }
}
