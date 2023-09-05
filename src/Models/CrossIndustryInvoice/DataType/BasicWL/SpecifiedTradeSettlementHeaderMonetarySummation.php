<?php

declare(strict_types=1);

namespace Dendreo\FacturX\DataType\BasicWL;

use Dendreo\FacturX\DataType\TaxTotalAmount;
use Models\EN16931\DataType\CurrencyCode;
use Models\EN16931\SemanticDataType\Amount;

/**
 * BG-22.
 */
class SpecifiedTradeSettlementHeaderMonetarySummation extends \Dendreo\FacturX\DataType\Minimum\SpecifiedTradeSettlementHeaderMonetarySummation
{
    /**
     * BT-106.
     */
    protected Amount $lineTotalAmount;

    /**
     * BT-108.
     */
    protected ?Amount $chargeTotalAmount;

    /**
     * BT-107.
     */
    protected ?Amount $allowanceTotalAmount;

    /**
     * BT-111 & BT-111-0.
     */
    protected ?TaxTotalAmount $taxTotalAmountCurrency;

    /**
     * BT-113.
     */
    protected ?Amount $totalPrepaidAmount;

    public function __construct(
        float $taxBasisTotalAmount,
        float $grandTotalAmount,
        float $duePayableAmount,
        float $lineTotalAmount,
        ?TaxTotalAmount $taxTotalAmountCurrency = null
    ) {
        parent::__construct($taxBasisTotalAmount, $grandTotalAmount, $duePayableAmount);

        $this->lineTotalAmount        = new Amount($lineTotalAmount);
        $this->chargeTotalAmount      = null;
        $this->allowanceTotalAmount   = null;
        $this->taxTotalAmountCurrency = $taxTotalAmountCurrency;
        $this->totalPrepaidAmount     = null;
    }

    public function getLineTotalAmount(): float
    {
        return $this->lineTotalAmount->getValueRounded();
    }

    public function getChargeTotalAmount(): ?float
    {
        return $this->chargeTotalAmount instanceof Amount ? $this->chargeTotalAmount->getValueRounded() : null;
    }

    public function setChargeTotalAmount(?float $chargeTotalAmount): static
    {
        $this->chargeTotalAmount = \is_float($chargeTotalAmount) ? new Amount($chargeTotalAmount) : null;

        return $this;
    }

    public function getAllowanceTotalAmount(): ?float
    {
        return $this->allowanceTotalAmount instanceof Amount ? $this->allowanceTotalAmount->getValueRounded() : null;
    }

    public function setAllowanceTotalAmount(?float $allowanceTotalAmount): static
    {
        $this->allowanceTotalAmount = \is_float($allowanceTotalAmount) ? new Amount($allowanceTotalAmount) : null;

        return $this;
    }

    public function getTaxTotalAmountCurrency(): ?TaxTotalAmount
    {
        return $this->taxTotalAmountCurrency;
    }

    public function setTaxTotalAmountCurrency(?TaxTotalAmount $taxTotalAmountCurrency): static
    {
        $this->taxTotalAmountCurrency = $taxTotalAmountCurrency;

        return $this;
    }

    public function getTotalPrepaidAmount(): ?float
    {
        return $this->totalPrepaidAmount instanceof Amount ? $this->totalPrepaidAmount->getValueRounded() : null;
    }

    public function setTotalPrepaidAmount(?float $totalPrepaidAmount): static
    {
        $this->totalPrepaidAmount = \is_float($totalPrepaidAmount) ? new Amount($totalPrepaidAmount) : null;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('ram:LineTotalAmount', $this->lineTotalAmount->getFormattedValueRounded()));

        if ($this->chargeTotalAmount instanceof Amount) {
            $currentNode->appendChild($document->createElement('ram:ChargeTotalAmount', $this->chargeTotalAmount->getFormattedValueRounded()));
        }

        if ($this->allowanceTotalAmount instanceof Amount) {
            $currentNode->appendChild($document->createElement('ram:AllowanceTotalAmount', $this->allowanceTotalAmount->getFormattedValueRounded()));
        }

        $currentNode->appendChild($document->createElement('ram:TaxBasisTotalAmount', $this->taxBasisTotalAmount->getFormattedValueRounded()));

        if ($this->taxTotalAmount instanceof TaxTotalAmount) {
            $currentNode->appendChild($this->taxTotalAmount->toXML($document));
        }

        if ($this->taxTotalAmountCurrency instanceof TaxTotalAmount) {
            $currentNode->appendChild($this->taxTotalAmountCurrency->toXML($document));
        }

        $currentNode->appendChild($document->createElement('ram:GrandTotalAmount', $this->grandTotalAmount->getFormattedValueRounded()));

        if ($this->totalPrepaidAmount instanceof Amount) {
            $currentNode->appendChild($document->createElement('ram:TotalPrepaidAmount', $this->totalPrepaidAmount->getFormattedValueRounded()));
        }

