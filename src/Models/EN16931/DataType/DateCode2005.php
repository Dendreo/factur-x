<?php

declare(strict_types=1);

namespace Dendreo\FacturX\Models\EN16931\DataType;

enum DateCode2005: string
{
    case INVOICE_DATE = "3";
    case DELIVERY_DATE = "35";
    case PAYMENT_DATE = "432";
}
