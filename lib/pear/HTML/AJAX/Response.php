<?php
/**
 * OO AJAX Implementation for PHP, contains HTML_AJAX_Response
 *
 * SVN Rev: $Id$ 
 *
 * @category   HTML
 * @package    AJAX
 * @author     Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright  2005-2006 Elizabeth Smith
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.5.6
 */

/**
 * Require the main AJAX library
 */
require_once 'HTML/AJAX.php';

/**
 * Simple base class for a response object to use as an ajax callback
 *
 * This is the base response class, more interesting response classes can be
 * built off of this, simply give it a unique content type and override the
 * getPayload method or fill the payload property with your extended classes's
 * serialized content
 *
 * @version   $Id$
 */
class HTML_AJAX_Response
{

    /**
     * The base response class uses plain text so use that content type
     *
     * @var string
     * @access public
     */
    var $contentType = 'text/plain';

    /**
     * Assign a string to this variable to use the bare response class
     *
     * @var string
     * @access public
     */
    var $payload = '';

    /**
     * Returns the appropriate content type
     *
     * This normally simply returns the contentType property but can be overridden
     * by an extending class if the content-type is variable
     *
     * @return  string   appropriate content type
     * @access public
     */
    function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Returns the serialized content of the response class
     *
     * You can either fill the payload elsewhere in an extending class and leave
     * this method alone, or you can override it if you have a different type
     * of payload that needs special treatment
     *
     * @return  string   serialized response content
     * @access public
     */
    function getPayload()
    {
        return $this->payload;
    }
}
?>
