<?php // $Id$
/**
 * formslib.php - library of classes for creating forms in Moodle, based on PEAR QuickForms.
 *
 * To use formslib then you will want to create a new file purpose_form.php eg. edit_form.php
 * and you want to name your class something like {modulename}_{purpose}_form. Your class will
 * extend moodleform overriding abstract classes definition and optionally defintion_after_data
 * and validation.
 *
 * See examples of use of this library in course/edit.php and course/edit_form.php
 *
 * A few notes :
 *      form defintion is used for both printing of form and processing and should be the same
 *              for both or you may lose some submitted data which won't be let through.
 *      you should be using setType for every form element except select, radio or checkbox
 *              elements, these elements clean themselves.
 *
 *
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

require_once $CFG->libdir.'/uploadlib.php';

if ($CFG->debug >= DEBUG_ALL){
    PEAR::setErrorHandling(PEAR_ERROR_PRINT);
}

/**
 * Moodle specific wrapper that separates quickforms syntax from moodle code. You won't directly
 * use this class you should write a class defintion which extends this class or a more specific
 * subclass such a moodleform_mod for each form you want to display and/or process with formslib.
 *
 * You will write your own definition() method which performs the form set up.
 */
class moodleform {
    var $_formname;       // form name
    var $_form;           // quickform object definition
    var $_customdata;     // globals workaround
    var $_upload_manager; // file upload manager

    /**
     * The constructor function calls the abstract function definition() and it will then
     * process and clean and attempt to validate incoming data.
     *
     * It will call your custom validate method to validate data and will also check any rules
     * you have specified in definition using addRule
     *
     * The name of the form (id attribute of the form) is automatically generated depending on
     * the name you gave the class extending moodleform. You should call your class something
     * like
     *
     * @param string $action the action attribute for the form.
     * @param array $customdata if your form defintion method needs access to data such as $course
     *               $cm, etc. to construct the form definition then pass it in this array. You can
     *               use globals for somethings.
     * @param string $method if you set this to anything other than 'post' then _GET and _POST will
     *               be merged and used as incoming data to the form.
     * @param string $target target frame for form submission. You will rarely use this. Don't use
     *                  it if you don't need to as the target attribute is deprecated in xhtml
     *                  strict.
     * @param mixed $attributes you can pass a string of html attributes here or an array.
     * @return moodleform
     */
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

