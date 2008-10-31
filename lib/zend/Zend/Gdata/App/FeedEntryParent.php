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
 * @see Zend_Gdata_App_Extension_Element
*/
require_once 'Zend/Gdata/App/Extension/Element.php';

/**
 * @see Zend_Gdata_App_Extension_Author
*/
require_once 'Zend/Gdata/App/Extension/Author.php';

/**
 * @see Zend_Gdata_App_Extension_Category
*/
require_once 'Zend/Gdata/App/Extension/Category.php';

/**
 * @see Zend_Gdata_App_Extension_Contributor
*/
require_once 'Zend/Gdata/App/Extension/Contributor.php';

/**
 * @see Zend_Gdata_App_Extension_Id
 */
require_once 'Zend/Gdata/App/Extension/Id.php';

/**
 * @see Zend_Gdata_App_Extension_Link
 */
require_once 'Zend/Gdata/App/Extension/Link.php';

/**
 * @see Zend_Gdata_App_Extension_Rights
 */
require_once 'Zend/Gdata/App/Extension/Rights.php';

/**
 * @see Zend_Gdata_App_Extension_Title
 */
require_once 'Zend/Gdata/App/Extension/Title.php';

/**
 * @see Zend_Gdata_App_Extension_Updated
 */
require_once 'Zend/Gdata/App/Extension/Updated.php';

/**
 * Zend_Version
 */
require_once 'Zend/Version.php';

