<?php

declare(strict_types=1);

namespace Models\EN16931\DataType\Identifier;

use Models\EN16931\DataType\InternationalCodeDesignator;

class LocationIdentifier
{
    public function __construct(
        public readonly string $value,
        public readonly ?InternationalCodeDesignator $scheme = null
    ) {
    }
}