<?php

declare(strict_types=1);

namespace Dendreo\FacturX\Models\EN16931\DataType\Identifier;

class SellerItemIdentifier
{
    public function __construct(public readonly string $value)
    {
    }
}
