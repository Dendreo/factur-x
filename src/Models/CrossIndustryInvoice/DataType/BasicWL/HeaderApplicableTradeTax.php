<?php

declare(strict_types=1);

namespace Dendreo\FacturX\DataType\BasicWL;

use Models\EN16931\DataType\DateCode2475;
use Models\EN16931\DataType\VatCategory;
use Models\EN16931\DataType\VatExoneration;
use Models\EN16931\SemanticDataType\Amount;
use Models\EN16931\SemanticDataType\Percentage;

/**
 * BG-23.
 */
class HeaderApplicableTradeTax
{
    protected const XML_NODE = 'ram:ApplicableTradeTax';

    /**
     * BT-117.
     */
    protected Amount $calculatedAmount;

    /**
     * BT-118-0.
     */
    protected string $typeCode;

    /**
     * BT-120.
     */
    protected ?string $exemptionReason;

    /**
     * BT-116.
     */
    protected Amount $basisAmount;

    /**
     * BT-118.
     */
    protected VatCategory $categoryCode;

    /**
     * BT-121.
     */
    protected ?VatExoneration $exemptionReasonCode;

    /**
     * BT-8.
     */
    protected ?DateCode2475 $dueDateTypeCode;

    /**
     * BT-119.
     */
    protected ?Percentage $rateApplicablePercent;

    public function __construct(float $calculatedAmount, float $basisAmount, VatCategory $categoryCode, ?Percentage $rateApplicablePercent = null)
    {
        $this->calculatedAmount      = new Amount($calculatedAmount);
        $this->basisAmount           = new Amount($basisAmount);
        $this->categoryCode          = $categoryCode;
        $this->typeCode              = 'VAT';
        $this->exemptionReason       = null;
        $this->exemptionReasonCode   = null;
        $this->dueDateTypeCode       = null;
        $this->rateApplicablePercent = $rateApplicablePercent;
    }

    public function getCalculatedAmount(): float
    {
        return $this->calculatedAmount->getValueRounded();
    }

    public function getTypeCode(): string
    {
        return $this->typeCode;
    }

    public function getExemptionReason(): ?string
    {
        return $this->exemptionReason;
    }

    public function setExemptionReason(?string $exemptionReason): static
    {
        $this->exemptionReason = $exemptionReason;

        return $this;
    }

    public function getBasisAmount(): float
    {
        return $this->basisAmount->getValueRounded();
    }

    public function getCategoryCode(): VatCategory
    {
        return $this->categoryCode;
    }

    public function getExemptionReasonCode(): ?VatExoneration
    {
        return $this->exemptionReasonCode;
    }

    public function setExemptionReasonCode(?VatExoneration $exemptionReasonCode): static
    {
        $this->exemptionReasonCode = $exemptionReasonCode;

        return $this;
    }

    public function getDueDateTypeCode(): ?DateCode2475
    {
        return $this->dueDateTypeCode;
    }

    public function setDueDateTypeCode(?DateCode2475 $dueDateTypeCode): static
    {
        $this->dueDateTypeCode = $dueDateTypeCode;

        return $this;
    }

    public function getRateApplicablePercent(): ?float
    {
        return $this->rateApplicablePercent?->getValueRounded();
    }

