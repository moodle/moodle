<?php

declare(strict_types=1);

namespace SAML2\XML\mdui;

use DOMElement;
use Webmozart\Assert\Assert;

/**
 * Class for handling the Keywords metadata extensions for login and discovery user interface
 *
 * @link: http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-metadata-ui/v1.0/sstc-saml-metadata-ui-v1.0.pdf
 * @package SimpleSAMLphp
 */
class Keywords
{
    /**
     * The keywords of this item.
     *
     * Array of strings.
     *
     * @var string[]
     */
    private $Keywords = [];

    /**
     * The language of this item.
     *
     * @var string
     */
    private $lang = '';


    /**
     * Initialize a Keywords.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('xml:lang')) {
            throw new \Exception('Missing lang on Keywords.');
        }
        if (!strlen($xml->textContent)) {
            throw new \Exception('Missing value for Keywords.');
        }
        foreach (explode(' ', $xml->textContent) as $keyword) {
            $this->Keywords[] = str_replace('+', ' ', $keyword);
        }
        $this->lang = $xml->getAttribute('xml:lang');
    }


    /**
     * Collect the value of the lang-property
     *
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->lang;
    }


    /**
     * Set the value of the lang-property
     *
     * @param string $lang
     * @return void
     */
    public function setLanguage(string $lang) : void
    {
        $this->lang = $lang;
    }


    /**
     * Collect the value of the Keywords-property
     *
     * @return string[]
     */
    public function getKeywords() : array
    {
        return $this->Keywords;
    }


    /**
     * Set the value of the Keywords-property
     *
     * @param string[] $keywords
     * @return void
     */
    public function setKeywords(array $keywords) : void
    {
        $this->Keywords = $keywords;
    }


    /**
     * Add the value to the Keywords-property
     *
     * @param string $keyword
     * @return void
     */
    public function addKeyword(string $keyword) : void
    {
        $this->Keywords[] = $keyword;
    }


    /**
     * Convert this Keywords to XML.
     *
     * @param \DOMElement $parent The element we should append this Keywords to.
     * @throws \Exception
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        Assert::notEmpty($this->lang, "Cannot convert Keywords to XML without a language set.");

        $doc = $parent->ownerDocument;

        $e = $doc->createElementNS(Common::NS, 'mdui:Keywords');
        $e->setAttribute('xml:lang', $this->lang);
        $value = '';
        foreach ($this->Keywords as $keyword) {
            if (strpos($keyword, "+") !== false) {
                throw new \Exception('Keywords may not contain a "+" character.');
            }
            $value .= str_replace(' ', '+', $keyword).' ';
        }
        $value = rtrim($value);
        $e->appendChild($doc->createTextNode($value));
        $parent->appendChild($e);

        return $e;
    }
}