/**
 * Abstract class for common functionality in entries and feeds
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Gdata_App_FeedEntryParent extends Zend_Gdata_App_Base
{

    /**
     * HTTP client object to use for retrieving feeds
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient = null;

    protected $_author = array();
    protected $_category = array();
    protected $_contributor = array();
    protected $_id = null;
    protected $_link = array();
    protected $_rights = null;
    protected $_title = null;
    protected $_updated = null;

    /**
     * Constructs a Feed or Entry
     */
    public function __construct($element = null)
    {
        if (!($element instanceof DOMElement)) {
            if ($element) {
                // Load the feed as an XML DOMDocument object
                @ini_set('track_errors', 1);
                $doc = new DOMDocument();
                $success = @$doc->loadXML($element);
                @ini_restore('track_errors');
                if (!$success) {
                    require_once 'Zend/Gdata/App/Exception.php';
                    throw new Zend_Gdata_App_Exception("DOMDocument cannot parse XML: $php_errormsg");
                }
                $element = $doc->getElementsByTagName($this->_rootElement)->item(0);
                if (!$element) {
                    require_once 'Zend/Gdata/App/Exception.php';
                    throw new Zend_Gdata_App_Exception('No root <' . $this->_rootElement . '> element found, cannot parse feed.');
                }
                $this->transferFromDOM($element);
            }
        } else {
            $this->transferFromDOM($element);
        }
    }

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feed.
     *
     * @param  Zend_Http_Client $httpClient
     * @return Zend_Gdata_App_Feed Provides a fluent interface
     */
    public function setHttpClient(Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }


    /**
     * Gets the HTTP client object. If none is set, a new Zend_Http_Client will be used.
     *
     * @return Zend_Http_Client_Abstract
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient instanceof Zend_Http_Client) {
            /**
             * @see Zend_Http_Client
             */
            require_once 'Zend/Http/Client.php';
            $this->_httpClient = new Zend_Http_Client();
            $useragent = 'Zend_Framework_Gdata/' . Zend_Version::VERSION;
            $this->_httpClient->setConfig(array(
                'strictredirects' => true,
                 'useragent' => $useragent
                )
            );
        }
        return $this->_httpClient;
    }


    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        foreach ($this->_author as $author) {
            $element->appendChild($author->getDOM($element->ownerDocument));
        }
        foreach ($this->_category as $category) {
            $element->appendChild($category->getDOM($element->ownerDocument));
        }
        foreach ($this->_contributor as $contributor) {
            $element->appendChild($contributor->getDOM($element->ownerDocument));
        }
        if ($this->_id != null) {
            $element->appendChild($this->_id->getDOM($element->ownerDocument));
        }
        foreach ($this->_link as $link) {
            $element->appendChild($link->getDOM($element->ownerDocument));
        }
        if ($this->_rights != null) {
            $element->appendChild($this->_rights->getDOM($element->ownerDocument));
        }
        if ($this->_title != null) {
            $element->appendChild($this->_title->getDOM($element->ownerDocument));
        }
        if ($this->_updated != null) {
            $element->appendChild($this->_updated->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('atom') . ':' . 'author':
            $author = new Zend_Gdata_App_Extension_Author();
            $author->transferFromDOM($child);
            $this->_author[] = $author;
            break;
        case $this->lookupNamespace('atom') . ':' . 'category':
            $category = new Zend_Gdata_App_Extension_Category();
            $category->transferFromDOM($child);
            $this->_category[] = $category;
            break;
        case $this->lookupNamespace('atom') . ':' . 'contributor':
            $contributor = new Zend_Gdata_App_Extension_Contributor();
            $contributor->transferFromDOM($child);
            $this->_contributor[] = $contributor;
            break;
        case $this->lookupNamespace('atom') . ':' . 'id':
            $id = new Zend_Gdata_App_Extension_Id();
            $id->transferFromDOM($child);
            $this->_id = $id;
            break;
        case $this->lookupNamespace('atom') . ':' . 'link':
            $link = new Zend_Gdata_App_Extension_Link();
            $link->transferFromDOM($child);
            $this->_link[] = $link;
            break;
        case $this->lookupNamespace('atom') . ':' . 'rights':
            $rights = new Zend_Gdata_App_Extension_Rights();
            $rights->transferFromDOM($child);
            $this->_rights = $rights;
            break;
        case $this->lookupNamespace('atom') . ':' . 'title':
            $title = new Zend_Gdata_App_Extension_Title();
            $title->transferFromDOM($child);
            $this->_title = $title;
            break;
        case $this->lookupNamespace('atom') . ':' . 'updated':
            $updated = new Zend_Gdata_App_Extension_Updated();
            $updated->transferFromDOM($child);
            $this->_updated = $updated;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * @return Zend_Gdata_App_Extension_Author
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * Sets the list of the authors of this feed/entry.  In an atom feed, each
     * author is represented by an atom:author element
     *
     * @param array $value
     * @return Zend_Gdata_App_FeedEntryParent Provides a fluent interface
     */
    public function setAuthor($value)
    {
        $this->_author = $value;
        return $this;
    }

    /**
     * Returns the array of categories that classify this feed/entry.  Each
     * category is represented in an atom feed by an atom:category element.
     *
     * @return array Array of Zend_Gdata_App_Extension_Category
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * Sets the array of categories that classify this feed/entry.  Each
     * category is represented in an atom feed by an atom:category element.
     *
     * @param array $value Array of Zend_Gdata_App_Extension_Category
     * @return Zend_Gdata_App_FeedEntryParent Provides a fluent interface
     */
    public function setCategory($value)
    {
        $this->_category = $value;
        return $this;
    }

    /**
     * Returns the array of contributors to this feed/entry.  Each contributor
     * is represented in an atom feed by an atom:contributor XML element
     *
     * @return array An array of Zend_Gdata_App_Extension_Contributor
     */
    public function getContributor()
    {
        return $this->_contributor;
    }

    /**
     * Sets the array of contributors to this feed/entry.  Each contributor
     * is represented in an atom feed by an atom:contributor XML element
     *
     * @param array $value
     * @return Zend_Gdata_App_FeedEntryParent Provides a fluent interface
     */
    public function setContributor($value)
    {
        $this->_contributor = $value;
        return $this;
    }

    /**
     * @return Zend_Gdata_App_Extension_Id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param Zend_Gdata_App_Extension_Id $value
     * @return Zend_Gdata_App_FeedEntryParent Provides a fluent interface
     */
    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }

    /**
     * Given a particular 'rel' value, this method returns a matching
     * Zend_Gdata_App_Extension_Link element.  If the 'rel' value 
     * is not provided, the full array of Zend_Gdata_App_Extension_Link 
     * elements is returned.  In an atom feed, each link is represented
     * by an atom:link element.  The 'rel' value passed to this function
     * is the atom:link/@rel attribute.  Example rel values include 'self',
     * 'edit', and 'alternate'.
     *
     * @param string $rel The rel value of the link to be found.  If null,
     *     the array of Zend_Gdata_App_Extension_link elements is returned
     * @return mixed Either a single Zend_Gdata_App_Extension_link element,
     *     an array of the same or null is returned depending on the rel value
     *     supplied as the argument to this function
     */
    public function getLink($rel = null)
    {
        if ($rel == null) {
            return $this->_link;
        } else {
            foreach ($this->_link as $link) {
                if ($link->rel == $rel) {
                    return $link;
                }
            }
            return null;
        }
    }

    /**
     * Returns the Zend_Gdata_App_Extension_Link element which represents
     * the URL used to edit this resource.  This link is in the atom feed/entry
     * as an atom:link with a rel attribute value of 'edit'.  
     *
     * @return Zend_Gdata_App_Extension_Link The link, or null if not found
     */
    public function getEditLink()
    {
        return $this->getLink('edit');
    }

    /**
     * Returns the Zend_Gdata_App_Extension_Link element which represents
     * the URL used to retrieve the next chunk of results when paging through
     * a feed.  This link is in the atom feed as an atom:link with a 
     * rel attribute value of 'next'.  
     *
     * @return Zend_Gdata_App_Extension_Link The link, or null if not found
     */
    public function getNextLink()
    {
        return $this->getLink('next');
    }

    /**
     * Returns the Zend_Gdata_App_Extension_Link element which represents
     * the URL used to retrieve the previous chunk of results when paging 
     * through a feed.  This link is in the atom feed as an atom:link with a 
     * rel attribute value of 'previous'.  
     *
     * @return Zend_Gdata_App_Extension_Link The link, or null if not found
     */
    public function getPreviousLink()
    {
        return $this->getLink('previous');
    }

    /**
     * @return Zend_Gdata_App_Extension_Link
     */
    public function getLicenseLink()
    {
        return $this->getLink('license');
    }

    /**
     * Returns the Zend_Gdata_App_Extension_Link element which represents
     * the URL used to retrieve the entry or feed represented by this object
     * This link is in the atom feed/entry as an atom:link with a 
     * rel attribute value of 'self'.  
     *
     * @return Zend_Gdata_App_Extension_Link The link, or null if not found
     */
    public function getSelfLink()
    {
        return $this->getLink('self');
    }

    /**
     * Returns the Zend_Gdata_App_Extension_Link element which represents
     * the URL for an alternate view of the data represented by this feed or
     * entry.  This alternate view is commonly a user-facing webpage, blog 
     * post, etc.  The MIME type for the data at the URL is available from the
     * returned Zend_Gdata_App_Extension_Link element. 
     * This link is in the atom feed/entry as an atom:link with a 
     * rel attribute value of 'self'.  
     *
     * @return Zend_Gdata_App_Extension_Link The link, or null if not found
     */
    public function getAlternateLink()
    {
        return $this->getLink('alternate');
    }

    /**
     * @param array $value The array of Zend_Gdata_App_Extension_Link elements
     * @return Zend_Gdata_App_FeedEntryParent Provides a fluent interface
     */
    public function setLink($value)
    {
        $this->_link = $value;
        return $this;
    }

    /**
     * @return Zend_Gdata_AppExtension_Rights
     */
    public function getRights()
    {
        return $this->_rights;
    }

    /**
     * @param Zend_Gdata_App_Extension_Rights $value
     * @return Zend_Gdata_App_FeedEntryParent Provides a fluent interface
     */
    public function setRights($value)
    {
        $this->_rights = $value;
        return $this;
    }

    /**
     * Returns the title of this feed or entry.  The title is an extremely
     * short textual representation of this resource and is found as
     * an atom:title element in a feed or entry
     *
     * @return Zend_Gdata_App_Extension_Title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Returns a string representation of the title of this feed or entry.  
     * The title is an extremely short textual representation of this 
     * resource and is found as an atom:title element in a feed or entry
     *
     * @return string
     */
    public function getTitleValue()
    {
        if (($titleObj = $this->getTitle()) != null) {
            return $titleObj->getText();
        } else {
            return null;
        }
    }

    /**
     * Returns the title of this feed or entry.  The title is an extremely
     * short textual representation of this resource and is found as
     * an atom:title element in a feed or entry
     *
     * @param Zend_Gdata_App_Extension_Title $value
     * @return Zend_Gdata_App_Feed_Entry_Parent Provides a fluent interface
     */
    public function setTitle($value)
    {
        $this->_title = $value;
        return $this;
    }

    /**
     * @return Zend_Gdata_App_Extension_Updated
     */
    public function getUpdated()
    {
        return $this->_updated;
    }

    /**
     * @param Zend_Gdata_App_Extension_Updated $value
     * @return Zend_Gdata_App_Feed_Entry_Parent Provides a fluent interface
     */
    public function setUpdated($value)
    {
        $this->_updated = $value;
        return $this;
    }

}