    public function setRateApplicablePercent(?float $rateApplicablePercent): static
    {
        $this->rateApplicablePercent = \is_float($rateApplicablePercent) ? new Percentage($rateApplicablePercent) : null;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($document->createElement('ram:CalculatedAmount', $this->calculatedAmount->getFormattedValueRounded()));
        $currentNode->appendChild($document->createElement('ram:TypeCode', $this->typeCode));

        if (\is_string($this->exemptionReason)) {
            $currentNode->appendChild($document->createElement('ram:ExemptionReason', $this->exemptionReason));
        }

        $currentNode->appendChild($document->createElement('ram:BasisAmount', $this->basisAmount->getFormattedValueRounded()));
        $currentNode->appendChild($document->createElement('ram:CategoryCode', $this->categoryCode->value));

        if ($this->exemptionReasonCode instanceof VatExoneration) {
            $currentNode->appendChild($document->createElement('ram:ExemptionReasonCode', $this->exemptionReasonCode->value));
        }

        if ($this->dueDateTypeCode instanceof DateCode2475) {
            $currentNode->appendChild($document->createElement('ram:DueDateTypeCode', $this->dueDateTypeCode->value));
        }

        if ($this->rateApplicablePercent instanceof Percentage) {
            $currentNode->appendChild($document->createElement('ram:RateApplicablePercent', $this->rateApplicablePercent->getFormattedValueRounded()));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $headerApplicableTradeTaxElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if ($headerApplicableTradeTaxElements->count() === 0) {
            return [];
        }

        $headerApplicableTradeTaxes = [];

        foreach ($headerApplicableTradeTaxElements as $headerApplicableTradeTaxElement) {
            $calculatedAmountElements      = $xpath->query('./ram:CalculatedAmount', $headerApplicableTradeTaxElement);
            $typeCodeElements              = $xpath->query('./ram:TypeCode', $headerApplicableTradeTaxElement);
            $exemptionReasonElements       = $xpath->query('./ram:ExemptionReason', $headerApplicableTradeTaxElement);
            $basisAmountElements           = $xpath->query('./ram:BasisAmount', $headerApplicableTradeTaxElement);
            $categoryCodeElements          = $xpath->query('./ram:CategoryCode', $headerApplicableTradeTaxElement);
            $exemptionReasonCodeElements   = $xpath->query('./ram:ExemptionReasonCode', $headerApplicableTradeTaxElement);
            $dueDateTypeCodeElements       = $xpath->query('./ram:DueDateTypeCode', $headerApplicableTradeTaxElement);
            $rateApplicablePercentElements = $xpath->query('./ram:RateApplicablePercent', $headerApplicableTradeTaxElement);

            if ($calculatedAmountElements->count() !== 1) {
                throw new \Exception('Malformed');
            }

            if ($typeCodeElements->count() !== 1) {
                throw new \Exception('Malformed');
            }

            if ($exemptionReasonElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if ($basisAmountElements->count() !== 1) {
                throw new \Exception('Malformed');
            }

            if ($categoryCodeElements->count() !== 1) {
                throw new \Exception('Malformed');
            }

            if ($exemptionReasonCodeElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if ($dueDateTypeCodeElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if ($rateApplicablePercentElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $calculatedAmount = $calculatedAmountElements->item(0)->nodeValue;
            $basisAmount      = $basisAmountElements->item(0)->nodeValue;
            $categoryCode     = VatCategory::tryFrom($categoryCodeElements->item(0)->nodeValue);

            if ($categoryCode === null) {
                throw new \Exception('Wrong CategoryCode');
            }

            if ($typeCodeElements->item(0)->nodeValue !== 'VAT') {
                throw new \Exception('Wrong TypeCode');
            }

            $headerApplicableTradeTax = new self((float) $calculatedAmount, (float) $basisAmount, $categoryCode);

            if ($exemptionReasonElements->count() === 1) {
                $headerApplicableTradeTax->setExemptionReason($exemptionReasonElements->item(0)->nodeValue);
            }

            if ($exemptionReasonCodeElements->count() === 1) {
                $exemptionReasonCode = VatExoneration::tryFrom($exemptionReasonCodeElements->item(0)->nodeValue);

                if ($exemptionReasonCode === null) {
                    throw new \Exception('Wrong ExemptionReasonCode');
                }

                $headerApplicableTradeTax->setExemptionReasonCode($exemptionReasonCode);
            }

            if ($dueDateTypeCodeElements->count() === 1) {
                $dueDateTypeCode = DateCode2475::tryFrom($dueDateTypeCodeElements->item(0)->nodeValue);

                if ($dueDateTypeCode === null) {
                    throw new \Exception('Wrong DueDateTypeCode');
                }

                $headerApplicableTradeTax->setDueDateTypeCode($dueDateTypeCode);
            }

            if ($rateApplicablePercentElements->count() === 1) {
                $headerApplicableTradeTax->setRateApplicablePercent((float) $rateApplicablePercentElements->item(0)->nodeValue);
            }

            $headerApplicableTradeTaxes[] = $headerApplicableTradeTax;
        }

        return $headerApplicableTradeTaxes;
    }
}
