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

class moodleform extends HTML_QuickForm_DHTMLRulesTableless{
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
    function moodleform($formName='', $method='post', $action='', $target='', $attributes=null){
        global $CFG;
        //we need to override the constructor, we don't call the parent constructor
        //at all because it strips slashes depending on the magic quotes setting
        //whereas Moodle already has added slashes whether magic quotes is on or not.
        
        //also added check for sesskey and added sesskey to all forms
        //and told class to ask Moodle for the max upload file size       
        HTML_Common::HTML_Common($attributes);
        $method = (strtoupper($method) == 'GET') ? 'get' : 'post';
        $action = ($action == '') ? $_SERVER['PHP_SELF'] : $action;
        $target = empty($target) ? array() : array('target' => $target);
        //no 'name' atttribute for form in xhtml strict :
        $attributes = array('action'=>$action, 'method'=>$method, 'id'=>$formName) + $target;
        $this->updateAttributes($attributes);
        //check for sesskey for this form 
        //if it is not present then we don't accept any input
        if (isset($_REQUEST['_qf__' . $formName])) {
            $this->_submitValues = $this->_recursiveFilter('stripslashes', 'get' == $method? $_GET: $_POST);
            foreach ($_FILES as $keyFirst => $valFirst) {
                foreach ($valFirst as $keySecond => $valSecond) {
                    if ('name' == $keySecond) {
                        $this->_submitFiles[$keyFirst][$keySecond] = $this->_recursiveFilter('stripslashes', $valSecond);
                    } else {
                        $this->_submitFiles[$keyFirst][$keySecond] = $valSecond;
                    }
                }
            }

            $this->_flagSubmitted = count($this->_submitValues) > 0 || count($this->_submitFiles) > 0;
        }
        
        //check sesskey
        if ($this->_flagSubmitted){
            confirm_sesskey($this->_submitValues['_qf__' . $formName]);
        }
        unset($this->_submitValues['_qf__' . $formName]);
        //add sesskey to all forms
        $this->addElement('hidden', '_qf__' . $formName, sesskey());
        
        if (preg_match('/^([0-9]+)([a-zA-Z]*)$/', get_max_upload_file_size(), $matches)) {
            // see http://www.php.net/manual/en/faq.using.php#faq.using.shorthandbytes
            switch (strtoupper($matches['2'])) {
                case 'G':
                    $this->_maxFileSize = $matches['1'] * 1073741824;
                    break;
                case 'M':
                    $this->_maxFileSize = $matches['1'] * 1048576;
                    break;
                case 'K':
                    $this->_maxFileSize = $matches['1'] * 1024;
                    break;
                default:
                    $this->_maxFileSize = $matches['1'];
            }
        }
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
    function getReqHTML(){
        return $this->_reqHTML;
    }
    /**
     * Class constructor - same parameters as HTML_QuickForm_DHTMLRulesTableless
     * @param    array       $buttons          An associative array representing help button to attach to 
     *                                          to the form. keys of array correspond to names of elements in form.
     * 
     * @access   public
    */
    function addHelpButtons($buttons, $suppresscheck=false){
        
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
    /**
     * Applies a data filter for the given field(s)
     * We can use any PARAM_ 
     *
     * @param    mixed     $element       Form element name or array of such names
     * @param    mixed     $filter        Callback, either function name or array(&$object, 'method') or PARAM_ integer
     * @since    2.0
     * @access   public
     */
    function applyFilter($element, $filter){
        if (is_numeric($filter)){
            $filter=create_function('$value', "clean_param(\$value, $filter);");
        }
        parent::applyFilter($element, $filter);
    }
    function exportValue($element, $addslashes=true){
        $unfiltered=parent::exportValue($element);
        if ($addslashes){
            return HTML_QuickForm::_recursiveFilter('addslashes',$unfiltered);
        } else {
            return $unfiltered;
        }
    }
    function exportValues($elementList, $addslashes=true){
        $unfiltered=parent::exportValues($elementList);
        if ($addslashes){
            return HTML_QuickForm::_recursiveFilter('addslashes',$unfiltered);
        } else {
            return $unfiltered;
        }
    }
}

/**
 * A renderer for moodleform that only uses XHTML and CSS and no
 * table tags, extends PEAR class HTML_QuickForm_Renderer_Tableless
 * 
 * Stylesheet is part of standard theme and should be automatically included.
 *
 * @author      Jamie Pratt <me@jamiep.org>
 * @license    gpl license
 */
class moodleform_renderer extends HTML_QuickForm_Renderer_Tableless{

    /**
    * Element template array
    * @var      array
    * @access   private
    */
    var $_elementTemplates;
    var $_htmleditors=array();
    function moodleform_renderer(){
        $this->_elementTemplates=array('default'=>"\n\t\t<div class=\"qfrow\"><label class=\"qflabel\">{label}{help}<!-- BEGIN required -->{req}<!-- END required --></label><div class=\"qfelement<!-- BEGIN error --> error<!-- END error --> {type}\"><!-- BEGIN error --><span class=\"error\">{error}</span><br /><!-- END error -->{element}</div></div>",
        'wide'=>"\n\t\t<div class=\"qfrow\"><label class=\"qflabel\">{label}{help}<!-- BEGIN required -->{req}<!-- END required --></label><br /><div class=\"qfelementwide<!-- BEGIN error --> error<!-- END error --> {type}\"><!-- BEGIN error --><span class=\"error\">{error}</span><br /><!-- END error -->{element}</span></div>");

        parent::HTML_QuickForm_Renderer_Tableless();
    }
    function startForm(&$form){
        $this->_reqHTML=$form->getReqHTML();
        $this->_elementTemplates=str_replace('{req}', $this->_reqHTML, $this->_elementTemplates);
        parent::startForm($form);
    }
    function startGroup(&$group, $required, $error){
        if (method_exists($group, 'getElementTemplateType')){
            $html = $this->_elementTemplates[$element->getElementTemplateType()];
        }else{
            $html = $this->_elementTemplates['default'];
           
        }
        if (method_exists($group, 'getHelpButton')){
            $html =str_replace('{help}', $group->getHelpButton(), $html);
        }else{
            $html =str_replace('{help}', '', $html);
            
        }
        $html =str_replace('{type}', 'group', $html);
        
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
        $html =str_replace('{type}', $element->getType(), $html);
        if (method_exists($element, 'getHelpButton')){
            $html=str_replace('{help}', $element->getHelpButton(), $html);
        }else{
            $html=str_replace('{help}', '', $html);
            
        }
        $this->_templates[$element->getName()]=$html;
        
        parent::renderElement($element, $required, $error);
    }

    
}

class moodleform_filter{
    var $paramtype;
    var $default;
    function moodleform_filter($paramtype, $default){
        $this->paramtype=$paramtype;
        $this->default=$default;
    }
    function required_param($value){
        if (isset($value)) {
            $param = $value;
        } else {
            error('A required parameter was missing');
        }
    
        return $this->clean_param($param);
    }
    function optional_param($value){
        if (!isset($value)) {
           return $this->default;
        }
        return $this->clean_param($value);
    }
    function clean_param($value){
        //clean param expects vars with slashes
        $value=HTML_QuickForm::_recursiveFilter('addslashes', $value);
        $value=clean_param($value, $this->paramtype);
        return HTML_QuickForm::_recursiveFilter('stripslashes', $value);
    }
}    

$GLOBALS['_HTML_QuickForm_default_renderer']=& new moodleform_renderer();

moodleform::registerElementType('checkbox', "$CFG->libdir/form/checkbox.php", 'moodleform_checkbox');
moodleform::registerElementType('file', "$CFG->libdir/form/file.php", 'moodleform_file');
moodleform::registerElementType('group', "$CFG->libdir/form/group.php", 'moodleform_group');
moodleform::registerElementType('password', "$CFG->libdir/form/password.php", 'moodleform_password');
moodleform::registerElementType('radio', "$CFG->libdir/form/radio.php", 'moodleform_radio');
moodleform::registerElementType('select', "$CFG->libdir/form/select.php", 'moodleform_select');
moodleform::registerElementType('text', "$CFG->libdir/form/text.php", 'moodleform_text');
moodleform::registerElementType('textarea', "$CFG->libdir/form/textarea.php", 'moodleform_textarea');
moodleform::registerElementType('date_selector', "$CFG->libdir/form/dateselector.php", 'moodleform_date_selector');
moodleform::registerElementType('htmleditor', "$CFG->libdir/form/htmleditor.php", 'moodleform_htmleditor');
moodleform::registerElementType('static', "$CFG->libdir/form/static.php", 'moodleform_static');


?>