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
 * @package    Zend_Dom
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Dom_Query_Css2Xpath
 */
require_once 'Zend/Dom/Query/Css2Xpath.php';

/**
 * @see Zend_Dom_Query_Result
 */
require_once 'Zend/Dom/Query/Result.php';

/**
 * Query DOM structures based on CSS selectors and/or XPath
 *
 * @package    Zend_Dom
 * @subpackage Query
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Dom_Query
{
    /**#@+
     * @const string Document types
     */
    const DOC_XML   = 'docXml';
    const DOC_HTML  = 'docHtml';
    const DOC_XHTML = 'docXhtml';
    /**#@-*/

    /**
     * @var string
     */
    protected $_document;

    /**
     * Document type
     * @var string
     */
    protected $_docType;

    /**
     * Constructor
     *
     * @param  null|string $document
     * @return void
     */
    public function __construct($document = null)
    {
        $this->setDocument($document);
    }

    /**
     * Set document to query
     *
     * @param  string $document
     * @return Zend_Dom_Query
     */
    public function setDocument($document)
    {
        if (0 === strlen($document)) {
            return $this;
        }
        if ('<?xml' == substr(trim($document), 0, 5)) {
            return $this->setDocumentXml($document);
        }
        if (strstr($document, 'DTD XHTML')) {
            return $this->setDocumentXhtml($document);
        }
        return $this->setDocumentHtml($document);
    }

    /**
     * Register HTML document
     *
     * @param  string $document
     * @return Zend_Dom_Query
     */
    public function setDocumentHtml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_HTML;
        return $this;
    }

    /**
     * Register XHTML document
     *
     * @param  string $document
     * @return Zend_Dom_Query
     */
    public function setDocumentXhtml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_XHTML;
        return $this;
    }

    /**
     * Register XML document
     *
     * @param  string $document
     * @return Zend_Dom_Query
     */
    public function setDocumentXml($document)
    {
        $this->_document = (string) $document;
        $this->_docType  = self::DOC_XML;
        return $this;
    }

    /**
     * Retrieve current document
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->_document;
    }

    /**
     * Get document type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->_docType;
    }

    /**
     * Perform a CSS selector query
     *
     * @param  string $query
     * @return Zend_Dom_Query_Result
     */
    public function query($query)
    {
        $xpathQuery = Zend_Dom_Query_Css2Xpath::transform($query);
        return $this->queryXpath($xpathQuery, $query);
    }

    /**
     * Perform an XPath query
     *
     * @param  string|array $xpathQuery
     * @param  string $query CSS selector query
     * @return Zend_Dom_Query_Result
     */
    public function queryXpath($xpathQuery, $query = null)
    {
        if (null === ($document = $this->getDocument())) {
            require_once 'Zend/Dom/Exception.php';
            throw new Zend_Dom_Exception('Cannot query; no document registered');
        }

        $domDoc = new DOMDocument;
        $type   = $this->getDocumentType();
        switch ($type) {
            case self::DOC_XML:
                $success = @$domDoc->loadXML($document);
                break;
            case self::DOC_HTML:
            case self::DOC_XHTML:
            default:
                $success = @$domDoc->loadHTML($document);
                break;
        }

        if (!$success) {
            require_once 'Zend/Dom/Exception.php';
            throw new Zend_Dom_Exception(sprintf('Error parsing document (type == %s)', $type));
        }

        $nodeList   = $this->_getNodeList($domDoc, $xpathQuery);
        return new Zend_Dom_Query_Result($query, $xpathQuery, $domDoc, $nodeList);
    }

    /**
     * Prepare node list
     *
     * @param  DOMDocument $document
     * @param  string|array $xpathQuery
     * @return array
     */
    protected function _getNodeList($document, $xpathQuery)
    {
        $xpath      = new DOMXPath($document);
        $xpathQuery = (string) $xpathQuery;
        if (preg_match_all('|\[contains\((@[a-z0-9_-]+),\s?\' |i', $xpathQuery, $matches)) {
            foreach ($matches[1] as $attribute) {
                $queryString = '//*[' . $attribute . ']';
                $attributeName = substr($attribute, 1);
                $nodes = $xpath->query($queryString);
                foreach ($nodes as $node) {
                    $attr = $node->attributes->getNamedItem($attributeName);
                    $attr->value = ' ' . $attr->value . ' ';
                }
            }
        }
        return $xpath->query($xpathQuery);
    }
}
