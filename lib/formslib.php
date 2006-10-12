<?php
/**
 * formslib.php - library of classes for creating forms in Moodle, based on PEAR QuickForms.
 * THIS IS NOT YET PART OF THE MOODLE API, IT IS HERE FOR TESTING ONLY
 * @author  Jamie Pratt
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

//point pear include path to moodles lib/pear so that includes and requires will search there for files before anywhere else.
if (FALSE===strstr(ini_get('include_path'), $CFG->libdir.'/pear' )){
    ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));
}
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/DHTMLRulesTableless.php';
require_once 'HTML/QuickForm/Renderer/Tableless.php';

if ($CFG->debug >= DEBUG_ALL){
    PEAR::setErrorHandling(PEAR_ERROR_PRINT);
}

class moodleform {
    var $_form;        // quickform object definition
    var $_customdata;  // globals workaround

    function moodleform($action, $customdata=null, $method='post', $target='', $attributes=null) {
        $form_name = rtrim(get_class($this), '_form');
        $this->_customdata = $customdata;
        $this->_form =& new MoodleQuickForm($form_name, $method, $action, $target, $attributes);

        $this->definition();

        $this->_form->addElement('hidden', 'sesskey', null); // automatic sesskey protection
        $this->_form->setDefault('sesskey', sesskey());
        $this->_form->addElement('hidden', '_qf__', null);   // form submission marker
        $this->_form->setDefault('_qf__', 1);

        // we have to know all input types before processing submission ;-)
        $this->_process_submission($method);

    }

    function _process_submission($method) {
        $submission = array();
        if ($method == 'post') {
            if (!empty($_POST)) {
                $submission = $_POST;
            }
        } else {
            $submission = array_merge_recursive($_GET, $_POST); // emulate handling of parameters in xxxx_param()
        }

        // following trick is needed to enable proper sesskey checks when using GET forms
        // the _qf__ serves as a marker that form was actually submitted
        if (array_key_exists('_qf__', $submission) and $submission['_qf__'] == 1) {
            if (!confirm_sesskey()) {
                error('Incorrect sesskey submitted, form not accepted!');
            }
        } else {
            $submission = array();
        }

        $this->_form->updateSubmission($submission);
    }

    function set_defaults($default_values, $slashed=false) {
        if (is_object($default_values)) {
            $default_values = (array)$default_values;
        }
        $filter = $slashed ? 'stripslashes' : NULL;
        $this->_form->setDefaults($default_values, $filter);
    }

    function is_submitted() {
        return $this->_form->isSubmitted();
    }

    function is_validated() {
        static $validated = null;

        if ($validated === null) {
            $internal_val = $this->_form->validate();
            $moodle_val = $this->validation($this->_form->exportValues(null, false));
            if ($moodle_val !== true) {
                if (!empty($moodle_val)) {
                    foreach ($moodle_val as $element=>$msg) {
                        $this->_form->setElementError($element, $msg);
                    }
                }
                $moodle_val = false;
            }
            $validated = ($internal_val and $moodle_val);
        }
        return $validated;
    }

    function data_submitted($slashed=true) {
        if ($this->is_submitted() and $this->is_validated()) {
            $data = $this->_form->exportValues(null, $slashed);
            if (empty($data)) {
                return NULL;
            } else {
                return (object)$data;
            }
        } else {
            return NULL;
        }
    }

    function display() {
        $this->_form->display();
    }

    // abstract method - always override
    function definition() {
        error('Abstract form_definition() method in class '.get_class($this).' must be overriden, please fix the code.');
    }

    // dummy stub method - override if needed
    function validation($data) {
        // return array of errors ("fieldname"=>"error message") or true if ok
        return true;
    }

}

class MoodleQuickForm extends HTML_QuickForm_DHTMLRulesTableless {
    var $_types = array();


    /**
     * Class constructor - same parameters as HTML_QuickForm_DHTMLRulesTableless
     * @param    string      $formName          Form's name.
     * @param    string      $method            (optional)Form's method defaults to 'POST'
     * @param    string      $action            (optional)Form's action
     * @param    string      $target            (optional)Form's target defaults to none
     * @param    mixed       $attributes        (optional)Extra attributes for <form> tag
     * @param    bool        $trackSubmit       (optional)Whether to track if the form was submitted by adding a special hidden field
     * @access   public
     */
    function MoodleQuickForm($formName, $method, $action, $target='', $attributes=null){
        global $CFG;

        HTML_Common::HTML_Common($attributes);
        $target = empty($target) ? array() : array('target' => $target);
        //no 'name' atttribute for form in xhtml strict :
        $attributes = array('action'=>$action, 'method'=>$method, 'id'=>$formName) + $target;
        $this->updateAttributes($attributes);

        //this is custom stuff for Moodle :
        $oldclass=   $this->getAttribute('class');
        if (!empty($oldclass)){
            $this->updateAttributes(array('class'=>$oldclass.' mform'));
        }else {
            $this->updateAttributes(array('class'=>'mform'));
        }
        $this->_helpImageURL="$CFG->wwwroot/lib/form/req.gif";
        $this->_reqHTML =
            helpbutton('requiredelement', get_string('requiredelement', 'form'),'moodle',
                 true, false, '', true, '<img alt="'.get_string('requiredelement', 'form').'" src="'.
                    $this->_helpImageURL.'" />');
        $this->setRequiredNote(get_string('denotesreq', 'form', $this->getReqHTML()));
    }

    function setType($elementname, $paramtype) {
        $this->_types[$elementname] = $paramtype;
    }

    function updateSubmission($submission) {
        if (empty($submission)) {
            $this->_submitValues = array();
            $this->_flagSubmitted = false;
        } else {
            foreach ($submission as $key=>$s) {
                if (array_key_exists($key, $this->_types)) {
                    $submission[$key] = clean_param($s, $this->_types[$key]);
                }
            }
            $this->_submitValues = $this->_recursiveFilter('stripslashes', $submission);
            $this->_flagSubmitted = true;
        }

        // need to tell all elements that they need to update their value attribute. 
         foreach (array_keys($this->_elements) as $key) { 
             $this->_elements[$key]->onQuickFormEvent('updateValue', null, $this); 
         } 
    }

    function getReqHTML(){
        return $this->_reqHTML;
    }

    /**
     * Initializes a default form value
     *
     * @param     string   $elementname        element name
     * @param     mixed    $values             values for that element name
     * @param     bool     $slashed            the default value is slashed
     * @access    public
     * @return    void
     */
    function setDefault($elementName, $defaultValue, $slashed=false){
        $filter = $slashed ? 'stripslashes' : NULL;
        $this->setDefaults(array($elementName=>$defaultValue), $filter);
    } // end func setDefault
    /**
     * Class constructor - same parameters as HTML_QuickForm_DHTMLRulesTableless
     * @param    array       $buttons          An associative array representing help button to attach to
     *                                          to the form. keys of array correspond to names of elements in form.
     *
     * @access   public
    */

    function setHelpButtons($buttons, $suppresscheck=false){

        foreach ($this->_elements as $no => $element){
            if (array_key_exists($element->getName(), $buttons)){

                if (method_exists($element, 'setHelpButton')){
                    $this->_elements[$no]->setHelpButton($buttons[$element->getName()]);
                }else{
                    $a=new object();
                    $a->name=$element->getName();
                    $a->classname=get_class($element);
                    print_error('nomethodforaddinghelpbutton', 'form', '', $a);
                }
                unset($buttons[$element->getName()]);
            }

        }
        if (count($buttons)&& !$suppresscheck){
            print_error('nonexistentformelements', 'form', '', join(', ', array_keys($buttons)));
        }
    }

    function exportValues($elementList= null, $addslashes=true){
        $unfiltered=parent::exportValues($elementList);
        unset($unfiltered['sesskey']); // we do not need to return sesskey
        unset($unfiltered['_qf__']);   // we do not need the submission marker too

        if ($addslashes){
            return $this->_recursiveFilter('addslashes',$unfiltered);
        } else {
            return $unfiltered;
        }
    }
}

