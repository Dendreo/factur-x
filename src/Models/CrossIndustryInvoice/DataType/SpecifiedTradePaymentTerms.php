<?php

declare(strict_types=1);

namespace Dendreo\FacturX\Models\CrossIndustryInvoice\DataType;

use Dendreo\FacturX\Models\EN16931\DataType\Identifier\MandateReferenceIdentifier;
use Dendreo\FacturX\Models\EN16931\Invoice;

/**
 * BT-20-00.
 */
class SpecifiedTradePaymentTerms
{
    protected const XML_NODE = 'ram:SpecifiedTradePaymentTerms';

    /**
     * BT-20.
     */
    private ?string $description;

    /**
     * BT-9-00.
     */
    private ?DueDateDateTime $dueDateDateTime;

    /**
     * BT-89.
     */
    private ?MandateReferenceIdentifier $directDebitMandateIdentifier;

    public function __construct(?DueDateDateTime $dueDateDateTime = null)
    {
        $this->dueDateDateTime              = $dueDateDateTime;
        $this->description                  = null;
        $this->directDebitMandateIdentifier = null;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDueDateDateTime(): DueDateDateTime
    {
        return $this->dueDateDateTime;
    }

    public function setDueDateDateTime(?DueDateDateTime $dueDateDateTime): static
    {
        $this->dueDateDateTime = $dueDateDateTime;

        return $this;
    }

    public function getDirectDebitMandateIdentifier(): ?MandateReferenceIdentifier
    {
        return $this->directDebitMandateIdentifier;
    }

    public function setDirectDebitMandateIdentifier(?MandateReferenceIdentifier $directDebitMandateIdentifier): static
    {
        $this->directDebitMandateIdentifier = $directDebitMandateIdentifier;

        return $this;
    }

    public function toXML(\DOMDocument $document): \DOMElement
    {
        $element = $document->createElement(self::XML_NODE);

        if (\is_string($this->description)) {
            $element->appendChild($document->createElement('ram:Description', $this->description));
        }

        if ($this->dueDateDateTime instanceof DueDateDateTime) {
            $element->appendChild($this->dueDateDateTime->toXML($document));
        }

        if ($this->directDebitMandateIdentifier instanceof MandateReferenceIdentifier) {
            $element->appendChild($document->createElement('ram:DirectDebitMandateID', $this->directDebitMandateIdentifier->value));
        }

        return $element;
    }

    public static function fromXML(\DOMXPath $xpath, \DOMElement $currentElement): ?self
    {
        $specifiedTradePaymentTermsElements = $xpath->query(sprintf('./%s', self::XML_NODE), $currentElement);

        if ($specifiedTradePaymentTermsElements->count() === 0) {
            return null;
        }

        if ($specifiedTradePaymentTermsElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        /** @var \DOMElement $specifiedTradePaymentTermsElement */
        $specifiedTradePaymentTermsElement = $specifiedTradePaymentTermsElements->item(0);

        $descriptionElements                  = $xpath->query('./ram:Description', $specifiedTradePaymentTermsElement);
        $directDebitMandateIdentifierElements = $xpath->query('./ram:DirectDebitMandateID', $specifiedTradePaymentTermsElement);

        if ($descriptionElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        if ($directDebitMandateIdentifierElements->count() > 1) {
            throw new \Exception('Malformed');
        }

        $dueDateDateTime = DueDateDateTime::fromXML($xpath, $specifiedTradePaymentTermsElement);

        $specifiedTradePaymentTerms = new self();

        if ($descriptionElements->count() === 1) {
            $specifiedTradePaymentTerms->setDescription($descriptionElements->item(0)->nodeValue);
        }

        if ($directDebitMandateIdentifierElements->count() === 1) {
            $specifiedTradePaymentTerms->setDirectDebitMandateIdentifier(new MandateReferenceIdentifier($directDebitMandateIdentifierElements->item(0)->nodeValue));
        }

        if ($dueDateDateTime instanceof DueDateDateTime) {
            $specifiedTradePaymentTerms->setDueDateDateTime($dueDateDateTime);
        }

        return $specifiedTradePaymentTerms;
    }

    public static function fromEN16931(Invoice $invoice): self
    {
        return (new self())
            ->setDescription($invoice->getPaymentTerms())
            ->setDueDateDateTime(
                $invoice->getPaymentDueDate() instanceof \DateTimeInterface
                    ? DueDateDateTime::fromEN16931($invoice->getPaymentDueDate())
                    : null
            )
            ->setDirectDebitMandateIdentifier($invoice->getPaymentInstructions()?->getDirectDebit()?->getMandateReferenceIdentifier());
    }
}
