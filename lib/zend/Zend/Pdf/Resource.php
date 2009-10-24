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


/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf_Element_Object */
require_once 'Zend/Pdf/Element/Object.php';

/** Zend_Pdf_Element_Dictionary */
require_once 'Zend/Pdf/Element/Dictionary.php';


/**
 * PDF file Resource abstraction
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Pdf_Resource
{
    /**
     * Each Pdf resource (fonts, images, ...) interacts with a PDF itself.
     * It creates appropriate PDF objects, structures and sometime embedded files.
     * Resources are referenced in content streams by names, which are stored in
     * a page resource dictionaries.
     *
     * Thus, resources must be attached to the PDF.
     *
     * Resource abstraction uses own PDF object factory to store all necessary information.
     * At the render time internal object factory is appended to the global PDF file
     * factory.
     *
     * Resource abstraction also cashes information about rendered PDF files and
     * doesn't duplicate resource description each time then Resource is rendered
     * (referenced).
     *
     * @var Zend_Pdf_ElementFactory_Interface
     */
    protected $_objectFactory;

    /**
     * Main resource object
     *
     * @var Zend_Pdf_Element_Object
     */
    protected $_resource;

    /**
     * Object constructor.
     *
     * If resource is not a Zend_Pdf_Element object, then stream object with specified value is
     * generated.
     *
     * @param Zend_Pdf_Element|string $resource
     */
    public function __construct($resource)
    {
        $this->_objectFactory     = Zend_Pdf_ElementFactory::createFactory(1);
        if ($resource instanceof Zend_Pdf_Element) {
            $this->_resource      = $this->_objectFactory->newObject($resource);
        } else {
            $this->_resource      = $this->_objectFactory->newStreamObject($resource);
        }
    }

    /**
     * Get resource.
     * Used to reference resource in an internal PDF data structures (resource dictionaries)
     *
     * @internal
     * @return Zend_Pdf_Element_Object
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Get factory.
     *
     * @internal
     * @return Zend_Pdf_ElementFactory_Interface
     */
    public function getFactory()
    {
        return $this->_objectFactory;
    }
}

