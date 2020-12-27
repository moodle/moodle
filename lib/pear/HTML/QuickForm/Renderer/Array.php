<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Alexey Borzov <borz_off@cs.msu.su>                          |
// |          Adam Daniel <adaniel1@eesus.jnj.com>                        |
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// |          Thomas Schulz <ths@4bconsult.de>                            |
// +----------------------------------------------------------------------+
//
// $Id$

require_once 'HTML/QuickForm/Renderer.php';

/**
 * A concrete renderer for HTML_QuickForm, makes an array of form contents
 *
 * Based on old toArray() code.
 *
 * The form array structure is the following:
 * array(
 *   'frozen'           => 'whether the form is frozen',
 *   'javascript'       => 'javascript for client-side validation',
 *   'attributes'       => 'attributes for <form> tag',
 *   'requirednote      => 'note about the required elements',
 *   // if we set the option to collect hidden elements
 *   'hidden'           => 'collected html of all hidden elements',
 *   // if there were some validation errors:
 *   'errors' => array(
 *     '1st element name' => 'Error for the 1st element',
 *     ...
 *     'nth element name' => 'Error for the nth element'
 *   ),
 *   // if there are no headers in the form:
 *   'elements' => array(
 *     element_1,
 *     ...
 *     element_N
 *   )
 *   // if there are headers in the form:
 *   'sections' => array(
 *     array(
 *       'header'   => 'Header text for the first header',
 *       'name'     => 'Header name for the first header',
 *       'elements' => array(
 *          element_1,
 *          ...
 *          element_K1
 *       )
 *     ),
 *     ...
 *     array(
 *       'header'   => 'Header text for the Mth header',
 *       'name'     => 'Header name for the Mth header',
 *       'elements' => array(
 *          element_1,
 *          ...
 *          element_KM
 *       )
 *     )
 *   )
 * );
 *
 * where element_i is an array of the form:
 * array(
 *   'name'      => 'element name',
 *   'value'     => 'element value',
 *   'type'      => 'type of the element',
 *   'frozen'    => 'whether element is frozen',
 *   'label'     => 'label for the element',
 *   'required'  => 'whether element is required',
 *   'error'     => 'error associated with the element',
 *   'style'     => 'some information about element style (e.g. for Smarty)',
 *   // if element is not a group
 *   'html'      => 'HTML for the element'
 *   // if element is a group
 *   'separator' => 'separator for group elements',
 *   'elements'  => array(
 *     element_1,
 *     ...
 *     element_N
 *   )
 * );
 *
 * @access public
 */
class HTML_QuickForm_Renderer_Array extends HTML_QuickForm_Renderer
{
   /**
    * An array being generated
    * @var array
    */
    var $_ary;

   /**
    * Number of sections in the form (i.e. number of headers in it)
    * @var integer
    */
    var $_sectionCount;

   /**
    * Current section number
    * @var integer
    */
    var $_currentSection;

   /**
    * Array representing current group
    * @var array
    */
    var $_currentGroup = null;

   /**
    * Additional style information for different elements
    * @var array
    */
    var $_elementStyles = array();

   /**
    * true: collect all hidden elements into string; false: process them as usual form elements
    * @var bool
    */
    var $_collectHidden = false;

   /**
    * true:  render an array of labels to many labels, $key 0 named 'label', the rest "label_$key"
    * false: leave labels as defined
    * @var bool
    */
    var $staticLabels = false;

