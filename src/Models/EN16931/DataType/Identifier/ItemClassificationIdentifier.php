<?php

declare(strict_types=1);

namespace Models\EN16931\DataType\Identifier;

use Models\EN16931\DataType\ItemTypeCode;

class ItemClassificationIdentifier
{
    public function __construct(
        public readonly string $value,
        public readonly ItemTypeCode $scheme,
        public readonly ?string $version = null,
    ) {
    }
}
