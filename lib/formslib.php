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
    var $_formname;        // form name
    var $_form;        // quickform object definition
    var $_customdata;  // globals workaround

    function moodleform($action, $customdata=null, $method='post', $target='', $attributes=null) {
        $this->_formname = rtrim(get_class($this), '_form');
        $this->_customdata = $customdata;
        $this->_form =& new MoodleQuickForm($this->_formname, $method, $action, $target, $attributes);

        $this->definition();

        $this->_form->addElement('hidden', 'sesskey', null); // automatic sesskey protection
        $this->_form->setDefault('sesskey', sesskey());
        $this->_form->addElement('hidden', '_qf__'.$this->_formname, null);   // form submission marker
        $this->_form->setDefault('_qf__'.$this->_formname, 1);
        $this->_form->_setDefaultRuleMessages();

        // we have to know all input types before processing submission ;-)
        $this->_process_submission($method);

    }
    /**
     * To autofocus on first form element with error.
     *
     * @return string  javascript to select form element with first error or
     * first element if no errors.
     */
    function focus(){
        $form=$this->_form;
        $elkeys=array_keys($form->_elementIndex);
        if (isset($form->_errors) &&  0!=count($form->_errors)){
            $errorkeys=array_keys($form->_errors);
            $keyinorder=array_intersect($elkeys, $errorkeys);
            $el='getElementById(\'id_'.array_shift($keyinorder).'\')';
            return $el;
        } else{
            $el='getElementById(\'id_'.array_shift($elkeys).'\')';
            return $el;
        }
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
        // the _qf__.$this->_formname serves as a marker that form was actually submitted
        if (array_key_exists('_qf__'.$this->_formname, $submission) and $submission['_qf__'.$this->_formname] == 1) {
            if (!confirm_sesskey()) {
                error('Incorrect sesskey submitted, form not accepted!');
            }
        } else {
            $submission = array();
        }

        $this->_form->updateSubmission($submission);
        $this->definition_after_data();
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
            $moodle_val = $this->validation($this->_form->exportValues(null, true));
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
            unset($data['sesskey']); // we do not need to return sesskey
            unset($data['_qf__'.$this->_formname]);   // we do not need the submission marker too
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

    /**
     * Another abstract function. This one is called after submitted data has
     * been processed and is available. All form setup that is dependent on form values
     * should go in here.
     *
     */
    function definition_after_data(){

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
    function setTypes($paramtypes) {
        $this->_types = $paramtypes + $this->_types;
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
     * Add an array of buttons to the form
     * @param    array       $buttons          An associative array representing help button to attach to
     *                                          to the form. keys of array correspond to names of elements in form.
     *
     * @access   public
    */
    function setHelpButtons($buttons, $suppresscheck=false){

        foreach ($buttons as $elementname => $button){
            $this->setHelpButton($elementname, $button, $suppresscheck);
        }
    }
    /**
     * Add a single button
     *
     * @param string $elementname name of the element to add the item to
     * @param array $button - arguments to pass to setHelpButton
     * @param boolean $suppresscheck - whether to throw an error if the element
     *                                  doesn't exist.
     */
    function setHelpButton($elementname, $button, $suppresscheck=false){
        if (array_key_exists($elementname, $this->_elementIndex)){
            //_elements has a numeric index, this code accesses the elements by name
            $element=&$this->_elements[$this->_elementIndex[$elementname]];
            if (method_exists($element, 'setHelpButton')){
                $element->setHelpButton($button);
            }else{
                $a=new object();
                $a->name=$element->getName();
                $a->classname=get_class($element);
                print_error('nomethodforaddinghelpbutton', 'form', '', $a);
            }
        }elseif (!$suppresscheck){
            print_error('nonexistentformelements', 'form', '', $elementname);
        }
    }

    function exportValues($elementList= null, $addslashes=true){
        $unfiltered=parent::exportValues($elementList);

        if ($addslashes){
            return $this->_recursiveFilter('addslashes',$unfiltered);
        } else {
            return $unfiltered;
        }
    }
    /**
     * Returns the client side validation script
     *
     * The code here was copied from HTML_QuickForm_DHTMLRulesTableless who copied it from  HTML_QuickForm
     * and slightly modified to run rules per-element
     * Needed to override this because of an error with client side validation of grouped elements.
     *
     * @access    public
     * @return    string    Javascript to perform validation, empty string if no 'client' rules were added
     */
    function getValidationScript()
    {
        if (empty($this->_rules) || empty($this->_attributes['onsubmit'])) {
            return '';
        }

        include_once('HTML/QuickForm/RuleRegistry.php');
        $registry =& HTML_QuickForm_RuleRegistry::singleton();
        $test = array();
        $js_escape = array(
            "\r"    => '\r',
            "\n"    => '\n',
            "\t"    => '\t',
            "'"     => "\\'",
            '"'     => '\"',
            '\\'    => '\\\\'
        );

        foreach ($this->_rules as $elementName => $rules) {
            foreach ($rules as $rule) {
                if ('client' == $rule['validation']) {
                    unset($element);

                    $dependent  = isset($rule['dependent']) && is_array($rule['dependent']);
                    $rule['message'] = strtr($rule['message'], $js_escape);

                    if (isset($rule['group'])) {
                        $group    =& $this->getElement($rule['group']);
                        // No JavaScript validation for frozen elements
                        if ($group->isFrozen()) {
                            continue 2;
                        }
                        $elements =& $group->getElements();
                        foreach (array_keys($elements) as $key) {
                            if ($elementName == $group->getElementName($key)) {
                                $element =& $elements[$key];
                                break;
                            }
                        }
                    } elseif ($dependent) {
                        $element   =  array();
                        $element[] =& $this->getElement($elementName);
                        foreach ($rule['dependent'] as $idx => $elName) {
                            $element[] =& $this->getElement($elName);
                        }
                    } else {
                        $element =& $this->getElement($elementName);
                    }
                    // No JavaScript validation for frozen elements
                    if (is_object($element) && $element->isFrozen()) {
                        continue 2;
                    } elseif (is_array($element)) {
                        foreach (array_keys($element) as $key) {
                            if ($element[$key]->isFrozen()) {
                                continue 3;
                            }
                        }
                    }
                    // Fix for bug displaying errors for elements in a group
                    //$test[$elementName][] = $registry->getValidationScript($element, $elementName, $rule);
                    $test[$elementName][0][] = $registry->getValidationScript($element, $elementName, $rule);
                    $test[$elementName][1]=$element;
                    //end of fix
                }
            }
        }
        $js = '
<script type="text/javascript">
//<![CDATA[
function qf_errorHandler(element, _qfMsg) {
  div = element.parentNode;
  if (_qfMsg != \'\') {
    span = document.createElement("span");
    span.className = "error";
    span.appendChild(document.createTextNode(_qfMsg.substring(3)));
    br = document.createElement("br");

    var errorDiv = document.getElementById(element.name + \'_errorDiv\');
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.id = element.name + \'_errorDiv\';
    }
    while (errorDiv.firstChild) {
      errorDiv.removeChild(errorDiv.firstChild);
    }

    errorDiv.insertBefore(br, errorDiv.firstChild);
    errorDiv.insertBefore(span, errorDiv.firstChild);
    element.parentNode.insertBefore(errorDiv, element.parentNode.firstChild);

    if (div.className.substr(div.className.length - 6, 6) != " error"
        && div.className != "error") {
      div.className += " error";
    }

    return false;
  } else {
    var errorDiv = document.getElementById(element.name + \'_errorDiv\');
    if (errorDiv) {
      errorDiv.parentNode.removeChild(errorDiv);
    }

    if (div.className.substr(div.className.length - 6, 6) == " error") {
      div.className = div.className.substr(0, div.className.length - 6);
    } else if (div.className == "error") {
      div.className = "";
    }

    return true;
  }
}';
        $validateJS = '';
        foreach ($test as $elementName => $jsandelement) {
            // Fix for bug displaying errors for elements in a group
            //unset($element);
            list($jsArr,$element)=$jsandelement;
            //end of fix
            $js .= '
function validate_' . $this->_attributes['id'] . '_' . $elementName . '(element) {
  var value = \'\';
  var errFlag = new Array();
  var _qfGroups = {};
  var _qfMsg = \'\';
  var frm = element.parentNode;
  while (frm && frm.nodeName != "FORM") {
    frm = frm.parentNode;
  }
' . join("\n", $jsArr) . '
  return qf_errorHandler(element, _qfMsg);
}
';
            $validateJS .= '
  ret = validate_' . $this->_attributes['id'] . '_' . $elementName.'(frm.elements[\''.$elementName.'\']) && ret;';
            // Fix for bug displaying errors for elements in a group
            //unset($element);
            //$element =& $this->getElement($elementName);
            //end of fix
            $valFunc = 'validate_' . $this->_attributes['id'] . '_' . $elementName . '(this)';
            $onBlur = $element->getAttribute('onBlur');
            $onChange = $element->getAttribute('onChange');
            $element->updateAttributes(array('onBlur' => $onBlur . $valFunc,
                                             'onChange' => $onChange . $valFunc));
        }
        $js .= '
function validate_' . $this->_attributes['id'] . '(frm) {
  var ret = true;
' . $validateJS . ';
  return ret;
}
//]]>
</script>';
        return $js;
    } // end func getValidationScript
    function _setDefaultRuleMessages(){
        foreach ($this->_rules as $field => $rulesarr){
            foreach ($rulesarr as $key => $rule){
                if ($rule['message']===null){
                    $a=new object();
                    $a->format=$rule['format'];
                    $str=get_string('err_'.$rule['type'], 'form', $a);
                    if (strpos($str, '[[')!==0){
                        $this->_rules[$field][$key]['message']=$str;
                    }
                }
            }
        }
    }
    function getLockOptionStartScript(){

        return '';
    }
    function getLockOptionEndScript(){

        return '';
    }

    function addGroupmodeSetting($course) {

        if (! $course = get_record('course', 'id', $course)) {
            error("This course doesn't exist");
        }

        if ($form->coursemodule) {
            if (! $cm = get_record('course_modules', 'id', $form->coursemodule)) {
                error("This course module doesn't exist");
            }
        } else {
            $cm = null;
        }
        $groupmode = groupmode($course, $cm);
        if ($course->groupmode or (!$course->groupmodeforce)) {
            unset($choices);
            $choices[NOGROUPS] = get_string('groupsnone');
            $choices[SEPARATEGROUPS] = get_string('groupsseparate');
            $choices[VISIBLEGROUPS] = get_string('groupsvisible');
            choose_from_menu($choices, 'groupmode', $groupmode, '', '', 0, false, $course->groupmodeforce);

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
    function finishForm(&$form){
        parent::finishForm($form);
        // add a validation script
        if ('' != ($script = $form->getLockOptionStartScript())) {
            $this->_html = $script . "\n" . $this->_html;
        }
        if ('' != ($script = $form->getLockOptionEndScript())) {
            $this->_html = $this->_html . "\n" . $script;
        }
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
MoodleQuickForm::registerElementType('format', "$CFG->libdir/form/format.php", 'MoodleQuickForm_format');
MoodleQuickForm::registerElementType('static', "$CFG->libdir/form/static.php", 'MoodleQuickForm_static');
MoodleQuickForm::registerElementType('hidden', "$CFG->libdir/form/hidden.php", 'MoodleQuickForm_hidden');

?>