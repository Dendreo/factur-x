<?php

namespace Dendreo\FacturX\Models\CrossIndustryInvoice\Basic;

use Dendreo\FacturX\Models\CrossIndustryInvoice\DataType\Basic\SupplyChainTradeTransaction;
use Dendreo\FacturX\Models\CrossIndustryInvoice\DataType\BasicWL\ExchangedDocument;
use Dendreo\FacturX\Models\CrossIndustryInvoice\DataType\ExchangedDocumentContext;

class CrossIndustryInvoice extends \Dendreo\FacturX\BasicWL\CrossIndustryInvoice
{
    public function __construct(
        ExchangedDocumentContext $exchangedDocumentContext,
        ExchangedDocument $exchangedDocument,
        SupplyChainTradeTransaction $supplyChainTradeTransaction
    ) {
        parent::__construct($exchangedDocumentContext, $exchangedDocument, $supplyChainTradeTransaction);
    }

    public static function fromXML(\DOMDocument $document): self
    {
        $xpath = new \DOMXPath($document);

        $crossIndustryInvoiceElements = $xpath->query(sprintf('//%s', self::XML_NODE));

        if (1 !== $crossIndustryInvoiceElements->count()) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $crossIndustryInvoiceElement */
        $crossIndustryInvoiceElement = $crossIndustryInvoiceElements->item(0);

        $exchangedDocumentContext    = ExchangedDocumentContext::fromXML($xpath, $crossIndustryInvoiceElement);
        $exchangedDocument           = ExchangedDocument::fromXML($xpath, $crossIndustryInvoiceElement);
        $supplyChainTradeTransaction = SupplyChainTradeTransaction::fromXML($xpath, $crossIndustryInvoiceElement);

        return new self($exchangedDocumentContext, $exchangedDocument, $supplyChainTradeTransaction);
    }
}