   /**
    * Constructor
    *
    * @param  bool    true: collect all hidden elements into string; false: process them as usual form elements
    * @param  bool    true: render an array of labels to many labels, $key 0 to 'label' and the oterh to "label_$key"
    * @access public
    */
    public function __construct($collectHidden = false, $staticLabels = false) {
        parent::__construct();
        $this->_collectHidden = $collectHidden;
        $this->_staticLabels  = $staticLabels;
    } // end constructor

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function HTML_QuickForm_Renderer_Array($collectHidden = false, $staticLabels = false) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($collectHidden, $staticLabels);
    }

   /**
    * Returns the resultant array
    *
    * @access public
    * @return array
    */
    function toArray()
    {
        return $this->_ary;
    }


    function startForm(&$form)
    {
        $this->_ary = array(
            'frozen'            => $form->isFrozen(),
            'javascript'        => $form->getValidationScript(),
            'attributes'        => $form->getAttributes(true),
            'requirednote'      => $form->getRequiredNote(),
            'errors'            => array()
        );
        if ($this->_collectHidden) {
            $this->_ary['hidden'] = '';
        }
        $this->_elementIdx     = 1;
        $this->_currentSection = null;
        $this->_sectionCount   = 0;
    } // end func startForm


    function renderHeader(&$header)
    {
        $this->_ary['sections'][$this->_sectionCount] = array(
            'header' => $header->toHtml(),
            'name'   => $header->getName()
        );
        $this->_currentSection = $this->_sectionCount++;
    } // end func renderHeader


    function renderElement(&$element, $required, $error)
    {
        $elAry = $this->_elementToArray($element, $required, $error);
        if (!empty($error)) {
            $this->_ary['errors'][$elAry['name']] = $error;
        }
        $this->_storeArray($elAry);
    } // end func renderElement


    function renderHidden(&$element)
    {
        if ($this->_collectHidden) {
            $this->_ary['hidden'] .= $element->toHtml() . "\n";
        } else {
            $this->renderElement($element, false, null);
        }
    } // end func renderHidden


    function startGroup(&$group, $required, $error)
    {
        $this->_currentGroup = $this->_elementToArray($group, $required, $error);
        if (!empty($error)) {
            $this->_ary['errors'][$this->_currentGroup['name']] = $error;
        }
    } // end func startGroup


    function finishGroup(&$group)
    {
        $this->_storeArray($this->_currentGroup);
        $this->_currentGroup = null;
    } // end func finishGroup


   /**
    * Creates an array representing an element
    *
    * @access private
    * @param  object    An HTML_QuickForm_element object
    * @param  bool      Whether an element is required
    * @param  string    Error associated with the element
    * @return array
    */
    function _elementToArray(&$element, $required, $error)
    {
        $ret = array(
            'name'      => $element->getName(),
            'value'     => $element->getValue(),
            'type'      => $element->getType(),
            'frozen'    => $element->isFrozen(),
            'required'  => $required,
            'error'     => $error
        );
        // render label(s)
        $labels = $element->getLabel();
        if (is_array($labels) && $this->_staticLabels) {
            foreach($labels as $key => $label) {
                $key = is_int($key)? $key + 1: $key;
                if (1 === $key) {
                    $ret['label'] = $label;
                } else {
                    $ret['label_' . $key] = $label;
                }
            }
        } else {
            $ret['label'] = $labels;
        }

        // set the style for the element
        if (isset($this->_elementStyles[$ret['name']])) {
            $ret['style'] = $this->_elementStyles[$ret['name']];
        }
        if ('group' == $ret['type']) {
            $ret['separator'] = $element->_separator;
            $ret['elements']  = array();
        } else {
            $ret['html']      = $element->toHtml();
        }
        return $ret;
    }


   /**
    * Stores an array representation of an element in the form array
    *
    * @access private
    * @param array  Array representation of an element
    * @return void
    */
    function _storeArray($elAry)
    {
        // where should we put this element...
        if (is_array($this->_currentGroup) && ('group' != $elAry['type'])) {
            $this->_currentGroup['elements'][] = $elAry;
        } elseif (isset($this->_currentSection)) {
            $this->_ary['sections'][$this->_currentSection]['elements'][] = $elAry;
        } else {
            $this->_ary['elements'][] = $elAry;
        }
    }


   /**
    * Sets a style to use for element rendering
    *
    * @param mixed      element name or array ('element name' => 'style name')
    * @param string     style name if $elementName is not an array
    * @access public
    * @return void
    */
    function setElementStyle($elementName, $styleName = null)
    {
        if (is_array($elementName)) {
            $this->_elementStyles = array_merge($this->_elementStyles, $elementName);
        } else {
            $this->_elementStyles[$elementName] = $styleName;
        }
    }
}
?>