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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_App_Feed
 */
require_once 'Zend/Gdata/App/Feed.php';

/**
 * @see Zend_Gdata_Entry
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_Extension_OpenSearchTotalResults
 */
require_once 'Zend/Gdata/Extension/OpenSearchTotalResults.php';

/**
 * @see Zend_Gdata_Extension_OpenSearchStartIndex
 */
require_once 'Zend/Gdata/Extension/OpenSearchStartIndex.php';

/**
 * @see Zend_Gdata_Extension_OpenSearchItemsPerPage
 */
require_once 'Zend/Gdata/Extension/OpenSearchItemsPerPage.php';

/**
 * The GData flavor of an Atom Feed
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Feed extends Zend_Gdata_App_Feed
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend_Gdata_Entry';

    /**
     * The openSearch:totalResults element
     *
     * @var string
     */
    protected $_totalResults = null;

    /**
     * The openSearch:startIndex element
     *
     * @var string
     */
    protected $_startIndex = null;

    /**
     * The openSearch:itemsPerPage element
     *
     * @var string
     */
    protected $_itemsPerPage = null;

    public function __construct($element = null)
    {
        foreach (Zend_Gdata::$namespaces as $nsPrefix => $nsUri) {
            $this->registerNamespace($nsPrefix, $nsUri);
        }
        parent::__construct($element);
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_totalResults != null) {
            $element->appendChild($this->_totalResults->getDOM($element->ownerDocument));
        }
        if ($this->_startIndex != null) {
            $element->appendChild($this->_startIndex->getDOM($element->ownerDocument));
        }
        if ($this->_itemsPerPage != null) {
            $element->appendChild($this->_itemsPerPage->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('openSearch') . ':' . 'totalResults':
            $totalResults = new Zend_Gdata_Extension_OpenSearchTotalResults();
            $totalResults->transferFromDOM($child);
            $this->_totalResults = $totalResults;
            break;
        case $this->lookupNamespace('openSearch') . ':' . 'startIndex':
            $startIndex = new Zend_Gdata_Extension_OpenSearchStartIndex();
            $startIndex->transferFromDOM($child);
            $this->_startIndex = $startIndex;
            break;
        case $this->lookupNamespace('openSearch') . ':' . 'itemsPerPage':
            $itemsPerPage = new Zend_Gdata_Extension_OpenSearchItemsPerPage();
            $itemsPerPage->transferFromDOM($child);
            $this->_itemsPerPage = $itemsPerPage;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    function setTotalResults($value) {
        $this->_totalResults = $value;
        return $this;
    }

    function getTotalResults() {
        return $this->_totalResults;
    }

    function setStartIndex($value) {
        $this->_startIndex = $value;
        return $this;
    }

    function getStartIndex() {
        return $this->_startIndex;
    }

    function setItemsPerPage($value) {
        $this->_itemsPerPage = $value;
        return $this;
    }

    function getItemsPerPage() {
        return $this->_itemsPerPage;
    }

}
