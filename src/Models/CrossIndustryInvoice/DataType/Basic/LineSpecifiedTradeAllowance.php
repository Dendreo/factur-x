<?php

namespace Dendreo\FacturX\Models\CrossIndustryInvoice\DataType\Basic;

use Dendreo\FacturX\Models\CrossIndustryInvoice\DataType\AllowanceIndicator;
use Dendreo\FacturX\Models\EN16931\DataType\AllowanceReasonCode;
use Dendreo\FacturX\Models\EN16931\SemanticDataType\Amount;

/**
 * BG-27.
 */
class LineSpecifiedTradeAllowance
{
    protected const XML_NODE = 'ram:SpecifiedTradeAllowanceCharge';

    /**
     * BG-27-0 & BG-27-1.
     */
    protected AllowanceIndicator $chargeIndicator;

    /**
     * BT-136.
     */
    protected Amount $actualAmount;

    /**
     * BT-140.
     */
    protected ?AllowanceReasonCode $reasonCode;

    /**
     * BT-139.
     */
    protected ?string $reason;

    public function __construct(float $actualAmount, ?AllowanceReasonCode $reasonCode = null, ?string $reason = null)
    {
        $this->actualAmount    = new Amount($actualAmount);
        $this->chargeIndicator = new AllowanceIndicator();
        $this->reasonCode      = $reasonCode;
        $this->reason          = $reason;
    }

    public function getChargeIndicator(): AllowanceIndicator
    {
        return $this->chargeIndicator;
    }

    public function getActualAmount(): float
    {
        return $this->actualAmount->getValueRounded();
    }

    public function getReasonCode(): ?AllowanceReasonCode
    {
        return $this->reasonCode;
    }

    public function setReasonCode(?AllowanceReasonCode $reasonCode): static
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $currentNode = $document->createElement(self::XML_NODE);

        $currentNode->appendChild($this->chargeIndicator->toXML($document));
        $currentNode->appendChild($document->createElement('ram:ActualAmount', $this->actualAmount->getFormattedValueRounded()));

        if ($this->reasonCode instanceof AllowanceReasonCode) {
            $currentNode->appendChild($document->createElement('ram:ReasonCode', $this->reasonCode->value));
        }

        if (\is_string($this->reason)) {
            $currentNode->appendChild($document->createElement('ram:Reason', $this->reason));
        }

        return $currentNode;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): array
    {
        $lineSpecifiedTradeAllowanceElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if ($lineSpecifiedTradeAllowanceElements->count() === 0) {
            return [];
        }

        $lineSpecifiedTradeAllowances = [];

        foreach ($lineSpecifiedTradeAllowanceElements as $lineSpecifiedTradeAllowanceElement) {
            $actualAmountElements = $xpath->query('./ram:ActualAmount', $lineSpecifiedTradeAllowanceElement);
            $reasonCodeElements   = $xpath->query('./ram:ReasonCode', $lineSpecifiedTradeAllowanceElement);
            $reasonElements       = $xpath->query('./ram:Reason', $lineSpecifiedTradeAllowanceElement);

            if ($actualAmountElements->count() !== 1) {
                throw new \Exception('Malformed');
            }

            if ($reasonCodeElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            if ($reasonElements->count() > 1) {
                throw new \Exception('Malformed');
            }

            $actualAmount = $actualAmountElements->item(0)->nodeValue;
            // Look if node is well constructed, already created in the constructor
            AllowanceIndicator::fromXML($xpath, $lineSpecifiedTradeAllowanceElement);

            $lineSpecifiedTradeAllowance = new self((float) $actualAmount);

            if ($reasonCodeElements->count() === 1) {
                $reasonCode = AllowanceReasonCode::tryFrom($reasonCodeElements->item(0)->nodeValue);

                if ($reasonCode === null) {
                    throw new \Exception('Wrong ReasonCode');
                }

                $lineSpecifiedTradeAllowance->setReasonCode($reasonCode);
            }

            if ($reasonElements->count() === 1) {
                $lineSpecifiedTradeAllowance->setReason($reasonElements->item(0)->nodeValue);
            }

            $lineSpecifiedTradeAllowances[] = $lineSpecifiedTradeAllowance;
        }

        return $lineSpecifiedTradeAllowances;
    }
}
