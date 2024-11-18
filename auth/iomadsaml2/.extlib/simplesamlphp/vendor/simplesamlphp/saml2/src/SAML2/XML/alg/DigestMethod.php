<?php

declare(strict_types=1);

namespace SAML2\XML\alg;

use DOMElement;
use Webmozart\Assert\Assert;

/**
 * Class for handling the alg:DigestMethod element.
 *
 * @link http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-metadata-algsupport.pdf
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package simplesamlphp/saml2
 */
class DigestMethod
{
    /**
     * An URI identifying an algorithm supported for digest operations.
     *
     * @var string
     */
    private $Algorithm = '';


    /**
     * Create/parse an alg:DigestMethod element.
     *
     * @param \DOMElement|null $xml The XML element we should load or null to create a new one from scratch.
     *
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('Algorithm')) {
            throw new \Exception('Missing required attribute "Algorithm" in alg:DigestMethod element.');
        }
        $this->setAlgorithm($xml->getAttribute('Algorithm'));
    }


    /**
     * Collect the value of the algorithm-property
     *
     * @return string
     */
    public function getAlgorithm() : string
    {
        return $this->Algorithm;
    }


    /**
     * Set the value of the Algorithm-property
     *
     * @param string $algorithm
     * @return void
     */
    public function setAlgorithm(string $algorithm) : void
    {
        $this->Algorithm = $algorithm;
    }


    /**
     * Convert this element to XML.
     *
     * @param \DOMElement $parent The element we should append to.
     * @return \DOMElement
     * @throws \Exception
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        Assert::notEmpty($this->Algorithm, 'Cannot convert DigestMethod to XML without an Algorithm set.');

        $doc = $parent->ownerDocument;
        $e = $doc->createElementNS(Common::NS, 'alg:DigestMethod');
        $parent->appendChild($e);
        $e->setAttribute('Algorithm', $this->Algorithm);

        return $e;
    }
}
