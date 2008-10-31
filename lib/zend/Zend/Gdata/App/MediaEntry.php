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
 * @see Zend_Gdata_App_Entry
 */
require_once 'Zend/Gdata/App/Entry.php';

/**
 * @see Zend_Gdata_App_MediaSource
 */
require_once 'Zend/Gdata/App/MediaSource.php';

/**
 * @see Zend_Mime
 */
require_once 'Zend/Mime.php';

/**
 * @see Zend_Mime_Message
 */
require_once 'Zend/Mime/Message.php';


/**
 * Concrete class for working with Atom entries containing multi-part data.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App_MediaEntry extends Zend_Gdata_App_Entry
{
    /**
     * The attached MediaSource/file
     *
     * @var Zend_Gdata_App_MediaSource 
     */
    protected $_mediaSource = null;

    /**
     * The Zend_Mime object used to generate the boundary
     *
     * @var Zend_Mime 
     */
    protected $_mime = null;
   
    /**
     * Constructs a new MediaEntry, representing XML data and optional
     * file to upload
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null, $mediaSource = null)
    {
        parent::__construct($element);
        $this->_mime = new Zend_Mime();
        $this->_mediaSource = $mediaSource;
    }
 
    /**
     * Return the Zend_Mime object associated with this MediaEntry.  This
     * object is used to generate the media boundaries.
     * 
     * @return Zend_Mime The Zend_Mime object associated with this MediaEntry.
     */
    public function getMime()
    {
        return $this->_mime;
    }
    
    /**
     * Return the MIME multipart representation of this MediaEntry.
     *
     * @return string The MIME multipart representation of this MediaEntry
     */
    public function encode()
    {
        $xmlData = $this->saveXML();
        if ($this->getMediaSource() === null) {
            // No attachment, just send XML for entry
            return $xmlData;
        } else {
            $mimeMessage = new Zend_Mime_Message();
            $mimeMessage->setMime($this->_mime);
           
            $xmlPart = new Zend_Mime_Part($xmlData);
            $xmlPart->type = 'application/atom+xml';
            $xmlPart->encoding = null;
            $mimeMessage->addPart($xmlPart);
            
            $binaryPart = new Zend_Mime_Part($this->getMediaSource()->encode());
            $binaryPart->type = $this->getMediaSource()->getContentType();
            $binaryPart->encoding = null;
            $mimeMessage->addPart($binaryPart);

            return $mimeMessage->generateMessage();
        }
    }
   
    /**
     * Return the MediaSource object representing the file attached to this
     * MediaEntry.
     *
     * @return Zend_Gdata_App_MediaSource The attached MediaSource/file
     */
    public function getMediaSource()
    {
        return $this->_mediaSource;
    }

    /**
     * Set the MediaSource object (file) for this MediaEntry
     *
     * @param Zend_Gdata_App_MediaSource $value The attached MediaSource/file
     * @return Zend_Gdata_App_MediaEntry Provides a fluent interface
     */
    public function setMediaSource($value)
    {
        if ($value instanceof Zend_Gdata_App_MediaSource) {
            $this->_mediaSource = $value;
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'You must specify the media data as a class that conforms to Zend_Gdata_App_MediaSource.');
        }
        return $this;
    }
    
    /**
     * Return the boundary used in the MIME multipart message
     *
     * @return string The boundary used in the MIME multipart message 
     */
    public function getBoundary()
    {
        return $this->_mime->boundary();
    }

}
