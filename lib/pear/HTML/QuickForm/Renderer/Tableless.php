<?php
/**
 * A renderer for HTML_QuickForm that only uses XHTML and CSS but no
 * table tags
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    HTML_QuickForm_Renderer_Tableless
 * @author     Alexey Borzov <borz_off@cs.msu.su>
 * @author     Adam Daniel <adaniel1@eesus.jnj.com>
 * @author     Bertrand Mansion <bmansion@mamasam.com>
 * @author     Mark Wiesemann <wiesemann@php.net>
 * @copyright  2005-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless
 */

require_once 'HTML/QuickForm/Renderer/Default.php';

/**
 * A renderer for HTML_QuickForm that only uses XHTML and CSS but no
 * table tags
 * 
 * You need to specify a stylesheet like the one that you find in
 * data/stylesheet.css to make this work.
 *
 * @category   HTML
 * @package    HTML_QuickForm_Renderer_Tableless
 * @author     Alexey Borzov <borz_off@cs.msu.su>
 * @author     Adam Daniel <adaniel1@eesus.jnj.com>
 * @author     Bertrand Mansion <bmansion@mamasam.com>
 * @author     Mark Wiesemann <wiesemann@php.net>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 0.3.4
 * @link       http://pear.php.net/package/HTML_QuickForm_Renderer_Tableless
 */
class HTML_QuickForm_Renderer_Tableless extends HTML_QuickForm_Renderer_Default
{
   /**
    * Header Template string
    * @var      string
    * @access   private
    */
    var $_headerTemplate = 
        "\n\t\t<legend>{header}</legend>";

   /**
    * Element template string
    * @var      string
    * @access   private
    */
    var $_elementTemplate = 
        "\n\t\t<div class=\"qfrow\"><label class=\"qflabel\"><!-- BEGIN required --><span class=\"required\">*</span><!-- END required -->{label}</label><div class=\"qfelement<!-- BEGIN error --> error<!-- END error -->\"><!-- BEGIN error --><span class=\"error\">{error}</span><br /><!-- END error -->{element}</div></div><br />";

   /**
    * Form template string
    * @var      string
    * @access   private
    */
    var $_formTemplate = 
        "\n<form{attributes}>\n\t<div style=\"display: none;\">{hidden}</div>\n{content}\n</form>";

   /**
    * Template used when opening a fieldset
    * @var      string
    * @access   private
    */
    var $_openFieldsetTemplate = "\n\t<fieldset{id}>";

   /**
    * Template used when opening a hidden fieldset
    * (i.e. a fieldset that is opened when there is no header element)
    * @var      string
    * @access   private
    */
    var $_openHiddenFieldsetTemplate = "\n\t<fieldset class=\"hidden\">";

   /**
    * Template used when closing a fieldset
    * @var      string
    * @access   private
    */
    var $_closeFieldsetTemplate = "\n\t</fieldset>";

   /**
    * Required Note template string
    * @var      string
    * @access   private
    */
    var $_requiredNoteTemplate = 
        "\n\t\t<div class=\"qfreqnote\">{requiredNote}</div>";

   /**
    * How many fieldsets are open
    * @var      integer
    * @access   private
    */
   var $_fieldsetsOpen = 0;

   /**
    * Array of element names that indicate the end of a fieldset
    * (a new one will be opened when a the next header element occurs)
    * @var      array
    * @access   private
    */
    var $_stopFieldsetElements = array();

   /**
    * Constructor
    *
    * @access public
    */
    function HTML_QuickForm_Renderer_Tableless()
    {
        $this->HTML_QuickForm_Renderer_Default();
    } // end constructor

   /**
    * Called when visiting a header element
    *
    * @param    object     An HTML_QuickForm_header element being visited
    * @access   public
    * @return   void
    */
    function renderHeader(&$header)
    {
        $name = $header->getName();
        $id = empty($name) ? '' : ' id="' . $name . '"';
        if (is_null($header->_text)) {
            $header_html = '';
        }
        elseif (!empty($name) && isset($this->_templates[$name])) {
            $header_html = str_replace('{header}', $header->toHtml(), $this->_templates[$name]);
        } else {
            $header_html = str_replace('{header}', $header->toHtml(), $this->_headerTemplate);
        }
        if ($this->_fieldsetsOpen > 0) {
            $this->_html .= $this->_closeFieldsetTemplate;
            $this->_fieldsetsOpen--;
        }
        $openFieldsetTemplate = str_replace('{id}', $id, $this->_openFieldsetTemplate);
        $this->_html .= $openFieldsetTemplate . $header_html;
        $this->_fieldsetsOpen++;
    } // end func renderHeader