        // update form definition based on final data
        $this->definition_after_data();
    }

    /**
     * To autofocus on first form element or first element with error.
     *
     * @return string  javascript to select form element with first error or
     *                  first element if no errors. Use this as a parameter
     *                  when calling print_header
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

    /**
     * Internal method. Alters submitted data to be suitable for quickforms processing.
     * Must be called when the form is fully set up.
     */
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
            $files = $_FILES;
        } else {
            $submission = array();
            $files = array();
        }

        $this->_form->updateSubmission($submission, $files);
    }

    /**
     * Internal method. Validates all uploaded files.
     */
    function _validate_files() {
        if (empty($_FILES)) {
            // we do not need to do any checks because no files were submitted
            // TODO: find out why server side required rule does not work for uploaded files;
            //       testing is easily done by always returning true from this function and adding
            //       $mform->addRule('soubor', get_string('required'), 'required', null, 'server');
            //       and submitting form without selected file
            return true;
        }
        $errors = array();
        $mform =& $this->_form;

        // create default upload manager if not already created
        if (empty($this->_upload_manager)) {
            $this->_upload_manager = new upload_manager();
        }

        // check the files
        $status = $this->_upload_manager->preprocess_files();

        // now check that we really want each file
        foreach ($_FILES as $elname=>$file) {
            if ($mform->elementExists($elname) and $mform->getElementType($elname)=='file') {
                $required = $mform->isElementRequired($elname);
                if (!empty($this->_upload_manager->files[$elname]['uploadlog']) and empty($this->_upload_manager->files[$elname]['clear'])) {
                    if (!$required and $file['error'] == UPLOAD_ERR_NO_FILE) {
                        // file not uploaded and not required - ignore it
                        continue;
                    }
                    $errors[$elname] = $this->_upload_manager->files[$elname]['uploadlog'];
                }
            } else {
                error('Incorrect upload attemp!');
            }
        }

        // return errors if found
        if ($status and 0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }

    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param mixed $default_values object or array of default values
     * @param bool $slased true if magic quotes applied to data values
     */
    function set_defaults($default_values, $slashed=false) {
        if (is_object($default_values)) {
            $default_values = (array)$default_values;
        }
        $filter = $slashed ? 'stripslashes' : NULL;
        $this->_form->setDefaults($default_values, $filter);
        //update form definition when data changed
        $this->definition_after_data();
    }

    /**
     * Check that form was submitted. Does not check validity of submitted data.
     *
     * @return bool true if form properly submitted
     */
    function is_submitted() {
        return $this->_form->isSubmitted();
    }

    /**
     * Check that form data is valid.
     *
     * @return bool true if form data valid
     */
    function is_validated() {
        static $validated = null; // one validation is enough

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
            $file_val = $this->_validate_files();
            if ($file_val !== true) {
                if (!empty($file_val)) {
                    foreach ($file_val as $element=>$msg) {
                        $this->_form->setElementError($element, $msg);
                    }
                }
                $file_val = false;
            }
            $validated = ($internal_val and $moodle_val and $file_val);
        }
        return $validated;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @param bool $slashed true means return data with addslashes applied
     * @return object submitted data; NULL if not valid or not submitted
     */
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

    /**
     * Save verified uploaded files into directory. Upload process can be customised from definition()
     * method by creating instance of upload manager and storing it in $this->_upload_form
     *
     * @param string $destination where to store uploaded files
     * @return bool success
     */
    function save_files($destination) {
        if (empty($this->_upload_manager)) {
            return false;
        }
        if ($this->is_submitted() and $this->is_validated()) {
            return $this->_upload_manager->save_files($destination);
        }
        return false;
    }

    /**
     * Print html form.
     */
    function display() {
        $this->_form->display();
    }

    /**
     * Abstract method - always override!
     *
     * If you need special handling of uploaded files, create instance of $this->_upload_manager here.
     */
    function definition() {
        error('Abstract form_definition() method in class '.get_class($this).' must be overriden, please fix the code.');
    }

    /**
     * Dummy stub method - override if you need to setup the form depending on current
     * values. This method is called after definition(), data submission and set_defaults().
     * All form setup that is dependent on form values should go in here.
     */
    function definition_after_data(){
    }

    /**
     * Dummy stub method - override if you needed to perform some extra validation.
     * If there are errors return array of errors ("fieldname"=>"error message"),
     * otherwise true if ok.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @return bool array of errors or true if ok
     */
    function validation($data) {
        return true;
    }
}
/**
 * This class adds extra methods to form wrapper specific to be used for module
 * add / update forms (mod/{modname}.mod_form.php replaces deprecared mod/{modname}/mod.html
 *
 */
class moodleform_mod extends moodleform {
    /**
     * Adds all the standard elements to a form to edit the settings for an activity module.
     *
     */
    function standard_coursemodule_elements(){
        $mform =& $this->_form;
        $mform->addElement('header', '', get_string('modstandardels', 'form'));

        $mform->addElement('modgroupmode', 'groupmode', get_string('groupmode'));

        $mform->addElement('modvisible', 'visible', get_string('visible'));

        $this->standard_hidden_coursemodule_elements();
    }

    function standard_hidden_coursemodule_elements(){
        $mform =& $this->_form;
        $mform->addElement('hidden', 'course', 0);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'coursemodule', 0);
        $mform->setType('coursemodule', PARAM_INT);

        $mform->addElement('hidden', 'section', 0);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'module', 0);
        $mform->setType('module', PARAM_INT);

        $mform->addElement('hidden', 'modulename', '');
        $mform->setType('modulename', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'instance', 0);
        $mform->setType('instance', PARAM_INT);

        $mform->addElement('hidden', 'add', 0);
        $mform->setType('add', PARAM_ALPHA);

        $mform->addElement('hidden', 'update', 0);
        $mform->setType('update', PARAM_INT);
    }

    /**
     * This function is called by course/modedit.php to setup defaults for standard form
     * elements.
     *
     * @param object $course
     * @param object $cm
     * @param integer $section
     */
    function standard_coursemodule_elements_setup($course, $cm, $section){
        $this->modgroupmode_setup($course, $cm);
        $this->modvisible_setup($course, $cm, $section);
    }
    /**
     * You can call this to load the default for the groupmode element.
     *
     * @param object $course
     * @param object $cm
     */
    function modgroupmode_setup($course, $cm){
        $this->set_defaults(array('groupmode'=>groupmode($course, $cm)));

    }
    /**
     * Sets the default for modvisible form element.
     *
     * @param object $course
     * @param object $cm
     * @param integer $section section is a db id when updating a activity config
     *                   or the section no when adding a new activity
     */
    function modvisible_setup($course, $cm, $section){
        if ($cm) {
            $visible = $cm->visible;
        } else {
            $visible = 1;
        }

        if (!$cm) { // adding activity
                    //in this case $form->section is the section number, not the id
            $hiddensection = !get_field('course_sections', 'visible', 'section', $section, 'course', $course->id);
        } else { //updating activity
            $hiddensection = !get_field('course_sections', 'visible', 'id', $section);
        }
        if ($hiddensection) {
            $visible = 0;
        }
         $this->set_defaults(array('visible'=>$visible));
    }

}

