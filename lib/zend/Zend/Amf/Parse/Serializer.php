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
 * @package    Zend_Amf
 * @subpackage Parse
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Base abstract class for all AMF serializers.
 *
 * @package    Zend_Amf
 * @subpackage Parse
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Amf_Parse_Serializer
{
    /**
     * Refrence to the current output stream being constructed
     *
     * @var string
     */
    protected $_stream;

    /**
     * Constructor
     * 
     * @param  Zend_Amf_Parse_OutputStream $stream 
     * @return void
     */
    public function __construct(Zend_Amf_Parse_OutputStream $stream)
    {
        $this->_stream = $stream;
    }

    /**
     * Find the PHP object type and convert it into an AMF object type
     *
     * @param  mixed $content
     * @param  int $markerType
     * @return void
     */
    public abstract function writeTypeMarker($content, $markerType=null);
}
