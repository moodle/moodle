<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Document */
require_once 'Zend/Search/Lucene/Document.php';


/**
 * HTML document.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Document_Html extends Zend_Search_Lucene_Document
{
    /**
     * List of document links
     *
     * @var array
     */
    private $_links = array();

    /**
     * List of document header links
     *
     * @var array
     */
    private $_headerLinks = array();

    /**
     * Stored DOM representation
     *
     * @var DOMDocument
     */
    private $_doc;

    /**
     * Object constructor
     *
     * @param string  $data
     * @param boolean $isFile
     * @param boolean $storeContent
     */
    private function __construct($data, $isFile, $storeContent)
    {
        $this->_doc = new DOMDocument();
        $this->_doc->substituteEntities = true;

        if ($isFile) {
            @$this->_doc->loadHTMLFile($data);
        } else{
            @$this->_doc->loadHTML($data);
        }

        $xpath = new DOMXPath($this->_doc);

        $docTitle = '';
        $titleNodes = $xpath->query('/html/head/title');
        foreach ($titleNodes as $titleNode) {
            // title should always have only one entry, but we process all nodeset entries
            $docTitle .= $titleNode->nodeValue . ' ';
        }
        $this->addField(Zend_Search_Lucene_Field::Text('title', $docTitle, $this->_doc->actualEncoding));

        $metaNodes = $xpath->query('/html/head/meta[@name]');
        foreach ($metaNodes as $metaNode) {
            $this->addField(Zend_Search_Lucene_Field::Text($metaNode->getAttribute('name'),
                                                           $metaNode->getAttribute('content'),
                                                           $this->_doc->actualEncoding));
        }

        $docBody = '';
        $bodyNodes = $xpath->query('/html/body');
        foreach ($bodyNodes as $bodyNode) {
            // body should always have only one entry, but we process all nodeset entries
            $this->_retrieveNodeText($bodyNode, $docBody);
        }
        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('body', $docBody, $this->_doc->actualEncoding));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('body', $docBody, $this->_doc->actualEncoding));
        }

        $linkNodes = $this->_doc->getElementsByTagName('a');
        foreach ($linkNodes as $linkNode) {
            if (($href = $linkNode->getAttribute('href')) != '') {
                $this->_links[] = $href;
            }
        }
        $this->_links = array_unique($this->_links);

        $linkNodes = $xpath->query('/html/head/link');
        foreach ($linkNodes as $linkNode) {
            if (($href = $linkNode->getAttribute('href')) != '') {
                $this->_headerLinks[] = $href;
            }
        }
        $this->_headerLinks = array_unique($this->_headerLinks);
    }

    /**
     * Get node text
     *
     * We should exclude scripts, which may be not included into comment tags, CDATA sections,
     *
     * @param DOMNode $node
     * @param string &$text
     */
    private function _retrieveNodeText(DOMNode $node, &$text)
    {
        if ($node->nodeType == XML_TEXT_NODE) {
            $text .= $node->nodeValue ;
            $text .= ' ';
        } else if ($node->nodeType == XML_ELEMENT_NODE  &&  $node->nodeName != 'script') {
            foreach ($node->childNodes as $childNode) {
                $this->_retrieveNodeText($childNode, $text);
            }
        }
    }

    /**
     * Get document HREF links
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->_links;
    }

    /**
     * Get document header links
     *
     * @return array
     */
    public function getHeaderLinks()
    {
        return $this->_headerLinks;
    }

    /**
     * Load HTML document from a string
     *
     * @param string $data
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Html
     */
    public static function loadHTML($data, $storeContent = false)
    {
        return new Zend_Search_Lucene_Document_Html($data, false, $storeContent);
    }

    /**
     * Load HTML document from a file
     *
     * @param string $file
     * @param boolean $storeContent
     * @return Zend_Search_Lucene_Document_Html
     */
    public static function loadHTMLFile($file, $storeContent = false)
    {
        return new Zend_Search_Lucene_Document_Html($file, true, $storeContent);
    }


    /**
     * Highlight text in text node
     *
     * @param DOMText $node
     * @param array   $wordsToHighlight
     * @param string  $color
     */
    public function _highlightTextNode(DOMText $node, $wordsToHighlight, $color)
    {
        $analyzer = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        $analyzer->setInput($node->nodeValue, $this->_doc->encoding);

        $matchedTokens = array();

        while (($token = $analyzer->nextToken()) !== null) {
            if (isset($wordsToHighlight[$token->getTermText()])) {
                $matchedTokens[] = $token;
            }
        }

        if (count($matchedTokens) == 0) {
            return;
        }

        $matchedTokens = array_reverse($matchedTokens);

        foreach ($matchedTokens as $token) {
            // Cut text after matched token
            $node->splitText($token->getEndOffset());

            // Cut matched node
            $matchedWordNode = $node->splitText($token->getStartOffset());

            $highlightedNode = $this->_doc->createElement('b', $matchedWordNode->nodeValue);
            $highlightedNode->setAttribute('style', 'color:black;background-color:' . $color);

            $node->parentNode->replaceChild($highlightedNode, $matchedWordNode);
        }
    }


    /**
     * highlight words in content of the specified node
     *
     * @param DOMNode $contextNode
     * @param array $wordsToHighlight
     * @param string $color
     */
    public function _highlightNode(DOMNode $contextNode, $wordsToHighlight, $color)
    {
        $textNodes = array();

        if (!$contextNode->hasChildNodes()) {
            return;
        }

        foreach ($contextNode->childNodes as $childNode) {
            if ($childNode->nodeType == XML_TEXT_NODE) {
                // process node later to leave childNodes structure untouched
                $textNodes[] = $childNode;
            } else {
                // Skip script nodes
                if ($childNode->nodeName != 'script') {
                    $this->_highlightNode($childNode, $wordsToHighlight, $color);
                }
            }
        }

        foreach ($textNodes as $textNode) {
            $this->_highlightTextNode($textNode, $wordsToHighlight, $color);
        }
    }



    /**
     * Highlight text with specified color
     *
     * @param string|array $words
     * @param string $color
     * @return string
     */
    public function highlight($words, $color = '#66ffff')
    {
        if (!is_array($words)) {
            $words = array($words);
        }
        $wordsToHighlight = array();

        $analyzer = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
        foreach ($words as $wordString) {
            $wordsToHighlight = array_merge($wordsToHighlight, $analyzer->tokenize($wordString));
        }

        if (count($wordsToHighlight) == 0) {
            return $this->_doc->saveHTML();
        }

        $wordsToHighlightFlipped = array();
        foreach ($wordsToHighlight as $id => $token) {
            $wordsToHighlightFlipped[$token->getTermText()] = $id;
        }

        $xpath = new DOMXPath($this->_doc);

        $matchedNodes = $xpath->query("/html/body");
        foreach ($matchedNodes as $matchedNode) {
            $this->_highlightNode($matchedNode, $wordsToHighlightFlipped, $color);
        }

    }

    /**
     * Get HTML
     *
     * @return string
     */
    public function getHTML()
    {
        return $this->_doc->saveHTML();
    }
}

