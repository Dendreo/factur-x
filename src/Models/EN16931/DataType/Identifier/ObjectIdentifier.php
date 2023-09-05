<?php

declare(strict_types=1);

namespace Models\EN16931\DataType\Identifier;

use Models\EN16931\DataType\ObjectSchemeCode;

class ObjectIdentifier
{
    public function __construct(public readonly string $value, public readonly ?ObjectSchemeCode $scheme = null)
    {
    }
}
