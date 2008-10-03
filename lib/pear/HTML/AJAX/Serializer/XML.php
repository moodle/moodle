<?php
// $Id$
/**
 * XML Serializer - does NOT need a js serializer, use responseXML property in XmlHttpRequest
 *
 * @category   HTML
 * @package    AJAX
 * @author     Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright  2005-2006 Elizabeth Smith
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.5.6
 * @link       http://pear.php.net/package/PackageName
 */
class HTML_AJAX_Serializer_XML
{

    /**
     * Serializes a domdocument into an xml string
     *
     * Uses dom or domxml to dump a string from a DomDocument instance
     * remember dom is always the default and this will die horribly without
     * a domdocument instance
     *
     * @access public
     * @param  object $input instanceof DomDocument
     * @return string xml string of DomDocument
     */
    function serialize($input) 
    {
        if(empty($input))
        {
            return $input;
        }
        // we check for the dom extension
        elseif (extension_loaded('Dom'))
        {
            return $input->saveXml();
        }
        // then will check for domxml
        elseif (extension_loaded('Domxml')) 
	{
            return $input->dump_mem();
        }
	// will throw an error
	else {
		$error = new HTML_AJAX_Serializer_Error();	
		$this->serializerNewType = 'Error';
		return $error->serialize(array('errStr'=>"Missing PHP Dom extension direct XML won't work"));
	}
    }

    /**
     * Unserializes the xml string sent from the document
     *
     * Uses dom or domxml to pump a string into a DomDocument instance
     * remember dom is always the default and this will die horribly without
     * one or the other, and will throw warnings if you have bad xml
     *
     * @access public
     * @param  string $input   The input to serialize.
     * @return object instanceofDomDocument
     */
    function unserialize($input) 
    {
        if(empty($input))
        {
            return $input;
        }
        // we check for the dom extension
        elseif (extension_loaded('Dom'))
        {
            $doc = new DOMDocument();
            $doc->loadXML($input);
            return $doc;
        }
        // then we check for the domxml extensions
        elseif (extension_loaded('Domxml'))
	{
            return domxml_open_mem($input);
	}
	// we give up and just return the xml directly
        else
        {
		return $input;
        }
    }
}
?>