   /**
    * Renders an element Html
    * Called when visiting an element
    *
    * @param object     An HTML_QuickForm_element object being visited
    * @param bool       Whether an element is required
    * @param string     An error message associated with an element
    * @access public
    * @return void
    */
    function renderElement(&$element, $required, $error)
    {
        // if the element name indicates the end of a fieldset, close the fieldset
        if (   in_array($element->getName(), $this->_stopFieldsetElements)
            && $this->_fieldsetsOpen > 0
           ) {
            $this->_html .= $this->_closeFieldsetTemplate;
            $this->_fieldsetsOpen--;
        }
        // if no fieldset was opened, we need to open a hidden one here to get
        // XHTML validity
        if ($this->_fieldsetsOpen === 0) {
            $this->_html .= $this->_openHiddenFieldsetTemplate;
            $this->_fieldsetsOpen++;
        }
        if (!$this->_inGroup) {
            $html = $this->_prepareTemplate($element->getName(), $element->getLabel(), $required, $error);
            // the following lines (until the "elseif") were changed / added
            // compared to the default renderer
            $element_html = $element->toHtml();
            if (!is_null($element->getAttribute('id'))) {
                $id = $element->getAttribute('id');
            } else {
                $id = $element->getName();
            }
            if (!empty($id) and !is_a($element, 'MoodleQuickForm_group') and !is_a($element, 'HTML_QuickForm_static')) { // moodle hack
                $html = str_replace('<label', '<label for="' . $id . '"', $html);
                $element_html = preg_replace('#name="' . $id . '#',
                                             'id="' . $id . '" name="' . $id . '',
                                             $element_html,
                                             1);
            }
            $this->_html .= str_replace('{element}', $element_html, $html);
        } elseif (!empty($this->_groupElementTemplate)) {
            $html = str_replace('{label}', $element->getLabel(), $this->_groupElementTemplate);
            if ($required) {
                $html = str_replace('<!-- BEGIN required -->', '', $html);
                $html = str_replace('<!-- END required -->', '', $html);
            } else {
                $html = preg_replace("/([ \t\n\r]*)?<!-- BEGIN required -->(\s|\S)*<!-- END required -->([ \t\n\r]*)?/i", '', $html);
            }
            $this->_groupElements[] = str_replace('{element}', $element->toHtml(), $html);

        } else {
            $this->_groupElements[] = $element->toHtml();
        }
    } // end func renderElement

   /**
    * Called when visiting a form, before processing any form elements
    *
    * @param    object      An HTML_QuickForm object being visited
    * @access   public
    * @return   void
    */
    function startForm(&$form)
    {
        $this->_fieldsetsOpen = 0;
        parent::startForm($form);
    } // end func startForm

   /**
    * Called when visiting a form, after processing all form elements
    * Adds required note, form attributes, validation javascript and form content.
    * 
    * @param    object      An HTML_QuickForm object being visited
    * @access   public
    * @return   void
    */
    function finishForm(&$form)
    {
        // add a required note, if one is needed
        if (!empty($form->_required) && !$form->_freezeAll) {
            $requiredNote = $form->getRequiredNote();
            // replace default required note by DOM/XHTML optimized note
            if ($requiredNote == '<span style="font-size:80%; color:#ff0000;">*</span><span style="font-size:80%;"> denotes required field</span>') {
                $requiredNote = '<span class="required">*</span> denotes required field';
            }
            $this->_html .= str_replace('{requiredNote}', $requiredNote, $this->_requiredNoteTemplate);
        }
        // close the open fieldset
        if ($this->_fieldsetsOpen > 0) {
            $this->_html .= $this->_closeFieldsetTemplate;
            $this->_fieldsetsOpen--;
        }
        // add form attributes and content
        $html = str_replace('{attributes}', $form->getAttributes(true), $this->_formTemplate);
        if (strpos($this->_formTemplate, '{hidden}')) {
            $html = str_replace('{hidden}', $this->_hiddenHtml, $html);
        } else {
            $this->_html .= $this->_hiddenHtml;
        }
        $this->_hiddenHtml = '';
        $this->_html = str_replace('{content}', $this->_html, $html);
        $this->_html = str_replace('></label>', '>&nbsp;</label>', $this->_html);
        // add a validation script
        if ('' != ($script = $form->getValidationScript())) {
            $this->_html = $script . "\n" . $this->_html;
        }
    } // end func finishForm

    /**
     * Sets the template used when opening a fieldset
     *
     * @param       string      The HTML used when opening a fieldset
     * @access      public
     * @return      void
     */
    function setOpenFieldsetTemplate($html)
    {
        $this->_openFieldsetTemplate = $html;
    } // end func setOpenFieldsetTemplate

    /**
     * Sets the template used when opening a hidden fieldset
     * (i.e. a fieldset that is opened when there is no header element)
     *
     * @param       string      The HTML used when opening a hidden fieldset
     * @access      public
     * @return      void
     */
    function setOpenHiddenFieldsetTemplate($html)
    {
        $this->_openHiddenFieldsetTemplate = $html;
    } // end func setOpenHiddenFieldsetTemplate

    /**
     * Sets the template used when closing a fieldset
     *
     * @param       string      The HTML used when closing a fieldset
     * @access      public
     * @return      void
     */
    function setCloseFieldsetTemplate($html)
    {
        $this->_closeFieldsetTemplate = $html;
    } // end func setCloseFieldsetTemplate

    /**
     * Adds one or more element names that indicate the end of a fieldset
     * (a new one will be opened when a the next header element occurs)
     *
     * @param       mixed      Element name(s) (as array or string)
     * @access      public
     * @return      void
     */
    function addStopFieldsetElements($element)
    {
        if (is_array($element)) {
            $this->_stopFieldsetElements = array_merge($this->_stopFieldsetElements,
                                                       $element);
        } else {
            $this->_stopFieldsetElements[] = $element;
        }
    } // end func addStopFieldsetElements

} // end class HTML_QuickForm_Renderer_Default
?>
