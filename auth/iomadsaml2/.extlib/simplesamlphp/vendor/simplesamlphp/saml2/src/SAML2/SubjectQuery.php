<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;

use SAML2\XML\saml\NameID;

/**
 * Base class for SAML 2 subject query messages.
 *
 * This base class can be used for various requests which ask for
 * information about a particular subject.
 *
 * Note that this class currently only handles the simple case - where the
 * subject doesn't contain any sort of subject confirmation requirements.
 *
 * @package SimpleSAMLphp
 */
abstract class SubjectQuery extends Request
{
    /**
     * The NameId of the subject in the query.
     *
     * @var \SAML2\XML\saml\NameID|null
     */
    private $nameId = null;


    /**
     * Constructor for SAML 2 subject query messages.
     *
     * @param string $tagName The tag name of the root element.
     * @param \DOMElement|null $xml The input message.
     */
    protected function __construct(string $tagName, DOMElement $xml = null)
    {
        parent::__construct($tagName, $xml);

        if ($xml === null) {
            return;
        }

        $this->parseSubject($xml);
    }


    /**
     * Parse subject in query.
     *
     * @param \DOMElement $xml The SubjectQuery XML element.
     * @throws \Exception
     * @return void
     */
    private function parseSubject(\DOMElement $xml) : void
    {
        /** @var \DOMElement[] $subject */
        $subject = Utils::xpQuery($xml, './saml_assertion:Subject');
        if (empty($subject)) {
            throw new \Exception('Missing subject in subject query.');
        } elseif (count($subject) > 1) {
            throw new \Exception('More than one <saml:Subject> in subject query.');
        }

        /** @var \DOMElement[] $nameId */
        $nameId = Utils::xpQuery($subject[0], './saml_assertion:NameID');
        if (empty($nameId)) {
            throw new \Exception('Missing <saml:NameID> in <saml:Subject>.');
        } elseif (count($nameId) > 1) {
            throw new \Exception('More than one <saml:NameID> in <saml:Subject>.');
        }
        $this->nameId = new NameID($nameId[0]);
    }


    /**
     * Retrieve the NameId of the subject in the query.
     *
     * @return \SAML2\XML\saml\NameID|null The name identifier of the assertion.
     */
    public function getNameId() : ?NameID
    {
        return $this->nameId;
    }


    /**
     * Set the NameId of the subject in the query.
     *
     * @param \SAML2\XML\saml\NameID|null $nameId The name identifier of the assertion.
     * @return void
     */
    public function setNameId(NameID $nameId = null) : void
    {
        $this->nameId = $nameId;
    }


    /**
     * Convert subject query message to an XML element.
     *
     * @return \DOMElement This subject query.
     */
    public function toUnsignedXML() : DOMElement
    {
        if ($this->nameId === null) {
            throw new \Exception('Cannot convert SubjectQuery to XML without a NameID set.');
        }
        $root = parent::toUnsignedXML();

        $subject = $root->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:Subject');
        $root->appendChild($subject);

        $this->nameId->toXML($subject);

        return $root;
    }
}
