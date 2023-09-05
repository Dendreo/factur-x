<?php

declare(strict_types=1);

namespace Models\EN16931\DataType\Reference;

abstract class DocumentReference
{
    public function __construct(public readonly string $value)
    {
    }
}