/**
 * You never extend this class directly. The class methods of this class are available from
 * the private $this->_form property on moodleform and it's children. You generally only
 * call methods on this class from within abstract methods that you override on moodleform such
 * as definition and definition_after_data
 *
 */
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

    /**
     * Should be used for all elements of a form except for select, radio and checkboxes which
     * clean their own data.
     *
     * @param string $elementname
     * @param integer $paramtype use the constants PARAM_*.
     *     *  PARAM_CLEAN is deprecated and you should try to use a more specific type.
     *     *  PARAM_TEXT should be used for cleaning data that is expected to be plain text.
     *          It will strip all html tags. But will still let tags for multilang support
     *          through.
     *     *  PARAM_RAW means no cleaning whatsoever, it is used mostly for data from the
     *          html editor. Data from the editor is later cleaned before display using
     *          format_text() function. PARAM_RAW can also be used for data that is validated
     *          by some other way or printed by p() or s().
     *     *  PARAM_INT should be used for integers.
     *     *  PARAM_ACTION is an alias of PARAM_ALPHA and is used for hidden fields specifying
     *          form actions.
     */
    function setType($elementname, $paramtype) {
        $this->_types[$elementname] = $paramtype;
    }

    /**
     * See description of setType above. This can be used to set several types at once.
     *
     * @param array $paramtypes
     */
    function setTypes($paramtypes) {
        $this->_types = $paramtypes + $this->_types;
    }

    function updateSubmission($submission, $files) {
        $this->_flagSubmitted = false;

        if (empty($submission)) {
            $this->_submitValues = array();
        } else {
            foreach ($submission as $key=>$s) {
                if (array_key_exists($key, $this->_types)) {
                    $submission[$key] = clean_param($s, $this->_types[$key]);
                }
            }
            $this->_submitValues = $this->_recursiveFilter('stripslashes', $submission);
            $this->_flagSubmitted = true;
        }

        if (empty($files)) {
            $this->_submitFiles = array();
        } else {
            if (1 == get_magic_quotes_gpc()) {
                foreach ($files as $elname=>$file) {
                    // dangerous characters in filenames are cleaned later in upload_manager
                    $files[$elname]['name'] = stripslashes($files[$elname]['name']);
                }
            }
            $this->_submitFiles = $files;
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
     * Initializes a default form value. Used to specify the default for a new entry where
     * no data is loaded in using moodleform::set_defaults()
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
    function setHelpButtons($buttons, $suppresscheck=false, $function='helpbutton'){

        foreach ($buttons as $elementname => $button){
            $this->setHelpButton($elementname, $button, $suppresscheck, $function);
        }
    }
    /**
     * Add a single button.
     *
     * @param string $elementname name of the element to add the item to
     * @param array $button - arguments to pass to function $function
     * @param boolean $suppresscheck - whether to throw an error if the element
     *                                  doesn't exist.
     * @param string $function - function to generate html from the arguments in $button
     */
    function setHelpButton($elementname, $button, $suppresscheck=false, $function='helpbutton'){
        if (array_key_exists($elementname, $this->_elementIndex)){
            //_elements has a numeric index, this code accesses the elements by name
            $element=&$this->_elements[$this->_elementIndex[$elementname]];
            if (method_exists($element, 'setHelpButton')){
                $element->setHelpButton($button, $function);
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
                    unset($element); //TODO: find out how to properly initialize it

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
MoodleQuickForm::registerElementType('modvisible', "$CFG->libdir/form/modvisible.php", 'MoodleQuickForm_modvisible');
MoodleQuickForm::registerElementType('modgroupmode', "$CFG->libdir/form/modgroupmode.php", 'MoodleQuickForm_modgroupmode');
MoodleQuickForm::registerElementType('selectyesno', "$CFG->libdir/form/selectyesno.php", 'MoodleQuickForm_selectyesno');
MoodleQuickForm::registerElementType('modgrade', "$CFG->libdir/form/modgrade.php", 'MoodleQuickForm_modgrade');
MoodleQuickForm::registerElementType('submit', "$CFG->libdir/form/submit.php", 'MoodleQuickForm_submit');

?>