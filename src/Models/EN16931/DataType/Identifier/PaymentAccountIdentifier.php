<?php

declare(strict_types=1);

namespace Models\EN16931\DataType\Identifier;

class PaymentAccountIdentifier
{
    public function __construct(public readonly string $value)
    {
    }
}