/**
 * A renderer for MoodleQuickForm that only uses XHTML and CSS and no
 * table tags, extends PEAR class HTML_QuickForm_Renderer_Tableless
 *
 * Stylesheet is part of standard theme and should be automatically included.
 *
 * @author      Jamie Pratt <me@jamiep.org>
 * @license    gpl license
 */
class MoodleQuickForm_Renderer extends HTML_QuickForm_Renderer_Tableless{

    /**
    * Element template array
    * @var      array
    * @access   private
    */
    var $_elementTemplates;

//   uncomment templates below and edit formslib.php for
//   ol li containers for form items.

    /**
    * Template used when opening a hidden fieldset
    * (i.e. a fieldset that is opened when there is no header element)
    * @var      string
    * @access   private
    */
    var $_openHiddenFieldsetTemplate = "\n\t<fieldset class=\"hidden\">";
//    var $_openHiddenFieldsetTemplate = "\n\t<fieldset class=\"hidden\">\n\t\t<ol>";
//   /**
//    * Header Template string
//    * @var      string
//    * @access   private
//    */
//    var $_headerTemplate =
//        "\n\t\t<legend>{header}</legend>\n\t\t<ol>";
//    var $_headerTemplate =
//        "\n\t\t<legend>{header}</legend>\n\t\t<ol>";

   /**
    * Template used when closing a fieldset
    * @var      string
    * @access   private
    */
    var $_closeFieldsetTemplate = "\n\t\t</fieldset>";
//    var $_closeFieldsetTemplate = "\n\t\t</ol>\n\t</fieldset>";

   /**
    * Required Note template string
    * @var      string
    * @access   private
    */
    var $_requiredNoteTemplate =
        "\n\t\t<div class=\"fdescription\">{requiredNote}</div>";

