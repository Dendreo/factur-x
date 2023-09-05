<?php

declare(strict_types=1);

namespace Dendreo\FacturX\Models\EN16931\Converter;

use Dendreo\FacturX\Models\EN16931\DataType\DateCode2005;
use Dendreo\FacturX\Models\EN16931\DataType\DateCode2475;

class DateCode2005ToDateCode2475Converter
{
    public static function convert(DateCode2005 $dateCode2005): DateCode2475
    {
        return match ($dateCode2005) {
            DateCode2005::INVOICE_DATE => DateCode2475::INVOICE_DATE,
            DateCode2005::DELIVERY_DATE => DateCode2475::DELIVERY_DATE,
            DateCode2005::PAYMENT_DATE => DateCode2475::PAYMENT_DATE
        };
    }
}
