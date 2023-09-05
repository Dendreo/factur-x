<?php

declare(strict_types=1);

namespace Dendreo\FacturX\Models\EN16931\DataType\Identifier;

use Dendreo\FacturX\Models\EN16931\DataType\ItemTypeCode;

class ItemClassificationIdentifier
{
    public function __construct(
        public readonly string $value,
        public readonly ItemTypeCode $scheme,
        public readonly ?string $version = null,
    ) {
    }
}
