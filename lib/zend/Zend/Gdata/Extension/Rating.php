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
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * Implements the gd:rating element
 * 
 * TODO: Add comments for these methods
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Extension_Rating extends Zend_Gdata_Extension
{

    protected $_rootElement = 'rating';
    protected $_min = null;
    protected $_max = null;
    protected $_numRaters = null;
    protected $_average = null;

    public function __construct($average = null, $min = null, 
            $max = null, $numRaters = null)
    {
        parent::__construct();
        $this->_average = $average;
        $this->_min = $min; 
        $this->_max = $max;
        $this->_numRaters = $numRaters;
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_min != null) {
            $element->setAttribute('min', $this->_min);
        }
        if ($this->_max != null) {
            $element->setAttribute('max', $this->_max);
        }
        if ($this->_numRaters != null) {
            $element->setAttribute('numRaters', $this->_numRaters);
        }
        if ($this->_average != null) {
            $element->setAttribute('average', $this->_average);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
            case 'min':
                $this->_min = $attribute->nodeValue;
                break;
            case 'max':
                $this->_max = $attribute->nodeValue;
                break;
            case 'numRaters':
                $this->_numRaters = $attribute->nodeValue;
                break;
            case 'average':
                $this->_average = $attribute->nodeValue;
                break;
            default:
                parent::takeAttributeFromDOM($attribute);
        }
    }

    public function __toString() 
    {   
        return $this->_average;
    }

    public function getMin()
    {
        return $this->_min;
    }

    public function setMin($value)
    {
        $this->_min = $value;
        return $this;
    }

    public function getNumRaters()
    {
        return $this->_numRaters;
    }

    public function setNumRaters($value)
    {
        $this->_numRaters = $value;
        return $this;
    }
    public function getAverage()
    {
        return $this->_average;
    }

    public function setAverage($value)
    {
        $this->_average = $value;
        return $this;
    }

    public function getMax()
    {
        return $this->_max;
    }

    public function setMax($value)
    {
        $this->_max = $value;
        return $this;
    }

}