        $currentNode->appendChild($document->createElement('ram:DuePayableAmount', $this->duePayableAmount->getFormattedValueRounded()));

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): self
    {
        $specifiedTradeSettlementHeaderMonetarySummationElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if ($specifiedTradeSettlementHeaderMonetarySummationElements->count() !== 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $specifiedTradeSettlementHeaderMonetarySummationElement */
        $specifiedTradeSettlementHeaderMonetarySummationElement = $specifiedTradeSettlementHeaderMonetarySummationElements->item(0);

        $lineTotalAmountElements      = $xpath->query('./ram:LineTotalAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);
        $chargeTotalAmountElements    = $xpath->query('./ram:ChargeTotalAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);
        $allowanceTotalAmountElements = $xpath->query('./ram:AllowanceTotalAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);
        $taxBasisTotalAmountElements  = $xpath->query('./ram:TaxBasisTotalAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);
        $grandTotalAmountElements     = $xpath->query('./ram:GrandTotalAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);
        $totalPrepaidAmountElements   = $xpath->query('./ram:TotalPrepaidAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);
        $duePayableAmountElements     = $xpath->query('./ram:DuePayableAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);

        if ($lineTotalAmountElements->count() !== 1) {
            throw new \Exception('Malformed');
        }

        if ($chargeTotalAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($allowanceTotalAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($taxBasisTotalAmountElements->count() !== 1) {
            throw new \Exception('Malformed');
        }

        if ($grandTotalAmountElements->count() !== 1) {
            throw new \Exception('Malformed');
        }

        if ($totalPrepaidAmountElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($duePayableAmountElements->count() !== 1) {
            throw new \Exception('Malformed');
        }

        $lineTotalAmount     = $lineTotalAmountElements->item(0)->nodeValue;
        $taxBasisTotalAmount = $taxBasisTotalAmountElements->item(0)->nodeValue;
        $grandTotalAmount    = $grandTotalAmountElements->item(0)->nodeValue;
        $duePayableAmount    = $duePayableAmountElements->item(0)->nodeValue;

        $specifiedTradeSettlementHeaderMonetarySummation = new self((float) $taxBasisTotalAmount, (float) $grandTotalAmount, (float) $duePayableAmount, (float) $lineTotalAmount);

        if ($chargeTotalAmountElements->count() === 1) {
            $specifiedTradeSettlementHeaderMonetarySummation->setChargeTotalAmount((float) $chargeTotalAmountElements->item(0)->nodeValue);
        }

        if ($allowanceTotalAmountElements->count() === 1) {
            $specifiedTradeSettlementHeaderMonetarySummation->setAllowanceTotalAmount((float) $allowanceTotalAmountElements->item(0)->nodeValue);
        }

        if ($totalPrepaidAmountElements->count() > 1) {
            $specifiedTradeSettlementHeaderMonetarySummation->setTotalPrepaidAmount((float) $totalPrepaidAmountElements->item(0)->nodeValue);
        }

        /** Checks BT-5/BT-6 for BT-110/BT-111 */
        $invoiceCurrencyCodeElements = $xpath->query('./ram:InvoiceCurrencyCode', $currentElement);
        $taxCurrencyCodeElements     = $xpath->query('./ram:TaxCurrencyCode');

        if ($invoiceCurrencyCodeElements->count() !== 1) {
            throw new \Exception('Malformed');
        }

        if ($taxCurrencyCodeElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $invoiceCurrencyCode = CurrencyCode::tryFrom($invoiceCurrencyCodeElements->item(0)->nodeValue);

        if ($invoiceCurrencyCode === null) {
            throw new \Exception('Wrong InvoiceCurrencyCode');
        }

        $taxCurrencyCode = null;

        if ($taxCurrencyCodeElements->count() === 1) {
            $taxCurrencyCode = CurrencyCode::tryFrom($taxCurrencyCodeElements->item(0)->nodeValue);

            if ($taxCurrencyCode === null) {
                throw new \Exception('Wrong TaxCurrencyCode');
            }
        }

        $taxTotalAmountElements = $xpath->query('./ram:TaxTotalAmount', $specifiedTradeSettlementHeaderMonetarySummationElement);

        if ($taxCurrencyCode === null || $invoiceCurrencyCode === $taxCurrencyCode) {
            // Same currency code for BT-5 & BT-6, only fill BT-110, no need to fill BT-111
            // Because we have one currency, one line maximum
            if ($taxTotalAmountElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if ($taxTotalAmountElements->count() === 1) {
                /** @var \DOMElement $taxTotalAmountItem */
                $taxTotalAmountItem = $taxTotalAmountElements->item(0);

                $taxTotalAmount = TaxTotalAmount::fromXML($taxTotalAmountItem);

                $specifiedTradeSettlementHeaderMonetarySummation->setTaxTotalAmount($taxTotalAmount);
            }
        } else {
            // Not same currency code for BT-5 & BT-6, have to fill BT-110 & BT-111
            // Because we have two currencies, two lines maximum
            if ($taxTotalAmountElements->count() > 2) {
                throw new \Exception('Malformed');
            }

            /** @var \DOMElement $taxTotalAmountElement */
            foreach ($taxTotalAmountElements as $taxTotalAmountElement) {
                $taxTotalAmount = TaxTotalAmount::fromXML($taxTotalAmountElement);

                if ($taxTotalAmount->getCurrencyIdentifier() === $taxCurrencyCode) {
                    $specifiedTradeSettlementHeaderMonetarySummation->setTaxTotalAmountCurrency($taxTotalAmount);
                }

                if ($taxTotalAmount->getCurrencyIdentifier() === $invoiceCurrencyCode) {
                    $specifiedTradeSettlementHeaderMonetarySummation->setTaxTotalAmount($taxTotalAmount);
                }
            }
        }

        return $specifiedTradeSettlementHeaderMonetarySummation;
    }
}
