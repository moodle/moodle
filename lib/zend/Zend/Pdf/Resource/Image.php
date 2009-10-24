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
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Dictionary */
require_once 'Zend/Pdf/Element/Dictionary.php';

/** Zend_Pdf_Element_Name */
require_once 'Zend/Pdf/Element/Name.php';

/** Zend_Pdf_Resource */
require_once 'Zend/Pdf/Resource.php';


/**
 * Image abstraction.
 *
 * Class is named not in accordance to the name convention.
 * It's "end-user" class, but its ancestor is not.
 * Thus part of the common class name is removed.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Pdf_Resource_Image extends Zend_Pdf_Resource
{
    /**
     * Object constructor.
     */
    public function __construct()
    {
        parent::__construct('');

        $this->_resource->dictionary->Type    = new Zend_Pdf_Element_Name('XObject');
        $this->_resource->dictionary->Subtype = new Zend_Pdf_Element_Name('Image');
    }
    /**
     * get the height in pixels of the image
     *
     * @return integer
     */
    abstract public function getPixelHeight();

    /**
     * get the width in pixels of the image
     *
     * @return integer
     */
    abstract public function getPixelWidth();

    /**
     * gets an associative array of information about an image
     *
     * @return array
     */
    abstract public function getProperties();
}