    function MoodleQuickForm_Renderer(){
        // switch next two lines for ol li containers for form items.
        //        $this->_elementTemplates=array('default'=>"\n\t\t<li class=\"fitem\"><label>{label}{help}<!-- BEGIN required -->{req}<!-- END required --></label><div class=\"qfelement<!-- BEGIN error --> error<!-- END error --> {type}\"><!-- BEGIN error --><span class=\"error\">{error}</span><br /><!-- END error -->{element}</div></li>");
        $this->_elementTemplates=array('default'=>"\n\t\t<div class=\"fitem\"><label>{label}{help}<!-- BEGIN required -->{req}<!-- END required --></label><div class=\"felement<!-- BEGIN error --> error<!-- END error --> {type}\"><!-- BEGIN error --><span class=\"error\">{error}</span><br /><!-- END error -->{element}</div></div>",
        'fieldset'=>"\n\t\t<div class=\"fitem\"><label>{label}{help}<!-- BEGIN required -->{req}<!-- END required --></label><fieldset class=\"felement<!-- BEGIN error --> error<!-- END error --> {type}\"><!-- BEGIN error --><span class=\"error\">{error}</span><br /><!-- END error -->{element}</fieldset></div>");

        parent::HTML_QuickForm_Renderer_Tableless();
    }

    function startForm(&$form){
        $this->_reqHTML=$form->getReqHTML();
        $this->_elementTemplates=str_replace('{req}', $this->_reqHTML, $this->_elementTemplates);
        parent::startForm($form);
    }

    function startGroup(&$group, $required, $error){
        if (method_exists($group, 'getElementTemplateType')){
            $html = $this->_elementTemplates[$group->getElementTemplateType()];
        }else{
            $html = $this->_elementTemplates['default'];

        }
        if (method_exists($group, 'getHelpButton')){
            $html =str_replace('{help}', $group->getHelpButton(), $html);
        }else{
            $html =str_replace('{help}', '', $html);

        }
        $html =str_replace('{type}', 'fgroup', $html);

        $this->_templates[$group->getName()]=$html;
        // Fix for bug in tableless quickforms that didn't allow you to stop a
        // fieldset before a group of elements.
        // if the element name indicates the end of a fieldset, close the fieldset
        if (   in_array($group->getName(), $this->_stopFieldsetElements)
            && $this->_fieldsetsOpen > 0
           ) {
            $this->_html .= $this->_closeFieldsetTemplate;
            $this->_fieldsetsOpen--;
        }
        parent::startGroup($group, $required, $error);
    }

    function renderElement(&$element, $required, $error){
        if (method_exists($element, 'getElementTemplateType')){
            $html = $this->_elementTemplates[$element->getElementTemplateType()];
        }else{
            $html = $this->_elementTemplates['default'];

        }
        $html =str_replace('{type}', 'f'.$element->getType(), $html);
        if (method_exists($element, 'getHelpButton')){
            $html=str_replace('{help}', $element->getHelpButton(), $html);
        }else{
            $html=str_replace('{help}', '', $html);

        }
        $this->_templates[$element->getName()]=$html;
        if (!is_null($element->getAttribute('id'))) {
            $id = $element->getAttribute('id');
        } else {
            $id = $element->getName();
        }
        $element->updateAttributes(array('id'=>'id_'.$id));
        parent::renderElement($element, $required, $error);
    }
}


$GLOBALS['_HTML_QuickForm_default_renderer']=& new MoodleQuickForm_Renderer();

MoodleQuickForm::registerElementType('checkbox', "$CFG->libdir/form/checkbox.php", 'MoodleQuickForm_checkbox');
MoodleQuickForm::registerElementType('file', "$CFG->libdir/form/file.php", 'MoodleQuickForm_file');
MoodleQuickForm::registerElementType('group', "$CFG->libdir/form/group.php", 'MoodleQuickForm_group');
MoodleQuickForm::registerElementType('password', "$CFG->libdir/form/password.php", 'MoodleQuickForm_password');
MoodleQuickForm::registerElementType('radio', "$CFG->libdir/form/radio.php", 'MoodleQuickForm_radio');
MoodleQuickForm::registerElementType('select', "$CFG->libdir/form/select.php", 'MoodleQuickForm_select');
MoodleQuickForm::registerElementType('text', "$CFG->libdir/form/text.php", 'MoodleQuickForm_text');
MoodleQuickForm::registerElementType('textarea', "$CFG->libdir/form/textarea.php", 'MoodleQuickForm_textarea');
MoodleQuickForm::registerElementType('date_selector', "$CFG->libdir/form/dateselector.php", 'MoodleQuickForm_date_selector');
MoodleQuickForm::registerElementType('date_time_selector', "$CFG->libdir/form/datetimeselector.php", 'MoodleQuickForm_date_time_selector');
MoodleQuickForm::registerElementType('htmleditor', "$CFG->libdir/form/htmleditor.php", 'MoodleQuickForm_htmleditor');
MoodleQuickForm::registerElementType('static', "$CFG->libdir/form/static.php", 'MoodleQuickForm_static');
MoodleQuickForm::registerElementType('hidden', "$CFG->libdir/form/hidden.php", 'MoodleQuickForm_hidden');

?>