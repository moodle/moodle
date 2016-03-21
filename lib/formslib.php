<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
 *      form definition is used for both printing of form and processing and should be the same
 *              for both or you may lose some submitted data which won't be let through.
 *      you should be using setType for every form element except select, radio or checkbox
 *              elements, these elements clean themselves.
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** setup.php includes our hacked pear libs first */
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/DHTMLRulesTableless.php';
require_once 'HTML/QuickForm/Renderer/Tableless.php';
require_once 'HTML/QuickForm/Rule.php';

require_once $CFG->libdir.'/filelib.php';

/**
 * EDITOR_UNLIMITED_FILES - hard-coded value for the 'maxfiles' option
 */
define('EDITOR_UNLIMITED_FILES', -1);

/**
 * Callback called when PEAR throws an error
 *
 * @param PEAR_Error $error
 */
function pear_handle_error($error){
    echo '<strong>'.$error->GetMessage().'</strong> '.$error->getUserInfo();
    echo '<br /> <strong>Backtrace </strong>:';
    print_object($error->backtrace);
}

if ($CFG->debugdeveloper) {
    //TODO: this is a wrong place to init PEAR!
    $GLOBALS['_PEAR_default_error_mode'] = PEAR_ERROR_CALLBACK;
    $GLOBALS['_PEAR_default_error_options'] = 'pear_handle_error';
}

/**
 * Initalize javascript for date type form element
 *
 * @staticvar bool $done make sure it gets initalize once.
 * @global moodle_page $PAGE
 */
function form_init_date_js() {
    global $PAGE;
    static $done = false;
    if (!$done) {
        $calendar = \core_calendar\type_factory::get_calendar_instance();
        $module   = 'moodle-form-dateselector';
        $function = 'M.form.dateselector.init_date_selectors';
        $config = array(array(
            'firstdayofweek'    => $calendar->get_starting_weekday(),
            'mon'               => strftime('%a', strtotime("Monday")),
            'tue'               => strftime('%a', strtotime("Tuesday")),
            'wed'               => strftime('%a', strtotime("Wednesday")),
            'thu'               => strftime('%a', strtotime("Thursday")),
            'fri'               => strftime('%a', strtotime("Friday")),
            'sat'               => strftime('%a', strtotime("Saturday")),
            'sun'               => strftime('%a', strtotime("Sunday")),
            'january'           => strftime('%B', strtotime("January 1")),
            'february'          => strftime('%B', strtotime("February 1")),
            'march'             => strftime('%B', strtotime("March 1")),
            'april'             => strftime('%B', strtotime("April 1")),
            'may'               => strftime('%B', strtotime("May 1")),
            'june'              => strftime('%B', strtotime("June 1")),
            'july'              => strftime('%B', strtotime("July 1")),
            'august'            => strftime('%B', strtotime("August 1")),
            'september'         => strftime('%B', strtotime("September 1")),
            'october'           => strftime('%B', strtotime("October 1")),
            'november'          => strftime('%B', strtotime("November 1")),
            'december'          => strftime('%B', strtotime("December 1"))
        ));
        $PAGE->requires->yui_module($module, $function, $config);
        $done = true;
    }
}

/**
 * Wrapper that separates quickforms syntax from moodle code
 *
 * Moodle specific wrapper that separates quickforms syntax from moodle code. You won't directly
 * use this class you should write a class definition which extends this class or a more specific
 * subclass such a moodleform_mod for each form you want to display and/or process with formslib.
 *
 * You will write your own definition() method which performs the form set up.
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo      MDL-19380 rethink the file scanning
 */
abstract class moodleform {
    /** @var string name of the form */
    protected $_formname;       // form name

    /** @var MoodleQuickForm quickform object definition */
    protected $_form;

    /** @var array globals workaround */
    protected $_customdata;

    /** @var object definition_after_data executed flag */
    protected $_definition_finalized = false;

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
     * @param mixed $action the action attribute for the form. If empty defaults to auto detect the
     *              current url. If a moodle_url object then outputs params as hidden variables.
     * @param mixed $customdata if your form defintion method needs access to data such as $course
     *              $cm, etc. to construct the form definition then pass it in this array. You can
     *              use globals for somethings.
     * @param string $method if you set this to anything other than 'post' then _GET and _POST will
     *               be merged and used as incoming data to the form.
     * @param string $target target frame for form submission. You will rarely use this. Don't use
     *               it if you don't need to as the target attribute is deprecated in xhtml strict.
     * @param mixed $attributes you can pass a string of html attributes here or an array.
     * @param bool $editable
     */
    public function __construct($action=null, $customdata=null, $method='post', $target='', $attributes=null, $editable=true) {
        global $CFG, $FULLME;
        // no standard mform in moodle should allow autocomplete with the exception of user signup
        if (empty($attributes)) {
            $attributes = array('autocomplete'=>'off');
        } else if (is_array($attributes)) {
            $attributes['autocomplete'] = 'off';
        } else {
            if (strpos($attributes, 'autocomplete') === false) {
                $attributes .= ' autocomplete="off" ';
            }
        }

        if (empty($action)){
            // do not rely on PAGE->url here because dev often do not setup $actualurl properly in admin_externalpage_setup()
            $action = strip_querystring($FULLME);
            if (!empty($CFG->sslproxy)) {
                // return only https links when using SSL proxy
                $action = preg_replace('/^http:/', 'https:', $action, 1);
            }
            //TODO: use following instead of FULLME - see MDL-33015
            //$action = strip_querystring(qualified_me());
        }
        // Assign custom data first, so that get_form_identifier can use it.
        $this->_customdata = $customdata;
        $this->_formname = $this->get_form_identifier();

        $this->_form = new MoodleQuickForm($this->_formname, $method, $action, $target, $attributes);
        if (!$editable){
            $this->_form->hardFreeze();
        }

        $this->definition();

        $this->_form->addElement('hidden', 'sesskey', null); // automatic sesskey protection
        $this->_form->setType('sesskey', PARAM_RAW);
        $this->_form->setDefault('sesskey', sesskey());
        $this->_form->addElement('hidden', '_qf__'.$this->_formname, null);   // form submission marker
        $this->_form->setType('_qf__'.$this->_formname, PARAM_RAW);
        $this->_form->setDefault('_qf__'.$this->_formname, 1);
        $this->_form->_setDefaultRuleMessages();

        // we have to know all input types before processing submission ;-)
        $this->_process_submission($method);
    }

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function moodleform($action=null, $customdata=null, $method='post', $target='', $attributes=null, $editable=true) {
        self::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }

    /**
     * It should returns unique identifier for the form.
     * Currently it will return class name, but in case two same forms have to be
     * rendered on same page then override function to get unique form identifier.
     * e.g This is used on multiple self enrollments page.
     *
     * @return string form identifier.
     */
    protected function get_form_identifier() {
        $class = get_class($this);

        return preg_replace('/[^a-z0-9_]/i', '_', $class);
    }

    /**
     * To autofocus on first form element or first element with error.
     *
     * @param string $name if this is set then the focus is forced to a field with this name
     * @return string javascript to select form element with first error or
     *                first element if no errors. Use this as a parameter
     *                when calling print_header
     */
    function focus($name=NULL) {
        $form =& $this->_form;
        $elkeys = array_keys($form->_elementIndex);
        $error = false;
        if (isset($form->_errors) &&  0 != count($form->_errors)){
            $errorkeys = array_keys($form->_errors);
            $elkeys = array_intersect($elkeys, $errorkeys);
            $error = true;
        }

        if ($error or empty($name)) {
            $names = array();
            while (empty($names) and !empty($elkeys)) {
                $el = array_shift($elkeys);
                $names = $form->_getElNamesRecursive($el);
            }
            if (!empty($names)) {
                $name = array_shift($names);
            }
        }

        $focus = '';
        if (!empty($name)) {
            $focus = 'forms[\''.$form->getAttribute('id').'\'].elements[\''.$name.'\']';
        }

        return $focus;
     }

    /**
     * Internal method. Alters submitted data to be suitable for quickforms processing.
     * Must be called when the form is fully set up.
     *
     * @param string $method name of the method which alters submitted data
     */
    function _process_submission($method) {
        $submission = array();
        if ($method == 'post') {
            if (!empty($_POST)) {
                $submission = $_POST;
            }
        } else {
            $submission = $_GET;
            merge_query_params($submission, $_POST); // Emulate handling of parameters in xxxx_param().
        }

        // following trick is needed to enable proper sesskey checks when using GET forms
        // the _qf__.$this->_formname serves as a marker that form was actually submitted
        if (array_key_exists('_qf__'.$this->_formname, $submission) and $submission['_qf__'.$this->_formname] == 1) {
            if (!confirm_sesskey()) {
                print_error('invalidsesskey');
            }
            $files = $_FILES;
        } else {
            $submission = array();
            $files = array();
        }
        $this->detectMissingSetType();

        $this->_form->updateSubmission($submission, $files);
    }

    /**
     * Internal method - should not be used anywhere.
     * @deprecated since 2.6
     * @return array $_POST.
     */
    protected function _get_post_params() {
        return $_POST;
    }

    /**
     * Internal method. Validates all old-style deprecated uploaded files.
     * The new way is to upload files via repository api.
     *
     * @param array $files list of files to be validated
     * @return bool|array Success or an array of errors
     */
    function _validate_files(&$files) {
        global $CFG, $COURSE;

        $files = array();

        if (empty($_FILES)) {
            // we do not need to do any checks because no files were submitted
            // note: server side rules do not work for files - use custom verification in validate() instead
            return true;
        }

        $errors = array();
        $filenames = array();

        // now check that we really want each file
        foreach ($_FILES as $elname=>$file) {
            $required = $this->_form->isElementRequired($elname);

            if ($file['error'] == 4 and $file['size'] == 0) {
                if ($required) {
                    $errors[$elname] = get_string('required');
                }
                unset($_FILES[$elname]);
                continue;
            }

            if (!empty($file['error'])) {
                $errors[$elname] = file_get_upload_error($file['error']);
                unset($_FILES[$elname]);
                continue;
            }

            if (!is_uploaded_file($file['tmp_name'])) {
                // TODO: improve error message
                $errors[$elname] = get_string('error');
                unset($_FILES[$elname]);
                continue;
            }

            if (!$this->_form->elementExists($elname) or !$this->_form->getElementType($elname)=='file') {
                // hmm, this file was not requested
                unset($_FILES[$elname]);
                continue;
            }

            // NOTE: the viruses are scanned in file picker, no need to deal with them here.

            $filename = clean_param($_FILES[$elname]['name'], PARAM_FILE);
            if ($filename === '') {
                // TODO: improve error message - wrong chars
                $errors[$elname] = get_string('error');
                unset($_FILES[$elname]);
                continue;
            }
            if (in_array($filename, $filenames)) {
                // TODO: improve error message - duplicate name
                $errors[$elname] = get_string('error');
                unset($_FILES[$elname]);
                continue;
            }
            $filenames[] = $filename;
            $_FILES[$elname]['name'] = $filename;

            $files[$elname] = $_FILES[$elname]['tmp_name'];
        }

        // return errors if found
        if (count($errors) == 0){
            return true;

        } else {
            $files = array();
            return $errors;
        }
    }

    /**
     * Internal method. Validates filepicker and filemanager files if they are
     * set as required fields. Also, sets the error message if encountered one.
     *
     * @return bool|array with errors
     */
    protected function validate_draft_files() {
        global $USER;
        $mform =& $this->_form;

        $errors = array();
        //Go through all the required elements and make sure you hit filepicker or
        //filemanager element.
        foreach ($mform->_rules as $elementname => $rules) {
            $elementtype = $mform->getElementType($elementname);
            //If element is of type filepicker then do validation
            if (($elementtype == 'filepicker') || ($elementtype == 'filemanager')){
                //Check if rule defined is required rule
                foreach ($rules as $rule) {
                    if ($rule['type'] == 'required') {
                        $draftid = (int)$mform->getSubmitValue($elementname);
                        $fs = get_file_storage();
                        $context = context_user::instance($USER->id);
                        if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
                            $errors[$elementname] = $rule['message'];
                        }
                    }
                }
            }
        }
        // Check all the filemanager elements to make sure they do not have too many
        // files in them.
        foreach ($mform->_elements as $element) {
            if ($element->_type == 'filemanager') {
                $maxfiles = $element->getMaxfiles();
                if ($maxfiles > 0) {
                    $draftid = (int)$element->getValue();
                    $fs = get_file_storage();
                    $context = context_user::instance($USER->id);
                    $files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, '', false);
                    if (count($files) > $maxfiles) {
                        $errors[$element->getName()] = get_string('err_maxfiles', 'form', $maxfiles);
                    }
                }
            }
        }
        if (empty($errors)) {
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
     * note: $slashed param removed
     *
     * @param stdClass|array $default_values object or array of default values
     */
    function set_data($default_values) {
        if (is_object($default_values)) {
            $default_values = (array)$default_values;
        }
        $this->_form->setDefaults($default_values);
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
     * Checks if button pressed is not for submitting the form
     *
     * @staticvar bool $nosubmit keeps track of no submit button
     * @return bool
     */
    function no_submit_button_pressed(){
        static $nosubmit = null; // one check is enough
        if (!is_null($nosubmit)){
            return $nosubmit;
        }
        $mform =& $this->_form;
        $nosubmit = false;
        if (!$this->is_submitted()){
            return false;
        }
        foreach ($mform->_noSubmitButtons as $nosubmitbutton){
            if (optional_param($nosubmitbutton, 0, PARAM_RAW)){
                $nosubmit = true;
                break;
            }
        }
        return $nosubmit;
    }


    /**
     * Check that form data is valid.
     * You should almost always use this, rather than {@link validate_defined_fields}
     *
     * @return bool true if form data valid
     */
    function is_validated() {
        //finalize the form definition before any processing
        if (!$this->_definition_finalized) {
            $this->_definition_finalized = true;
            $this->definition_after_data();
        }

        return $this->validate_defined_fields();
    }

    /**
     * Validate the form.
     *
     * You almost always want to call {@link is_validated} instead of this
     * because it calls {@link definition_after_data} first, before validating the form,
     * which is what you want in 99% of cases.
     *
     * This is provided as a separate function for those special cases where
     * you want the form validated before definition_after_data is called
     * for example, to selectively add new elements depending on a no_submit_button press,
     * but only when the form is valid when the no_submit_button is pressed,
     *
     * @param bool $validateonnosubmit optional, defaults to false.  The default behaviour
     *             is NOT to validate the form when a no submit button has been pressed.
     *             pass true here to override this behaviour
     *
     * @return bool true if form data valid
     */
    function validate_defined_fields($validateonnosubmit=false) {
        static $validated = null; // one validation is enough
        $mform =& $this->_form;
        if ($this->no_submit_button_pressed() && empty($validateonnosubmit)){
            return false;
        } elseif ($validated === null) {
            $internal_val = $mform->validate();

            $files = array();
            $file_val = $this->_validate_files($files);
            //check draft files for validation and flag them if required files
            //are not in draft area.
            $draftfilevalue = $this->validate_draft_files();

            if ($file_val !== true && $draftfilevalue !== true) {
                $file_val = array_merge($file_val, $draftfilevalue);
            } else if ($draftfilevalue !== true) {
                $file_val = $draftfilevalue;
            } //default is file_val, so no need to assign.

            if ($file_val !== true) {
                if (!empty($file_val)) {
                    foreach ($file_val as $element=>$msg) {
                        $mform->setElementError($element, $msg);
                    }
                }
                $file_val = false;
            }

            $data = $mform->exportValues();
            $moodle_val = $this->validation($data, $files);
            if ((is_array($moodle_val) && count($moodle_val)!==0)) {
                // non-empty array means errors
                foreach ($moodle_val as $element=>$msg) {
                    $mform->setElementError($element, $msg);
                }
                $moodle_val = false;

            } else {
                // anything else means validation ok
                $moodle_val = true;
            }

            $validated = ($internal_val and $moodle_val and $file_val);
        }
        return $validated;
    }

    /**
     * Return true if a cancel button has been pressed resulting in the form being submitted.
     *
     * @return bool true if a cancel button has been pressed
     */
    function is_cancelled(){
        $mform =& $this->_form;
        if ($mform->isSubmitted()){
            foreach ($mform->_cancelButtons as $cancelbutton){
                if (optional_param($cancelbutton, 0, PARAM_RAW)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * note: $slashed param removed
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    function get_data() {
        $mform =& $this->_form;

        if (!$this->is_cancelled() and $this->is_submitted() and $this->is_validated()) {
            $data = $mform->exportValues();
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
     * Return submitted data without validation or NULL if there is no submitted data.
     * note: $slashed param removed
     *
     * @return object submitted data; NULL if not submitted
     */
    function get_submitted_data() {
        $mform =& $this->_form;

        if ($this->is_submitted()) {
            $data = $mform->exportValues();
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
     *
     * @deprecated since Moodle 2.0
     * @todo MDL-31294 remove this api
     * @see moodleform::save_stored_file()
     * @see moodleform::save_file()
     * @param string $destination path where file should be stored
     * @return bool Always false
     */
    function save_files($destination) {
        debugging('Not used anymore, please fix code! Use save_stored_file() or save_file() instead');
        return false;
    }

    /**
     * Returns name of uploaded file.
     *
     * @param string $elname first element if null
     * @return string|bool false in case of failure, string if ok
     */
    function get_new_filename($elname=null) {
        global $USER;

        if (!$this->is_submitted() or !$this->is_validated()) {
            return false;
        }

        if (is_null($elname)) {
            if (empty($_FILES)) {
                return false;
            }
            reset($_FILES);
            $elname = key($_FILES);
        }

        if (empty($elname)) {
            return false;
        }

        $element = $this->_form->getElement($elname);

        if ($element instanceof MoodleQuickForm_filepicker || $element instanceof MoodleQuickForm_filemanager) {
            $values = $this->_form->exportValues($elname);
            if (empty($values[$elname])) {
                return false;
            }
            $draftid = $values[$elname];
            $fs = get_file_storage();
            $context = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
                return false;
            }
            $file = reset($files);
            return $file->get_filename();
        }

        if (!isset($_FILES[$elname])) {
            return false;
        }

        return $_FILES[$elname]['name'];
    }

    /**
     * Save file to standard filesystem
     *
     * @param string $elname name of element
     * @param string $pathname full path name of file
     * @param bool $override override file if exists
     * @return bool success
     */
    function save_file($elname, $pathname, $override=false) {
        global $USER;

        if (!$this->is_submitted() or !$this->is_validated()) {
            return false;
        }
        if (file_exists($pathname)) {
            if ($override) {
                if (!@unlink($pathname)) {
                    return false;
                }
            } else {
                return false;
            }
        }

        $element = $this->_form->getElement($elname);

        if ($element instanceof MoodleQuickForm_filepicker || $element instanceof MoodleQuickForm_filemanager) {
            $values = $this->_form->exportValues($elname);
            if (empty($values[$elname])) {
                return false;
            }
            $draftid = $values[$elname];
            $fs = get_file_storage();
            $context = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
                return false;
            }
            $file = reset($files);

            return $file->copy_content_to($pathname);

        } else if (isset($_FILES[$elname])) {
            return copy($_FILES[$elname]['tmp_name'], $pathname);
        }

        return false;
    }

    /**
     * Returns a temporary file, do not forget to delete after not needed any more.
     *
     * @param string $elname name of the elmenet
     * @return string|bool either string or false
     */
    function save_temp_file($elname) {
        if (!$this->get_new_filename($elname)) {
            return false;
        }
        if (!$dir = make_temp_directory('forms')) {
            return false;
        }
        if (!$tempfile = tempnam($dir, 'tempup_')) {
            return false;
        }
        if (!$this->save_file($elname, $tempfile, true)) {
            // something went wrong
            @unlink($tempfile);
            return false;
        }

        return $tempfile;
    }

    /**
     * Get draft files of a form element
     * This is a protected method which will be used only inside moodleforms
     *
     * @param string $elname name of element
     * @return array|bool|null
     */
    protected function get_draft_files($elname) {
        global $USER;

        if (!$this->is_submitted()) {
            return false;
        }

        $element = $this->_form->getElement($elname);

        if ($element instanceof MoodleQuickForm_filepicker || $element instanceof MoodleQuickForm_filemanager) {
            $values = $this->_form->exportValues($elname);
            if (empty($values[$elname])) {
                return false;
            }
            $draftid = $values[$elname];
            $fs = get_file_storage();
            $context = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
                return null;
            }
            return $files;
        }
        return null;
    }

    /**
     * Save file to local filesystem pool
     *
     * @param string $elname name of element
     * @param int $newcontextid id of context
     * @param string $newcomponent name of the component
     * @param string $newfilearea name of file area
     * @param int $newitemid item id
     * @param string $newfilepath path of file where it get stored
     * @param string $newfilename use specified filename, if not specified name of uploaded file used
     * @param bool $overwrite overwrite file if exists
     * @param int $newuserid new userid if required
     * @return mixed stored_file object or false if error; may throw exception if duplicate found
     */
    function save_stored_file($elname, $newcontextid, $newcomponent, $newfilearea, $newitemid, $newfilepath='/',
                              $newfilename=null, $overwrite=false, $newuserid=null) {
        global $USER;

        if (!$this->is_submitted() or !$this->is_validated()) {
            return false;
        }

        if (empty($newuserid)) {
            $newuserid = $USER->id;
        }

        $element = $this->_form->getElement($elname);
        $fs = get_file_storage();

        if ($element instanceof MoodleQuickForm_filepicker) {
            $values = $this->_form->exportValues($elname);
            if (empty($values[$elname])) {
                return false;
            }
            $draftid = $values[$elname];
            $context = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($context->id, 'user' ,'draft', $draftid, 'id DESC', false)) {
                return false;
            }
            $file = reset($files);
            if (is_null($newfilename)) {
                $newfilename = $file->get_filename();
            }

            if ($overwrite) {
                if ($oldfile = $fs->get_file($newcontextid, $newcomponent, $newfilearea, $newitemid, $newfilepath, $newfilename)) {
                    if (!$oldfile->delete()) {
                        return false;
                    }
                }
            }

            $file_record = array('contextid'=>$newcontextid, 'component'=>$newcomponent, 'filearea'=>$newfilearea, 'itemid'=>$newitemid,
                                 'filepath'=>$newfilepath, 'filename'=>$newfilename, 'userid'=>$newuserid);
            return $fs->create_file_from_storedfile($file_record, $file);

        } else if (isset($_FILES[$elname])) {
            $filename = is_null($newfilename) ? $_FILES[$elname]['name'] : $newfilename;

            if ($overwrite) {
                if ($oldfile = $fs->get_file($newcontextid, $newcomponent, $newfilearea, $newitemid, $newfilepath, $newfilename)) {
                    if (!$oldfile->delete()) {
                        return false;
                    }
                }
            }

            $file_record = array('contextid'=>$newcontextid, 'component'=>$newcomponent, 'filearea'=>$newfilearea, 'itemid'=>$newitemid,
                                 'filepath'=>$newfilepath, 'filename'=>$newfilename, 'userid'=>$newuserid);
            return $fs->create_file_from_pathname($file_record, $_FILES[$elname]['tmp_name']);
        }

        return false;
    }

    /**
     * Get content of uploaded file.
     *
     * @param string $elname name of file upload element
     * @return string|bool false in case of failure, string if ok
     */
    function get_file_content($elname) {
        global $USER;

        if (!$this->is_submitted() or !$this->is_validated()) {
            return false;
        }

        $element = $this->_form->getElement($elname);

        if ($element instanceof MoodleQuickForm_filepicker || $element instanceof MoodleQuickForm_filemanager) {
            $values = $this->_form->exportValues($elname);
            if (empty($values[$elname])) {
                return false;
            }
            $draftid = $values[$elname];
            $fs = get_file_storage();
            $context = context_user::instance($USER->id);
            if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
                return false;
            }
            $file = reset($files);

            return $file->get_content();

        } else if (isset($_FILES[$elname])) {
            return file_get_contents($_FILES[$elname]['tmp_name']);
        }

        return false;
    }

    /**
     * Print html form.
     */
    function display() {
        //finalize the form definition if not yet done
        if (!$this->_definition_finalized) {
            $this->_definition_finalized = true;
            $this->definition_after_data();
        }

        $this->_form->display();
    }

    /**
     * Renders the html form (same as display, but returns the result).
     *
     * Note that you can only output this rendered result once per page, as
     * it contains IDs which must be unique.
     *
     * @return string HTML code for the form
     */
    public function render() {
        ob_start();
        $this->display();
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    /**
     * Form definition. Abstract method - always override!
     */
    protected abstract function definition();

    /**
     * Dummy stub method - override if you need to setup the form depending on current
     * values. This method is called after definition(), data submission and set_data().
     * All form setup that is dependent on form values should go in here.
     */
    function definition_after_data(){
    }

    /**
     * Dummy stub method - override if you needed to perform some extra validation.
     * If there are errors return array of errors ("fieldname"=>"error message"),
     * otherwise true if ok.
     *
     * Server side rules do not work for uploaded files, implement serverside rules here if needed.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    function validation($data, $files) {
        return array();
    }

    /**
     * Helper used by {@link repeat_elements()}.
     *
     * @param int $i the index of this element.
     * @param HTML_QuickForm_element $elementclone
     * @param array $namecloned array of names
     */
    function repeat_elements_fix_clone($i, $elementclone, &$namecloned) {
        $name = $elementclone->getName();
        $namecloned[] = $name;

        if (!empty($name)) {
            $elementclone->setName($name."[$i]");
        }

        if (is_a($elementclone, 'HTML_QuickForm_header')) {
            $value = $elementclone->_text;
            $elementclone->setValue(str_replace('{no}', ($i+1), $value));

        } else if (is_a($elementclone, 'HTML_QuickForm_submit') || is_a($elementclone, 'HTML_QuickForm_button')) {
            $elementclone->setValue(str_replace('{no}', ($i+1), $elementclone->getValue()));

        } else {
            $value=$elementclone->getLabel();
            $elementclone->setLabel(str_replace('{no}', ($i+1), $value));
        }
    }

    /**
     * Method to add a repeating group of elements to a form.
     *
     * @param array $elementobjs Array of elements or groups of elements that are to be repeated
     * @param int $repeats no of times to repeat elements initially
     * @param array $options a nested array. The first array key is the element name.
     *    the second array key is the type of option to set, and depend on that option,
     *    the value takes different forms.
     *         'default'    - default value to set. Can include '{no}' which is replaced by the repeat number.
     *         'type'       - PARAM_* type.
     *         'helpbutton' - array containing the helpbutton params.
     *         'disabledif' - array containing the disabledIf() arguments after the element name.
     *         'rule'       - array containing the addRule arguments after the element name.
     *         'expanded'   - whether this section of the form should be expanded by default. (Name be a header element.)
     *         'advanced'   - whether this element is hidden by 'Show more ...'.
     * @param string $repeathiddenname name for hidden element storing no of repeats in this form
     * @param string $addfieldsname name for button to add more fields
     * @param int $addfieldsno how many fields to add at a time
     * @param string $addstring name of button, {no} is replaced by no of blanks that will be added.
     * @param bool $addbuttoninside if true, don't call closeHeaderBefore($addfieldsname). Default false.
     * @return int no of repeats of element in this page
     */
    function repeat_elements($elementobjs, $repeats, $options, $repeathiddenname,
            $addfieldsname, $addfieldsno=5, $addstring=null, $addbuttoninside=false){
        if ($addstring===null){
            $addstring = get_string('addfields', 'form', $addfieldsno);
        } else {
            $addstring = str_ireplace('{no}', $addfieldsno, $addstring);
        }
        $repeats = optional_param($repeathiddenname, $repeats, PARAM_INT);
        $addfields = optional_param($addfieldsname, '', PARAM_TEXT);
        if (!empty($addfields)){
            $repeats += $addfieldsno;
        }
        $mform =& $this->_form;
        $mform->registerNoSubmitButton($addfieldsname);
        $mform->addElement('hidden', $repeathiddenname, $repeats);
        $mform->setType($repeathiddenname, PARAM_INT);
        //value not to be overridden by submitted value
        $mform->setConstants(array($repeathiddenname=>$repeats));
        $namecloned = array();
        for ($i = 0; $i < $repeats; $i++) {
            foreach ($elementobjs as $elementobj){
                $elementclone = fullclone($elementobj);
                $this->repeat_elements_fix_clone($i, $elementclone, $namecloned);

                if ($elementclone instanceof HTML_QuickForm_group && !$elementclone->_appendName) {
                    foreach ($elementclone->getElements() as $el) {
                        $this->repeat_elements_fix_clone($i, $el, $namecloned);
                    }
                    $elementclone->setLabel(str_replace('{no}', $i + 1, $elementclone->getLabel()));
                }

                $mform->addElement($elementclone);
            }
        }
        for ($i=0; $i<$repeats; $i++) {
            foreach ($options as $elementname => $elementoptions){
                $pos=strpos($elementname, '[');
                if ($pos!==FALSE){
                    $realelementname = substr($elementname, 0, $pos)."[$i]";
                    $realelementname .= substr($elementname, $pos);
                }else {
                    $realelementname = $elementname."[$i]";
                }
                foreach ($elementoptions as  $option => $params){

                    switch ($option){
                        case 'default' :
                            $mform->setDefault($realelementname, str_replace('{no}', $i + 1, $params));
                            break;
                        case 'helpbutton' :
                            $params = array_merge(array($realelementname), $params);
                            call_user_func_array(array(&$mform, 'addHelpButton'), $params);
                            break;
                        case 'disabledif' :
                            foreach ($namecloned as $num => $name){
                                if ($params[0] == $name){
                                    $params[0] = $params[0]."[$i]";
                                    break;
                                }
                            }
                            $params = array_merge(array($realelementname), $params);
                            call_user_func_array(array(&$mform, 'disabledIf'), $params);
                            break;
                        case 'rule' :
                            if (is_string($params)){
                                $params = array(null, $params, null, 'client');
                            }
                            $params = array_merge(array($realelementname), $params);
                            call_user_func_array(array(&$mform, 'addRule'), $params);
                            break;

                        case 'type':
                            $mform->setType($realelementname, $params);
                            break;

                        case 'expanded':
                            $mform->setExpanded($realelementname, $params);
                            break;

                        case 'advanced' :
                            $mform->setAdvanced($realelementname, $params);
                            break;
                    }
                }
            }
        }
        $mform->addElement('submit', $addfieldsname, $addstring);

        if (!$addbuttoninside) {
            $mform->closeHeaderBefore($addfieldsname);
        }

        return $repeats;
    }

    /**
     * Adds a link/button that controls the checked state of a group of checkboxes.
     *
     * @param int $groupid The id of the group of advcheckboxes this element controls
     * @param string $text The text of the link. Defaults to selectallornone ("select all/none")
     * @param array $attributes associative array of HTML attributes
     * @param int $originalValue The original general state of the checkboxes before the user first clicks this element
     */
    function add_checkbox_controller($groupid, $text = null, $attributes = null, $originalValue = 0) {
        global $CFG, $PAGE;

        // Name of the controller button
        $checkboxcontrollername = 'nosubmit_checkbox_controller' . $groupid;
        $checkboxcontrollerparam = 'checkbox_controller'. $groupid;
        $checkboxgroupclass = 'checkboxgroup'.$groupid;

        // Set the default text if none was specified
        if (empty($text)) {
            $text = get_string('selectallornone', 'form');
        }

        $mform = $this->_form;
        $selectvalue = optional_param($checkboxcontrollerparam, null, PARAM_INT);
        $contollerbutton = optional_param($checkboxcontrollername, null, PARAM_ALPHAEXT);

        $newselectvalue = $selectvalue;
        if (is_null($selectvalue)) {
            $newselectvalue = $originalValue;
        } else if (!is_null($contollerbutton)) {
            $newselectvalue = (int) !$selectvalue;
        }
        // set checkbox state depending on orignal/submitted value by controoler button
        if (!is_null($contollerbutton) || is_null($selectvalue)) {
            foreach ($mform->_elements as $element) {
                if (($element instanceof MoodleQuickForm_advcheckbox) &&
                        $element->getAttribute('class') == $checkboxgroupclass &&
                        !$element->isFrozen()) {
                    $mform->setConstants(array($element->getName() => $newselectvalue));
                }
            }
        }

        $mform->addElement('hidden', $checkboxcontrollerparam, $newselectvalue, array('id' => "id_".$checkboxcontrollerparam));
        $mform->setType($checkboxcontrollerparam, PARAM_INT);
        $mform->setConstants(array($checkboxcontrollerparam => $newselectvalue));

        $PAGE->requires->yui_module('moodle-form-checkboxcontroller', 'M.form.checkboxcontroller',
                array(
                    array('groupid' => $groupid,
                        'checkboxclass' => $checkboxgroupclass,
                        'checkboxcontroller' => $checkboxcontrollerparam,
                        'controllerbutton' => $checkboxcontrollername)
                    )
                );

        require_once("$CFG->libdir/form/submit.php");
        $submitlink = new MoodleQuickForm_submit($checkboxcontrollername, $attributes);
        $mform->addElement($submitlink);
        $mform->registerNoSubmitButton($checkboxcontrollername);
        $mform->setDefault($checkboxcontrollername, $text);
    }

    /**
     * Use this method to a cancel and submit button to the end of your form. Pass a param of false
     * if you don't want a cancel button in your form. If you have a cancel button make sure you
     * check for it being pressed using is_cancelled() and redirecting if it is true before trying to
     * get data with get_data().
     *
     * @param bool $cancel whether to show cancel button, default true
     * @param string $submitlabel label for submit button, defaults to get_string('savechanges')
     */
    function add_action_buttons($cancel = true, $submitlabel=null){
        if (is_null($submitlabel)){
            $submitlabel = get_string('savechanges');
        }
        $mform =& $this->_form;
        if ($cancel){
            //when two elements we need a group
            $buttonarray=array();
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
            $buttonarray[] = &$mform->createElement('cancel');
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');
        } else {
            //no group needed
            $mform->addElement('submit', 'submitbutton', $submitlabel);
            $mform->closeHeaderBefore('submitbutton');
        }
    }

    /**
     * Adds an initialisation call for a standard JavaScript enhancement.
     *
     * This function is designed to add an initialisation call for a JavaScript
     * enhancement that should exist within javascript-static M.form.init_{enhancementname}.
     *
     * Current options:
     *  - Selectboxes
     *      - smartselect:  Turns a nbsp indented select box into a custom drop down
     *                      control that supports multilevel and category selection.
     *                      $enhancement = 'smartselect';
     *                      $options = array('selectablecategories' => true|false)
     *
     * @since Moodle 2.0
     * @param string|element $element form element for which Javascript needs to be initalized
     * @param string $enhancement which init function should be called
     * @param array $options options passed to javascript
     * @param array $strings strings for javascript
     */
    function init_javascript_enhancement($element, $enhancement, array $options=array(), array $strings=null) {
        global $PAGE;
        if (is_string($element)) {
            $element = $this->_form->getElement($element);
        }
        if (is_object($element)) {
            $element->_generateId();
            $elementid = $element->getAttribute('id');
            $PAGE->requires->js_init_call('M.form.init_'.$enhancement, array($elementid, $options));
            if (is_array($strings)) {
                foreach ($strings as $string) {
                    if (is_array($string)) {
                        call_user_func_array(array($PAGE->requires, 'string_for_js'), $string);
                    } else {
                        $PAGE->requires->string_for_js($string, 'moodle');
                    }
                }
            }
        }
    }

    /**
     * Returns a JS module definition for the mforms JS
     *
     * @return array
     */
    public static function get_js_module() {
        global $CFG;
        return array(
            'name' => 'mform',
            'fullpath' => '/lib/form/form.js',
            'requires' => array('base', 'node')
        );
    }

    /**
     * Detects elements with missing setType() declerations.
     *
     * Finds elements in the form which should a PARAM_ type set and throws a
     * developer debug warning for any elements without it. This is to reduce the
     * risk of potential security issues by developers mistakenly forgetting to set
     * the type.
     *
     * @return void
     */
    private function detectMissingSetType() {
        global $CFG;

        if (!$CFG->debugdeveloper) {
            // Only for devs.
            return;
        }

        $mform = $this->_form;
        foreach ($mform->_elements as $element) {
            $group = false;
            $elements = array($element);

            if ($element->getType() == 'group') {
                $group = $element;
                $elements = $element->getElements();
            }

            foreach ($elements as $index => $element) {
                switch ($element->getType()) {
                    case 'hidden':
                    case 'text':
                    case 'url':
                        if ($group) {
                            $name = $group->getElementName($index);
                        } else {
                            $name = $element->getName();
                        }
                        $key = $name;
                        $found = array_key_exists($key, $mform->_types);
                        // For repeated elements we need to look for
                        // the "main" type, not for the one present
                        // on each repetition. All the stuff in formslib
                        // (repeat_elements(), updateSubmission()... seems
                        // to work that way.
                        while (!$found && strrpos($key, '[') !== false) {
                            $pos = strrpos($key, '[');
                            $key = substr($key, 0, $pos);
                            $found = array_key_exists($key, $mform->_types);
                        }
                        if (!$found) {
                            debugging("Did you remember to call setType() for '$name'? ".
                                'Defaulting to PARAM_RAW cleaning.', DEBUG_DEVELOPER);
                        }
                        break;
                }
            }
        }
    }

    /**
     * Used by tests to simulate submitted form data submission from the user.
     *
     * For form fields where no data is submitted the default for that field as set by set_data or setDefault will be passed to
     * get_data.
     *
     * This method sets $_POST or $_GET and $_FILES with the data supplied. Our unit test code empties all these
     * global arrays after each test.
     *
     * @param array  $simulatedsubmitteddata       An associative array of form values (same format as $_POST).
     * @param array  $simulatedsubmittedfiles      An associative array of files uploaded (same format as $_FILES). Can be omitted.
     * @param string $method                       'post' or 'get', defaults to 'post'.
     * @param null   $formidentifier               the default is to use the class name for this class but you may need to provide
     *                                              a different value here for some forms that are used more than once on the
     *                                              same page.
     */
    public static function mock_submit($simulatedsubmitteddata, $simulatedsubmittedfiles = array(), $method = 'post',
                                       $formidentifier = null) {
        $_FILES = $simulatedsubmittedfiles;
        if ($formidentifier === null) {
            $formidentifier = get_called_class();
        }
        $simulatedsubmitteddata['_qf__'.$formidentifier] = 1;
        $simulatedsubmitteddata['sesskey'] = sesskey();
        if (strtolower($method) === 'get') {
            $_GET = $simulatedsubmitteddata;
        } else {
            $_POST = $simulatedsubmitteddata;
        }
    }
}

/**
 * MoodleQuickForm implementation
 *
 * You never extend this class directly. The class methods of this class are available from
 * the private $this->_form property on moodleform and its children. You generally only
 * call methods on this class from within abstract methods that you override on moodleform such
 * as definition and definition_after_data
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm extends HTML_QuickForm_DHTMLRulesTableless {
    /** @var array type (PARAM_INT, PARAM_TEXT etc) of element value */
    var $_types = array();

    /** @var array dependent state for the element/'s */
    var $_dependencies = array();

    /** @var array Array of buttons that if pressed do not result in the processing of the form. */
    var $_noSubmitButtons=array();

    /** @var array Array of buttons that if pressed do not result in the processing of the form. */
    var $_cancelButtons=array();

    /** @var array Array whose keys are element names. If the key exists this is a advanced element */
    var $_advancedElements = array();

    /**
     * Array whose keys are element names and values are the desired collapsible state.
     * True for collapsed, False for expanded. If not present, set to default in
     * {@link self::accept()}.
     *
     * @var array
     */
    var $_collapsibleElements = array();

    /**
     * Whether to enable shortforms for this form
     *
     * @var boolean
     */
    var $_disableShortforms = false;

    /** @var bool whether to automatically initialise M.formchangechecker for this form. */
    protected $_use_form_change_checker = true;

    /**
     * The form name is derived from the class name of the wrapper minus the trailing form
     * It is a name with words joined by underscores whereas the id attribute is words joined by underscores.
     * @var string
     */
    var $_formName = '';

    /**
     * String with the html for hidden params passed in as part of a moodle_url
     * object for the action. Output in the form.
     * @var string
     */
    var $_pageparams = '';

    /**
     * Class constructor - same parameters as HTML_QuickForm_DHTMLRulesTableless
     *
     * @staticvar int $formcounter counts number of forms
     * @param string $formName Form's name.
     * @param string $method Form's method defaults to 'POST'
     * @param string|moodle_url $action Form's action
     * @param string $target (optional)Form's target defaults to none
     * @param mixed $attributes (optional)Extra attributes for <form> tag
     */
    public function __construct($formName, $method, $action, $target='', $attributes=null) {
        global $CFG, $OUTPUT;

        static $formcounter = 1;

        // TODO MDL-52313 Replace with the call to parent::__construct().
        HTML_Common::__construct($attributes);
        $target = empty($target) ? array() : array('target' => $target);
        $this->_formName = $formName;
        if (is_a($action, 'moodle_url')){
            $this->_pageparams = html_writer::input_hidden_params($action);
            $action = $action->out_omit_querystring();
        } else {
            $this->_pageparams = '';
        }
        // No 'name' atttribute for form in xhtml strict :
        $attributes = array('action' => $action, 'method' => $method, 'accept-charset' => 'utf-8') + $target;
        if (is_null($this->getAttribute('id'))) {
            $attributes['id'] = 'mform' . $formcounter;
        }
        $formcounter++;
        $this->updateAttributes($attributes);

        // This is custom stuff for Moodle :
        $oldclass=   $this->getAttribute('class');
        if (!empty($oldclass)){
            $this->updateAttributes(array('class'=>$oldclass.' mform'));
        }else {
            $this->updateAttributes(array('class'=>'mform'));
        }
        $this->_reqHTML = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';
        $this->_advancedHTML = '<img class="adv" title="'.get_string('advancedelement', 'form').'" alt="'.get_string('advancedelement', 'form').'" src="'.$OUTPUT->pix_url('adv') .'" />';
        $this->setRequiredNote(get_string('somefieldsrequired', 'form', '<img alt="'.get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />'));
    }

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function MoodleQuickForm($formName, $method, $action, $target='', $attributes=null) {
        self::__construct($formName, $method, $action, $target, $attributes);
    }

    /**
     * Use this method to indicate an element in a form is an advanced field. If items in a form
     * are marked as advanced then 'Hide/Show Advanced' buttons will automatically be displayed in the
     * form so the user can decide whether to display advanced form controls.
     *
     * If you set a header element to advanced then all elements it contains will also be set as advanced.
     *
     * @param string $elementName group or element name (not the element name of something inside a group).
     * @param bool $advanced default true sets the element to advanced. False removes advanced mark.
     */
    function setAdvanced($elementName, $advanced = true) {
        if ($advanced){
            $this->_advancedElements[$elementName]='';
        } elseif (isset($this->_advancedElements[$elementName])) {
            unset($this->_advancedElements[$elementName]);
        }
    }

    /**
     * Use this method to indicate that the fieldset should be shown as expanded.
     * The method is applicable to header elements only.
     *
     * @param string $headername header element name
     * @param boolean $expanded default true sets the element to expanded. False makes the element collapsed.
     * @param boolean $ignoreuserstate override the state regardless of the state it was on when
     *                                 the form was submitted.
     * @return void
     */
    function setExpanded($headername, $expanded = true, $ignoreuserstate = false) {
        if (empty($headername)) {
            return;
        }
        $element = $this->getElement($headername);
        if ($element->getType() != 'header') {
            debugging('Cannot use setExpanded on non-header elements', DEBUG_DEVELOPER);
            return;
        }
        if (!$headerid = $element->getAttribute('id')) {
            $element->_generateId();
            $headerid = $element->getAttribute('id');
        }
        if ($this->getElementType('mform_isexpanded_' . $headerid) === false) {
            // See if the form has been submitted already.
            $formexpanded = optional_param('mform_isexpanded_' . $headerid, -1, PARAM_INT);
            if (!$ignoreuserstate && $formexpanded != -1) {
                // Override expanded state with the form variable.
                $expanded = $formexpanded;
            }
            // Create the form element for storing expanded state.
            $this->addElement('hidden', 'mform_isexpanded_' . $headerid);
            $this->setType('mform_isexpanded_' . $headerid, PARAM_INT);
            $this->setConstant('mform_isexpanded_' . $headerid, (int) $expanded);
        }
        $this->_collapsibleElements[$headername] = !$expanded;
    }

    /**
     * Use this method to add show more/less status element required for passing
     * over the advanced elements visibility status on the form submission.
     *
     * @param string $headerName header element name.
     * @param boolean $showmore default false sets the advanced elements to be hidden.
     */
    function addAdvancedStatusElement($headerid, $showmore=false){
        // Add extra hidden element to store advanced items state for each section.
        if ($this->getElementType('mform_showmore_' . $headerid) === false) {
            // See if we the form has been submitted already.
            $formshowmore = optional_param('mform_showmore_' . $headerid, -1, PARAM_INT);
            if (!$showmore && $formshowmore != -1) {
                // Override showmore state with the form variable.
                $showmore = $formshowmore;
            }
            // Create the form element for storing advanced items state.
            $this->addElement('hidden', 'mform_showmore_' . $headerid);
            $this->setType('mform_showmore_' . $headerid, PARAM_INT);
            $this->setConstant('mform_showmore_' . $headerid, (int)$showmore);
        }
    }

    /**
     * This function has been deprecated. Show advanced has been replaced by
     * "Show more.../Show less..." in the shortforms javascript module.
     *
     * @deprecated since Moodle 2.5
     * @param bool $showadvancedNow if true will show advanced elements.
      */
    function setShowAdvanced($showadvancedNow = null){
        debugging('Call to deprecated function setShowAdvanced. See "Show more.../Show less..." in shortforms yui module.');
    }

    /**
     * This function has been deprecated. Show advanced has been replaced by
     * "Show more.../Show less..." in the shortforms javascript module.
     *
     * @deprecated since Moodle 2.5
     * @return bool (Always false)
      */
    function getShowAdvanced(){
        debugging('Call to deprecated function setShowAdvanced. See "Show more.../Show less..." in shortforms yui module.');
        return false;
    }

    /**
     * Use this method to indicate that the form will not be using shortforms.
     *
     * @param boolean $disable default true, controls if the shortforms are disabled.
     */
    function setDisableShortforms ($disable = true) {
        $this->_disableShortforms = $disable;
    }

    /**
     * Call this method if you don't want the formchangechecker JavaScript to be
     * automatically initialised for this form.
     */
    public function disable_form_change_checker() {
        $this->_use_form_change_checker = false;
    }

    /**
     * If you have called {@link disable_form_change_checker()} then you can use
     * this method to re-enable it. It is enabled by default, so normally you don't
     * need to call this.
     */
    public function enable_form_change_checker() {
        $this->_use_form_change_checker = true;
    }

    /**
     * @return bool whether this form should automatically initialise
     *      formchangechecker for itself.
     */
    public function is_form_change_checker_enabled() {
        return $this->_use_form_change_checker;
    }

    /**
    * Accepts a renderer
    *
    * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
    */
    function accept(&$renderer) {
        if (method_exists($renderer, 'setAdvancedElements')){
            //Check for visible fieldsets where all elements are advanced
            //and mark these headers as advanced as well.
            //Also mark all elements in a advanced header as advanced.
            $stopFields = $renderer->getStopFieldSetElements();
            $lastHeader = null;
            $lastHeaderAdvanced = false;
            $anyAdvanced = false;
            $anyError = false;
            foreach (array_keys($this->_elements) as $elementIndex){
                $element =& $this->_elements[$elementIndex];

                // if closing header and any contained element was advanced then mark it as advanced
                if ($element->getType()=='header' || in_array($element->getName(), $stopFields)){
                    if ($anyAdvanced && !is_null($lastHeader)) {
                        $lastHeader->_generateId();
                        $this->setAdvanced($lastHeader->getName());
                        $this->addAdvancedStatusElement($lastHeader->getAttribute('id'), $anyError);
                    }
                    $lastHeaderAdvanced = false;
                    unset($lastHeader);
                    $lastHeader = null;
                } elseif ($lastHeaderAdvanced) {
                    $this->setAdvanced($element->getName());
                }

                if ($element->getType()=='header'){
                    $lastHeader =& $element;
                    $anyAdvanced = false;
                    $anyError = false;
                    $lastHeaderAdvanced = isset($this->_advancedElements[$element->getName()]);
                } elseif (isset($this->_advancedElements[$element->getName()])){
                    $anyAdvanced = true;
                    if (isset($this->_errors[$element->getName()])) {
                        $anyError = true;
                    }
                }
            }
            // the last header may not be closed yet...
            if ($anyAdvanced && !is_null($lastHeader)){
                $this->setAdvanced($lastHeader->getName());
                $lastHeader->_generateId();
                $this->addAdvancedStatusElement($lastHeader->getAttribute('id'), $anyError);
            }
            $renderer->setAdvancedElements($this->_advancedElements);
        }
        if (method_exists($renderer, 'setCollapsibleElements') && !$this->_disableShortforms) {

            // Count the number of sections.
            $headerscount = 0;
            foreach (array_keys($this->_elements) as $elementIndex){
                $element =& $this->_elements[$elementIndex];
                if ($element->getType() == 'header') {
                    $headerscount++;
                }
            }

            $anyrequiredorerror = false;
            $headercounter = 0;
            $headername = null;
            foreach (array_keys($this->_elements) as $elementIndex){
                $element =& $this->_elements[$elementIndex];

                if ($element->getType() == 'header') {
                    $headercounter++;
                    $element->_generateId();
                    $headername = $element->getName();
                    $anyrequiredorerror = false;
                } else if (in_array($element->getName(), $this->_required) || isset($this->_errors[$element->getName()])) {
                    $anyrequiredorerror = true;
                } else {
                    // Do not reset $anyrequiredorerror to false because we do not want any other element
                    // in this header (fieldset) to possibly revert the state given.
                }

                if ($element->getType() == 'header') {
                    if ($headercounter === 1 && !isset($this->_collapsibleElements[$headername])) {
                        // By default the first section is always expanded, except if a state has already been set.
                        $this->setExpanded($headername, true);
                    } else if (($headercounter === 2 && $headerscount === 2) && !isset($this->_collapsibleElements[$headername])) {
                        // The second section is always expanded if the form only contains 2 sections),
                        // except if a state has already been set.
                        $this->setExpanded($headername, true);
                    }
                } else if ($anyrequiredorerror) {
                    // If any error or required field are present within the header, we need to expand it.
                    $this->setExpanded($headername, true, true);
                } else if (!isset($this->_collapsibleElements[$headername])) {
                    // Define element as collapsed by default.
                    $this->setExpanded($headername, false);
                }
            }

            // Pass the array to renderer object.
            $renderer->setCollapsibleElements($this->_collapsibleElements);
        }
        parent::accept($renderer);
    }

    /**
     * Adds one or more element names that indicate the end of a fieldset
     *
     * @param string $elementName name of the element
     */
    function closeHeaderBefore($elementName){
        $renderer =& $this->defaultRenderer();
        $renderer->addStopFieldsetElements($elementName);
    }

    /**
     * Should be used for all elements of a form except for select, radio and checkboxes which
     * clean their own data.
     *
     * @param string $elementname
     * @param int $paramtype defines type of data contained in element. Use the constants PARAM_*.
     *        {@link lib/moodlelib.php} for defined parameter types
     */
    function setType($elementname, $paramtype) {
        $this->_types[$elementname] = $paramtype;
    }

    /**
     * This can be used to set several types at once.
     *
     * @param array $paramtypes types of parameters.
     * @see MoodleQuickForm::setType
     */
    function setTypes($paramtypes) {
        $this->_types = $paramtypes + $this->_types;
    }

    /**
     * Return the type(s) to use to clean an element.
     *
     * In the case where the element has an array as a value, we will try to obtain a
     * type defined for that specific key, and recursively until done.
     *
     * This method does not work reverse, you cannot pass a nested element and hoping to
     * fallback on the clean type of a parent. This method intends to be used with the
     * main element, which will generate child types if needed, not the other way around.
     *
     * Example scenario:
     *
     * You have defined a new repeated element containing a text field called 'foo'.
     * By default there will always be 2 occurence of 'foo' in the form. Even though
     * you've set the type on 'foo' to be PARAM_INT, for some obscure reason, you want
     * the first value of 'foo', to be PARAM_FLOAT, which you set using setType:
     * $mform->setType('foo[0]', PARAM_FLOAT).
     *
     * Now if you call this method passing 'foo', along with the submitted values of 'foo':
     * array(0 => '1.23', 1 => '10'), you will get an array telling you that the key 0 is a
     * FLOAT and 1 is an INT. If you had passed 'foo[1]', along with its value '10', you would
     * get the default clean type returned (param $default).
     *
     * @param string $elementname name of the element.
     * @param mixed $value value that should be cleaned.
     * @param int $default default constant value to be returned (PARAM_...)
     * @return string|array constant value or array of constant values (PARAM_...)
     */
    public function getCleanType($elementname, $value, $default = PARAM_RAW) {
        $type = $default;
        if (array_key_exists($elementname, $this->_types)) {
            $type = $this->_types[$elementname];
        }
        if (is_array($value)) {
            $default = $type;
            $type = array();
            foreach ($value as $subkey => $subvalue) {
                $typekey = "$elementname" . "[$subkey]";
                if (array_key_exists($typekey, $this->_types)) {
                    $subtype = $this->_types[$typekey];
                } else {
                    $subtype = $default;
                }
                if (is_array($subvalue)) {
                    $type[$subkey] = $this->getCleanType($typekey, $subvalue, $subtype);
                } else {
                    $type[$subkey] = $subtype;
                }
            }
        }
        return $type;
    }

    /**
     * Return the cleaned value using the passed type(s).
     *
     * @param mixed $value value that has to be cleaned.
     * @param int|array $type constant value to use to clean (PARAM_...), typically returned by {@link self::getCleanType()}.
     * @return mixed cleaned up value.
     */
    public function getCleanedValue($value, $type) {
        if (is_array($type) && is_array($value)) {
            foreach ($type as $key => $param) {
                $value[$key] = $this->getCleanedValue($value[$key], $param);
            }
        } else if (!is_array($type) && !is_array($value)) {
            $value = clean_param($value, $type);
        } else if (!is_array($type) && is_array($value)) {
            $value = clean_param_array($value, $type, true);
        } else {
            throw new coding_exception('Unexpected type or value received in MoodleQuickForm::getCleanedValue()');
        }
        return $value;
    }

    /**
     * Updates submitted values
     *
     * @param array $submission submitted values
     * @param array $files list of files
     */
    function updateSubmission($submission, $files) {
        $this->_flagSubmitted = false;

        if (empty($submission)) {
            $this->_submitValues = array();
        } else {
            foreach ($submission as $key => $s) {
                $type = $this->getCleanType($key, $s);
                $submission[$key] = $this->getCleanedValue($s, $type);
            }
            $this->_submitValues = $submission;
            $this->_flagSubmitted = true;
        }

        if (empty($files)) {
            $this->_submitFiles = array();
        } else {
            $this->_submitFiles = $files;
            $this->_flagSubmitted = true;
        }

        // need to tell all elements that they need to update their value attribute.
         foreach (array_keys($this->_elements) as $key) {
             $this->_elements[$key]->onQuickFormEvent('updateValue', null, $this);
         }
    }

    /**
     * Returns HTML for required elements
     *
     * @return string
     */
    function getReqHTML(){
        return $this->_reqHTML;
    }

    /**
     * Returns HTML for advanced elements
     *
     * @return string
     */
    function getAdvancedHTML(){
        return $this->_advancedHTML;
    }

    /**
     * Initializes a default form value. Used to specify the default for a new entry where
     * no data is loaded in using moodleform::set_data()
     *
     * note: $slashed param removed
     *
     * @param string $elementName element name
     * @param mixed $defaultValue values for that element name
     */
    function setDefault($elementName, $defaultValue){
        $this->setDefaults(array($elementName=>$defaultValue));
    }

    /**
     * Add a help button to element, only one button per element is allowed.
     *
     * This is new, simplified and preferable method of setting a help icon on form elements.
     * It uses the new $OUTPUT->help_icon().
     *
     * Typically, you will provide the same identifier and the component as you have used for the
     * label of the element. The string identifier with the _help suffix added is then used
     * as the help string.
     *
     * There has to be two strings defined:
     *   1/ get_string($identifier, $component) - the title of the help page
     *   2/ get_string($identifier.'_help', $component) - the actual help page text
     *
     * @since Moodle 2.0
     * @param string $elementname name of the element to add the item to
     * @param string $identifier help string identifier without _help suffix
     * @param string $component component name to look the help string in
     * @param string $linktext optional text to display next to the icon
     * @param bool $suppresscheck set to true if the element may not exist
     */
    function addHelpButton($elementname, $identifier, $component = 'moodle', $linktext = '', $suppresscheck = false) {
        global $OUTPUT;
        if (array_key_exists($elementname, $this->_elementIndex)) {
            $element = $this->_elements[$this->_elementIndex[$elementname]];
            $element->_helpbutton = $OUTPUT->help_icon($identifier, $component, $linktext);
        } else if (!$suppresscheck) {
            debugging(get_string('nonexistentformelements', 'form', $elementname));
        }
    }

    /**
     * Set constant value not overridden by _POST or _GET
     * note: this does not work for complex names with [] :-(
     *
     * @param string $elname name of element
     * @param mixed $value
     */
    function setConstant($elname, $value) {
        $this->_constantValues = HTML_QuickForm::arrayMerge($this->_constantValues, array($elname=>$value));
        $element =& $this->getElement($elname);
        $element->onQuickFormEvent('updateValue', null, $this);
    }

    /**
     * export submitted values
     *
     * @param string $elementList list of elements in form
     * @return array
     */
    function exportValues($elementList = null){
        $unfiltered = array();
        if (null === $elementList) {
            // iterate over all elements, calling their exportValue() methods
            foreach (array_keys($this->_elements) as $key) {
                if ($this->_elements[$key]->isFrozen() && !$this->_elements[$key]->_persistantFreeze) {
                    $varname = $this->_elements[$key]->_attributes['name'];
                    $value = '';
                    // If we have a default value then export it.
                    if (isset($this->_defaultValues[$varname])) {
                        $value = $this->prepare_fixed_value($varname, $this->_defaultValues[$varname]);
                    }
                } else {
                    $value = $this->_elements[$key]->exportValue($this->_submitValues, true);
                }

                if (is_array($value)) {
                    // This shit throws a bogus warning in PHP 4.3.x
                    $unfiltered = HTML_QuickForm::arrayMerge($unfiltered, $value);
                }
            }
        } else {
            if (!is_array($elementList)) {
                $elementList = array_map('trim', explode(',', $elementList));
            }
            foreach ($elementList as $elementName) {
                $value = $this->exportValue($elementName);
                if (@PEAR::isError($value)) {
                    return $value;
                }
                //oh, stock QuickFOrm was returning array of arrays!
                $unfiltered = HTML_QuickForm::arrayMerge($unfiltered, $value);
            }
        }

        if (is_array($this->_constantValues)) {
            $unfiltered = HTML_QuickForm::arrayMerge($unfiltered, $this->_constantValues);
        }
        return $unfiltered;
    }

    /**
     * This is a bit of a hack, and it duplicates the code in
     * HTML_QuickForm_element::_prepareValue, but I could not think of a way or
     * reliably calling that code. (Think about date selectors, for example.)
     * @param string $name the element name.
     * @param mixed $value the fixed value to set.
     * @return mixed the appropriate array to add to the $unfiltered array.
     */
    protected function prepare_fixed_value($name, $value) {
        if (null === $value) {
            return null;
        } else {
            if (!strpos($name, '[')) {
                return array($name => $value);
            } else {
                $valueAry = array();
                $myIndex  = "['" . str_replace(array(']', '['), array('', "']['"), $name) . "']";
                eval("\$valueAry$myIndex = \$value;");
                return $valueAry;
            }
        }
    }

    /**
     * Adds a validation rule for the given field
     *
     * If the element is in fact a group, it will be considered as a whole.
     * To validate grouped elements as separated entities,
     * use addGroupRule instead of addRule.
     *
     * @param string $element Form element name
     * @param string $message Message to display for invalid data
     * @param string $type Rule type, use getRegisteredRules() to get types
     * @param string $format (optional)Required for extra rule data
     * @param string $validation (optional)Where to perform validation: "server", "client"
     * @param bool $reset Client-side validation: reset the form element to its original value if there is an error?
     * @param bool $force Force the rule to be applied, even if the target form element does not exist
     */
    function addRule($element, $message, $type, $format=null, $validation='server', $reset = false, $force = false)
    {
        parent::addRule($element, $message, $type, $format, $validation, $reset, $force);
        if ($validation == 'client') {
            $this->updateAttributes(array('onsubmit' => 'try { var myValidator = validate_' . $this->_formName . '; } catch(e) { return true; } return myValidator(this);'));
        }

    }

    /**
     * Adds a validation rule for the given group of elements
     *
     * Only groups with a name can be assigned a validation rule
     * Use addGroupRule when you need to validate elements inside the group.
     * Use addRule if you need to validate the group as a whole. In this case,
     * the same rule will be applied to all elements in the group.
     * Use addRule if you need to validate the group against a function.
     *
     * @param string $group Form group name
     * @param array|string $arg1 Array for multiple elements or error message string for one element
     * @param string $type (optional)Rule type use getRegisteredRules() to get types
     * @param string $format (optional)Required for extra rule data
     * @param int $howmany (optional)How many valid elements should be in the group
     * @param string $validation (optional)Where to perform validation: "server", "client"
     * @param bool $reset Client-side: whether to reset the element's value to its original state if validation failed.
     */
    function addGroupRule($group, $arg1, $type='', $format=null, $howmany=0, $validation = 'server', $reset = false)
    {
        parent::addGroupRule($group, $arg1, $type, $format, $howmany, $validation, $reset);
        if (is_array($arg1)) {
             foreach ($arg1 as $rules) {
                foreach ($rules as $rule) {
                    $validation = (isset($rule[3]) && 'client' == $rule[3])? 'client': 'server';

                    if ('client' == $validation) {
                        $this->updateAttributes(array('onsubmit' => 'try { var myValidator = validate_' . $this->_formName . '; } catch(e) { return true; } return myValidator(this);'));
                    }
                }
            }
        } elseif (is_string($arg1)) {

            if ($validation == 'client') {
                $this->updateAttributes(array('onsubmit' => 'try { var myValidator = validate_' . $this->_formName . '; } catch(e) { return true; } return myValidator(this);'));
            }
        }
    }

    /**
     * Returns the client side validation script
     *
     * The code here was copied from HTML_QuickForm_DHTMLRulesTableless who copied it from  HTML_QuickForm
     * and slightly modified to run rules per-element
     * Needed to override this because of an error with client side validation of grouped elements.
     *
     * @return string Javascript to perform validation, empty string if no 'client' rules were added
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
                        foreach ($rule['dependent'] as $elName) {
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
                    //for editor element, [text] is appended to the name.
                    $fullelementname = $elementName;
                    if ($element->getType() == 'editor') {
                        $fullelementname .= '[text]';
                        //Add format to rule as moodleform check which format is supported by browser
                        //it is not set anywhere... So small hack to make sure we pass it down to quickform
                        if (is_null($rule['format'])) {
                            $rule['format'] = $element->getFormat();
                        }
                    }
                    // Fix for bug displaying errors for elements in a group
                    $test[$fullelementname][0][] = $registry->getValidationScript($element, $fullelementname, $rule);
                    $test[$fullelementname][1]=$element;
                    //end of fix
                }
            }
        }

        // Fix for MDL-9524. If you don't do this, then $element may be left as a reference to one of the fields in
        // the form, and then that form field gets corrupted by the code that follows.
        unset($element);

        $js = '
<script type="text/javascript">
//<![CDATA[

var skipClientValidation = false;

function qf_errorHandler(element, _qfMsg, escapedName) {
  div = element.parentNode;

  if ((div == undefined) || (element.name == undefined)) {
    //no checking can be done for undefined elements so let server handle it.
    return true;
  }

  if (_qfMsg != \'\') {
    var errorSpan = document.getElementById(\'id_error_\' + escapedName);
    if (!errorSpan) {
      errorSpan = document.createElement("span");
      errorSpan.id = \'id_error_\' + escapedName;
      errorSpan.className = "error";
      element.parentNode.insertBefore(errorSpan, element.parentNode.firstChild);
      document.getElementById(errorSpan.id).setAttribute(\'TabIndex\', \'0\');
      document.getElementById(errorSpan.id).focus();
    }

    while (errorSpan.firstChild) {
      errorSpan.removeChild(errorSpan.firstChild);
    }

    errorSpan.appendChild(document.createTextNode(_qfMsg.substring(3)));

    if (div.className.substr(div.className.length - 6, 6) != " error"
      && div.className != "error") {
        div.className += " error";
        linebreak = document.createElement("br");
        linebreak.className = "error";
        linebreak.id = \'id_error_break_\' + escapedName;
        errorSpan.parentNode.insertBefore(linebreak, errorSpan.nextSibling);
    }

    return false;
  } else {
    var errorSpan = document.getElementById(\'id_error_\' + escapedName);
    if (errorSpan) {
      errorSpan.parentNode.removeChild(errorSpan);
    }
    var linebreak = document.getElementById(\'id_error_break_\' + escapedName);
    if (linebreak) {
      linebreak.parentNode.removeChild(linebreak);
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
            $escapedElementName = preg_replace_callback(
                '/[_\[\]-]/',
                create_function('$matches', 'return sprintf("_%2x",ord($matches[0]));'),
                $elementName);
            $js .= '
function validate_' . $this->_formName . '_' . $escapedElementName . '(element, escapedName) {
  if (undefined == element) {
     //required element was not found, then let form be submitted without client side validation
     return true;
  }
  var value = \'\';
  var errFlag = new Array();
  var _qfGroups = {};
  var _qfMsg = \'\';
  var frm = element.parentNode;
  if ((undefined != element.name) && (frm != undefined)) {
      while (frm && frm.nodeName.toUpperCase() != "FORM") {
        frm = frm.parentNode;
      }
    ' . join("\n", $jsArr) . '
      return qf_errorHandler(element, _qfMsg, escapedName);
  } else {
    //element name should be defined else error msg will not be displayed.
    return true;
  }
}
';
            $validateJS .= '
  ret = validate_' . $this->_formName . '_' . $escapedElementName.'(frm.elements[\''.$elementName.'\'], \''.$escapedElementName.'\') && ret;
  if (!ret && !first_focus) {
    first_focus = true;
    Y.use(\'moodle-core-event\', function() {
        Y.Global.fire(M.core.globalEvents.FORM_ERROR, {formid: \'' . $this->_attributes['id'] . '\',
                                                       elementid: \'id_error_' . $escapedElementName . '\'});
        document.getElementById(\'id_error_' . $escapedElementName . '\').focus();
    });
  }
';

            // Fix for bug displaying errors for elements in a group
            //unset($element);
            //$element =& $this->getElement($elementName);
            //end of fix
            $valFunc = 'validate_' . $this->_formName . '_' . $escapedElementName . '(this, \''.$escapedElementName.'\')';
            $onBlur = $element->getAttribute('onBlur');
            $onChange = $element->getAttribute('onChange');
            $element->updateAttributes(array('onBlur' => $onBlur . $valFunc,
                                             'onChange' => $onChange . $valFunc));
        }
//  do not rely on frm function parameter, because htmlarea breaks it when overloading the onsubmit method
        $js .= '
function validate_' . $this->_formName . '(frm) {
  if (skipClientValidation) {
     return true;
  }
  var ret = true;

  var frm = document.getElementById(\''. $this->_attributes['id'] .'\')
  var first_focus = false;
' . $validateJS . ';
  return ret;
}
//]]>
</script>';
        return $js;
    } // end func getValidationScript

    /**
     * Sets default error message
     */
    function _setDefaultRuleMessages(){
        foreach ($this->_rules as $field => $rulesarr){
            foreach ($rulesarr as $key => $rule){
                if ($rule['message']===null){
                    $a=new stdClass();
                    $a->format=$rule['format'];
                    $str=get_string('err_'.$rule['type'], 'form', $a);
                    if (strpos($str, '[[')!==0){
                        $this->_rules[$field][$key]['message']=$str;
                    }
                }
            }
        }
    }

    /**
     * Get list of attributes which have dependencies
     *
     * @return array
     */
    function getLockOptionObject(){
        $result = array();
        foreach ($this->_dependencies as $dependentOn => $conditions){
            $result[$dependentOn] = array();
            foreach ($conditions as $condition=>$values) {
                $result[$dependentOn][$condition] = array();
                foreach ($values as $value=>$dependents) {
                    $result[$dependentOn][$condition][$value] = array();
                    $i = 0;
                    foreach ($dependents as $dependent) {
                        $elements = $this->_getElNamesRecursive($dependent);
                        if (empty($elements)) {
                            // probably element inside of some group
                            $elements = array($dependent);
                        }
                        foreach($elements as $element) {
                            if ($element == $dependentOn) {
                                continue;
                            }
                            $result[$dependentOn][$condition][$value][] = $element;
                        }
                    }
                }
            }
        }
        return array($this->getAttribute('id'), $result);
    }

    /**
     * Get names of element or elements in a group.
     *
     * @param HTML_QuickForm_group|element $element element group or element object
     * @return array
     */
    function _getElNamesRecursive($element) {
        if (is_string($element)) {
            if (!$this->elementExists($element)) {
                return array();
            }
            $element = $this->getElement($element);
        }

        if (is_a($element, 'HTML_QuickForm_group')) {
            $elsInGroup = $element->getElements();
            $elNames = array();
            foreach ($elsInGroup as $elInGroup){
                if (is_a($elInGroup, 'HTML_QuickForm_group')) {
                    // not sure if this would work - groups nested in groups
                    $elNames = array_merge($elNames, $this->_getElNamesRecursive($elInGroup));
                } else {
                    $elNames[] = $element->getElementName($elInGroup->getName());
                }
            }

        } else if (is_a($element, 'HTML_QuickForm_header')) {
            return array();

        } else if (is_a($element, 'HTML_QuickForm_hidden')) {
            return array();

        } else if (method_exists($element, 'getPrivateName') &&
                !($element instanceof HTML_QuickForm_advcheckbox)) {
            // The advcheckbox element implements a method called getPrivateName,
            // but in a way that is not compatible with the generic API, so we
            // have to explicitly exclude it.
            return array($element->getPrivateName());

        } else {
            $elNames = array($element->getName());
        }

        return $elNames;
    }

    /**
     * Adds a dependency for $elementName which will be disabled if $condition is met.
     * If $condition = 'notchecked' (default) then the condition is that the $dependentOn element
     * is not checked. If $condition = 'checked' then the condition is that the $dependentOn element
     * is checked. If $condition is something else (like "eq" for equals) then it is checked to see if the value
     * of the $dependentOn element is $condition (such as equal) to $value.
     *
     * When working with multiple selects, the dependentOn has to be the real name of the select, meaning that
     * it will most likely end up with '[]'. Also, the value should be an array of required values, or a string
     * containing the values separated by pipes: array('red', 'blue') or 'red|blue'.
     *
     * @param string $elementName the name of the element which will be disabled
     * @param string $dependentOn the name of the element whose state will be checked for condition
     * @param string $condition the condition to check
     * @param mixed $value used in conjunction with condition.
     */
    function disabledIf($elementName, $dependentOn, $condition = 'notchecked', $value='1') {
        // Multiple selects allow for a multiple selection, we transform the array to string here as
        // an array cannot be used as a key in an associative array.
        if (is_array($value)) {
            $value = implode('|', $value);
        }
        if (!array_key_exists($dependentOn, $this->_dependencies)) {
            $this->_dependencies[$dependentOn] = array();
        }
        if (!array_key_exists($condition, $this->_dependencies[$dependentOn])) {
            $this->_dependencies[$dependentOn][$condition] = array();
        }
        if (!array_key_exists($value, $this->_dependencies[$dependentOn][$condition])) {
            $this->_dependencies[$dependentOn][$condition][$value] = array();
        }
        $this->_dependencies[$dependentOn][$condition][$value][] = $elementName;
    }

    /**
     * Registers button as no submit button
     *
     * @param string $buttonname name of the button
     */
    function registerNoSubmitButton($buttonname){
        $this->_noSubmitButtons[]=$buttonname;
    }

    /**
     * Checks if button is a no submit button, i.e it doesn't submit form
     *
     * @param string $buttonname name of the button to check
     * @return bool
     */
    function isNoSubmitButton($buttonname){
        return (array_search($buttonname, $this->_noSubmitButtons)!==FALSE);
    }

    /**
     * Registers a button as cancel button
     *
     * @param string $addfieldsname name of the button
     */
    function _registerCancelButton($addfieldsname){
        $this->_cancelButtons[]=$addfieldsname;
    }

    /**
     * Displays elements without HTML input tags.
     * This method is different to freeze() in that it makes sure no hidden
     * elements are included in the form.
     * Note: If you want to make sure the submitted value is ignored, please use setDefaults().
     *
     * This function also removes all previously defined rules.
     *
     * @param string|array $elementList array or string of element(s) to be frozen
     * @return object|bool if element list is not empty then return error object, else true
     */
    function hardFreeze($elementList=null)
    {
        if (!isset($elementList)) {
            $this->_freezeAll = true;
            $elementList = array();
        } else {
            if (!is_array($elementList)) {
                $elementList = preg_split('/[ ]*,[ ]*/', $elementList);
            }
            $elementList = array_flip($elementList);
        }

        foreach (array_keys($this->_elements) as $key) {
            $name = $this->_elements[$key]->getName();
            if ($this->_freezeAll || isset($elementList[$name])) {
                $this->_elements[$key]->freeze();
                $this->_elements[$key]->setPersistantFreeze(false);
                unset($elementList[$name]);

                // remove all rules
                $this->_rules[$name] = array();
                // if field is required, remove the rule
                $unset = array_search($name, $this->_required);
                if ($unset !== false) {
                    unset($this->_required[$unset]);
                }
            }
        }

        if (!empty($elementList)) {
            return self::raiseError(null, QUICKFORM_NONEXIST_ELEMENT, null, E_USER_WARNING, "Nonexistant element(s): '" . implode("', '", array_keys($elementList)) . "' in HTML_QuickForm::freeze()", 'HTML_QuickForm_Error', true);
        }
        return true;
    }

    /**
     * Hard freeze all elements in a form except those whose names are in $elementList or hidden elements in a form.
     *
     * This function also removes all previously defined rules of elements it freezes.
     *
     * @throws HTML_QuickForm_Error
     * @param array $elementList array or string of element(s) not to be frozen
     * @return bool returns true
     */
    function hardFreezeAllVisibleExcept($elementList)
    {
        $elementList = array_flip($elementList);
        foreach (array_keys($this->_elements) as $key) {
            $name = $this->_elements[$key]->getName();
            $type = $this->_elements[$key]->getType();

            if ($type == 'hidden'){
                // leave hidden types as they are
            } elseif (!isset($elementList[$name])) {
                $this->_elements[$key]->freeze();
                $this->_elements[$key]->setPersistantFreeze(false);

                // remove all rules
                $this->_rules[$name] = array();
                // if field is required, remove the rule
                $unset = array_search($name, $this->_required);
                if ($unset !== false) {
                    unset($this->_required[$unset]);
                }
            }
        }
        return true;
    }

   /**
    * Tells whether the form was already submitted
    *
    * This is useful since the _submitFiles and _submitValues arrays
    * may be completely empty after the trackSubmit value is removed.
    *
    * @return bool
    */
    function isSubmitted()
    {
        return parent::isSubmitted() && (!$this->isFrozen());
    }
}

/**
 * MoodleQuickForm renderer
 *
 * A renderer for MoodleQuickForm that only uses XHTML and CSS and no
 * table tags, extends PEAR class HTML_QuickForm_Renderer_Tableless
 *
 * Stylesheet is part of standard theme and should be automatically included.
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_Renderer extends HTML_QuickForm_Renderer_Tableless{

    /** @var array Element template array */
    var $_elementTemplates;

    /**
     * Template used when opening a hidden fieldset
     * (i.e. a fieldset that is opened when there is no header element)
     * @var string
     */
    var $_openHiddenFieldsetTemplate = "\n\t<fieldset class=\"hidden\"><div>";

    /** @var string Header Template string */
    var $_headerTemplate =
       "\n\t\t<legend class=\"ftoggler\">{header}</legend>\n\t\t<div class=\"fcontainer clearfix\">\n\t\t";

    /** @var string Template used when opening a fieldset */
    var $_openFieldsetTemplate = "\n\t<fieldset class=\"{classes}\" {id}>";

    /** @var string Template used when closing a fieldset */
    var $_closeFieldsetTemplate = "\n\t\t</div></fieldset>";

    /** @var string Required Note template string */
    var $_requiredNoteTemplate =
        "\n\t\t<div class=\"fdescription required\">{requiredNote}</div>";

    /**
     * Collapsible buttons string template.
     *
     * Note that the <span> will be converted as a link. This is done so that the link is not yet clickable
     * until the Javascript has been fully loaded.
     *
     * @var string
     */
    var $_collapseButtonsTemplate =
        "\n\t<div class=\"collapsible-actions\"><span class=\"collapseexpand\">{strexpandall}</span></div>";

    /**
     * Array whose keys are element names. If the key exists this is a advanced element
     *
     * @var array
     */
    var $_advancedElements = array();

    /**
     * Array whose keys are element names and the the boolean values reflect the current state. If the key exists this is a collapsible element.
     *
     * @var array
     */
    var $_collapsibleElements = array();

    /**
     * @var string Contains the collapsible buttons to add to the form.
     */
    var $_collapseButtons = '';

    /**
     * Constructor
     */
    public function __construct() {
        // switch next two lines for ol li containers for form items.
        //        $this->_elementTemplates=array('default'=>"\n\t\t".'<li class="fitem"><label>{label}{help}<!-- BEGIN required -->{req}<!-- END required --></label><div class="qfelement<!-- BEGIN error --> error<!-- END error --> {type}"><!-- BEGIN error --><span class="error">{error}</span><br /><!-- END error -->{element}</div></li>');
        $this->_elementTemplates = array(
        'default'=>"\n\t\t".'<div id="{id}" class="fitem {advanced}<!-- BEGIN required --> required<!-- END required --> fitem_{type} {emptylabel}" {aria-live}><div class="fitemtitle"><label>{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg} </label>{help}</div><div class="felement {type}<!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error" tabindex="0">{error}</span><br /><!-- END error -->{element}</div></div>',

        'actionbuttons'=>"\n\t\t".'<div id="{id}" class="fitem fitem_actionbuttons fitem_{type}"><div class="felement {type}">{element}</div></div>',

        'fieldset'=>"\n\t\t".'<div id="{id}" class="fitem {advanced}<!-- BEGIN required --> required<!-- END required --> fitem_{type} {emptylabel}"><div class="fitemtitle"><div class="fgrouplabel"><label>{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg} </label>{help}</div></div><fieldset class="felement {type}<!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error" tabindex="0">{error}</span><br /><!-- END error -->{element}</fieldset></div>',

        'static'=>"\n\t\t".'<div class="fitem {advanced} {emptylabel}"><div class="fitemtitle"><div class="fstaticlabel">{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg} {help}</div></div><div class="felement fstatic <!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error" tabindex="0">{error}</span><br /><!-- END error -->{element}</div></div>',

        'warning'=>"\n\t\t".'<div class="fitem {advanced} {emptylabel}">{element}</div>',

        'nodisplay'=>'');

        parent::__construct();
    }

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function MoodleQuickForm_Renderer() {
        self::__construct();
    }

    /**
     * Set element's as adavance element
     *
     * @param array $elements form elements which needs to be grouped as advance elements.
     */
    function setAdvancedElements($elements){
        $this->_advancedElements = $elements;
    }

    /**
     * Setting collapsible elements
     *
     * @param array $elements
     */
    function setCollapsibleElements($elements) {
        $this->_collapsibleElements = $elements;
    }

    /**
     * What to do when starting the form
     *
     * @param MoodleQuickForm $form reference of the form
     */
    function startForm(&$form){
        global $PAGE;
        $this->_reqHTML = $form->getReqHTML();
        $this->_elementTemplates = str_replace('{req}', $this->_reqHTML, $this->_elementTemplates);
        $this->_advancedHTML = $form->getAdvancedHTML();
        $this->_collapseButtons = '';
        $formid = $form->getAttribute('id');
        parent::startForm($form);
        // HACK to prevent browsers from automatically inserting the user's password into the wrong fields.
        $this->_hiddenHtml .= prevent_form_autofill_password();
        if ($form->isFrozen()){
            $this->_formTemplate = "\n<div class=\"mform frozen\">\n{content}\n</div>";
        } else {
            $this->_formTemplate = "\n<form{attributes}>\n\t<div style=\"display: none;\">{hidden}</div>\n{collapsebtns}\n{content}\n</form>";
            $this->_hiddenHtml .= $form->_pageparams;
        }

        if ($form->is_form_change_checker_enabled()) {
            $PAGE->requires->yui_module('moodle-core-formchangechecker',
                    'M.core_formchangechecker.init',
                    array(array(
                        'formid' => $formid
                    ))
            );
            $PAGE->requires->string_for_js('changesmadereallygoaway', 'moodle');
        }
        if (!empty($this->_collapsibleElements)) {
            if (count($this->_collapsibleElements) > 1) {
                $this->_collapseButtons = $this->_collapseButtonsTemplate;
                $this->_collapseButtons = str_replace('{strexpandall}', get_string('expandall'), $this->_collapseButtons);
                $PAGE->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
            }
            $PAGE->requires->yui_module('moodle-form-shortforms', 'M.form.shortforms', array(array('formid' => $formid)));
        }
        if (!empty($this->_advancedElements)){
            $PAGE->requires->strings_for_js(array('showmore', 'showless'), 'form');
            $PAGE->requires->yui_module('moodle-form-showadvanced', 'M.form.showadvanced', array(array('formid' => $formid)));
        }
    }

    /**
     * Create advance group of elements
     *
     * @param object $group Passed by reference
     * @param bool $required if input is required field
     * @param string $error error message to display
     */
    function startGroup(&$group, $required, $error){
        // Make sure the element has an id.
        $group->_generateId();

        if (method_exists($group, 'getElementTemplateType')){
            $html = $this->_elementTemplates[$group->getElementTemplateType()];
        }else{
            $html = $this->_elementTemplates['default'];

        }

        if (isset($this->_advancedElements[$group->getName()])){
            $html =str_replace(' {advanced}', ' advanced', $html);
            $html =str_replace('{advancedimg}', $this->_advancedHTML, $html);
        } else {
            $html =str_replace(' {advanced}', '', $html);
            $html =str_replace('{advancedimg}', '', $html);
        }
        if (method_exists($group, 'getHelpButton')){
            $html =str_replace('{help}', $group->getHelpButton(), $html);
        }else{
            $html =str_replace('{help}', '', $html);
        }
        $html =str_replace('{id}', 'fgroup_' . $group->getAttribute('id'), $html);
        $html =str_replace('{name}', $group->getName(), $html);
        $html =str_replace('{type}', 'fgroup', $html);
        $emptylabel = '';
        if ($group->getLabel() == '') {
            $emptylabel = 'femptylabel';
        }
        $html = str_replace('{emptylabel}', $emptylabel, $html);

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

    /**
     * Renders element
     *
     * @param HTML_QuickForm_element $element element
     * @param bool $required if input is required field
     * @param string $error error message to display
     */
    function renderElement(&$element, $required, $error){
        // Make sure the element has an id.
        $element->_generateId();

        //adding stuff to place holders in template
        //check if this is a group element first
        if (($this->_inGroup) and !empty($this->_groupElementTemplate)) {
            // so it gets substitutions for *each* element
            $html = $this->_groupElementTemplate;
        }
        elseif (method_exists($element, 'getElementTemplateType')){
            $html = $this->_elementTemplates[$element->getElementTemplateType()];
        }else{
            $html = $this->_elementTemplates['default'];
        }
        if (isset($this->_advancedElements[$element->getName()])){
            $html = str_replace(' {advanced}', ' advanced', $html);
            $html = str_replace(' {aria-live}', ' aria-live="polite"', $html);
        } else {
            $html = str_replace(' {advanced}', '', $html);
            $html = str_replace(' {aria-live}', '', $html);
        }
        if (isset($this->_advancedElements[$element->getName()])||$element->getName() == 'mform_showadvanced'){
            $html =str_replace('{advancedimg}', $this->_advancedHTML, $html);
        } else {
            $html =str_replace('{advancedimg}', '', $html);
        }
        $html =str_replace('{id}', 'fitem_' . $element->getAttribute('id'), $html);
        $html =str_replace('{type}', 'f'.$element->getType(), $html);
        $html =str_replace('{name}', $element->getName(), $html);
        $emptylabel = '';
        if ($element->getLabel() == '') {
            $emptylabel = 'femptylabel';
        }
        $html = str_replace('{emptylabel}', $emptylabel, $html);
        if (method_exists($element, 'getHelpButton')){
            $html = str_replace('{help}', $element->getHelpButton(), $html);
        }else{
            $html = str_replace('{help}', '', $html);

        }
        if (($this->_inGroup) and !empty($this->_groupElementTemplate)) {
            $this->_groupElementTemplate = $html;
        }
        elseif (!isset($this->_templates[$element->getName()])) {
            $this->_templates[$element->getName()] = $html;
        }

        parent::renderElement($element, $required, $error);
    }

    /**
     * Called when visiting a form, after processing all form elements
     * Adds required note, form attributes, validation javascript and form content.
     *
     * @global moodle_page $PAGE
     * @param moodleform $form Passed by reference
     */
    function finishForm(&$form){
        global $PAGE;
        if ($form->isFrozen()){
            $this->_hiddenHtml = '';
        }
        parent::finishForm($form);
        $this->_html = str_replace('{collapsebtns}', $this->_collapseButtons, $this->_html);
        if (!$form->isFrozen()) {
            $args = $form->getLockOptionObject();
            if (count($args[1]) > 0) {
                $PAGE->requires->js_init_call('M.form.initFormDependencies', $args, true, moodleform::get_js_module());
            }
        }
    }
   /**
    * Called when visiting a header element
    *
    * @param HTML_QuickForm_header $header An HTML_QuickForm_header element being visited
    * @global moodle_page $PAGE
    */
    function renderHeader(&$header) {
        global $PAGE;

        $header->_generateId();
        $name = $header->getName();

        $id = empty($name) ? '' : ' id="' . $header->getAttribute('id') . '"';
        if (is_null($header->_text)) {
            $header_html = '';
        } elseif (!empty($name) && isset($this->_templates[$name])) {
            $header_html = str_replace('{header}', $header->toHtml(), $this->_templates[$name]);
        } else {
            $header_html = str_replace('{header}', $header->toHtml(), $this->_headerTemplate);
        }

        if ($this->_fieldsetsOpen > 0) {
            $this->_html .= $this->_closeFieldsetTemplate;
            $this->_fieldsetsOpen--;
        }

        // Define collapsible classes for fieldsets.
        $arialive = '';
        $fieldsetclasses = array('clearfix');
        if (isset($this->_collapsibleElements[$header->getName()])) {
            $fieldsetclasses[] = 'collapsible';
            if ($this->_collapsibleElements[$header->getName()]) {
                $fieldsetclasses[] = 'collapsed';
            }
        }

        if (isset($this->_advancedElements[$name])){
            $fieldsetclasses[] = 'containsadvancedelements';
        }

        $openFieldsetTemplate = str_replace('{id}', $id, $this->_openFieldsetTemplate);
        $openFieldsetTemplate = str_replace('{classes}', join(' ', $fieldsetclasses), $openFieldsetTemplate);

        $this->_html .= $openFieldsetTemplate . $header_html;
        $this->_fieldsetsOpen++;
    }

    /**
     * Return Array of element names that indicate the end of a fieldset
     *
     * @return array
     */
    function getStopFieldsetElements(){
        return $this->_stopFieldsetElements;
    }
}

/**
 * Required elements validation
 *
 * This class overrides QuickForm validation since it allowed space or empty tag as a value
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_Rule_Required extends HTML_QuickForm_Rule {
    /**
     * Checks if an element is not empty.
     * This is a server-side validation, it works for both text fields and editor fields
     *
     * @param string $value Value to check
     * @param int|string|array $options Not used yet
     * @return bool true if value is not empty
     */
    function validate($value, $options = null) {
        global $CFG;
        if (is_array($value) && array_key_exists('text', $value)) {
            $value = $value['text'];
        }
        if (is_array($value)) {
            // nasty guess - there has to be something in the array, hopefully nobody invents arrays in arrays
            $value = implode('', $value);
        }
        $stripvalues = array(
            '#</?(?!img|canvas|hr).*?>#im', // all tags except img, canvas and hr
            '#(\xc2\xa0|\s|&nbsp;)#', // Any whitespaces actually.
        );
        if (!empty($CFG->strictformsrequired)) {
            $value = preg_replace($stripvalues, '', (string)$value);
        }
        if ((string)$value == '') {
            return false;
        }
        return true;
    }

    /**
     * This function returns Javascript code used to build client-side validation.
     * It checks if an element is not empty.
     *
     * @param int $format format of data which needs to be validated.
     * @return array
     */
    function getValidationScript($format = null) {
        global $CFG;
        if (!empty($CFG->strictformsrequired)) {
            if (!empty($format) && $format == FORMAT_HTML) {
                return array('', "{jsVar}.replace(/(<(?!img|hr|canvas)[^>]*>)|&nbsp;|\s+/ig, '') == ''");
            } else {
                return array('', "{jsVar}.replace(/^\s+$/g, '') == ''");
            }
        } else {
            return array('', "{jsVar} == ''");
        }
    }
}

/**
 * @global object $GLOBALS['_HTML_QuickForm_default_renderer']
 * @name $_HTML_QuickForm_default_renderer
 */
$GLOBALS['_HTML_QuickForm_default_renderer'] = new MoodleQuickForm_Renderer();

/** Please keep this list in alphabetical order. */
MoodleQuickForm::registerElementType('advcheckbox', "$CFG->libdir/form/advcheckbox.php", 'MoodleQuickForm_advcheckbox');
MoodleQuickForm::registerElementType('autocomplete', "$CFG->libdir/form/autocomplete.php", 'MoodleQuickForm_autocomplete');
MoodleQuickForm::registerElementType('button', "$CFG->libdir/form/button.php", 'MoodleQuickForm_button');
MoodleQuickForm::registerElementType('cancel', "$CFG->libdir/form/cancel.php", 'MoodleQuickForm_cancel');
MoodleQuickForm::registerElementType('searchableselector', "$CFG->libdir/form/searchableselector.php", 'MoodleQuickForm_searchableselector');
MoodleQuickForm::registerElementType('checkbox', "$CFG->libdir/form/checkbox.php", 'MoodleQuickForm_checkbox');
MoodleQuickForm::registerElementType('date_selector', "$CFG->libdir/form/dateselector.php", 'MoodleQuickForm_date_selector');
MoodleQuickForm::registerElementType('date_time_selector', "$CFG->libdir/form/datetimeselector.php", 'MoodleQuickForm_date_time_selector');
MoodleQuickForm::registerElementType('duration', "$CFG->libdir/form/duration.php", 'MoodleQuickForm_duration');
MoodleQuickForm::registerElementType('editor', "$CFG->libdir/form/editor.php", 'MoodleQuickForm_editor');
MoodleQuickForm::registerElementType('filemanager', "$CFG->libdir/form/filemanager.php", 'MoodleQuickForm_filemanager');
MoodleQuickForm::registerElementType('filepicker', "$CFG->libdir/form/filepicker.php", 'MoodleQuickForm_filepicker');
MoodleQuickForm::registerElementType('grading', "$CFG->libdir/form/grading.php", 'MoodleQuickForm_grading');
MoodleQuickForm::registerElementType('group', "$CFG->libdir/form/group.php", 'MoodleQuickForm_group');
MoodleQuickForm::registerElementType('header', "$CFG->libdir/form/header.php", 'MoodleQuickForm_header');
MoodleQuickForm::registerElementType('hidden', "$CFG->libdir/form/hidden.php", 'MoodleQuickForm_hidden');
MoodleQuickForm::registerElementType('htmleditor', "$CFG->libdir/form/htmleditor.php", 'MoodleQuickForm_htmleditor');
MoodleQuickForm::registerElementType('listing', "$CFG->libdir/form/listing.php", 'MoodleQuickForm_listing');
MoodleQuickForm::registerElementType('modgrade', "$CFG->libdir/form/modgrade.php", 'MoodleQuickForm_modgrade');
MoodleQuickForm::registerElementType('modvisible', "$CFG->libdir/form/modvisible.php", 'MoodleQuickForm_modvisible');
MoodleQuickForm::registerElementType('password', "$CFG->libdir/form/password.php", 'MoodleQuickForm_password');
MoodleQuickForm::registerElementType('passwordunmask', "$CFG->libdir/form/passwordunmask.php", 'MoodleQuickForm_passwordunmask');
MoodleQuickForm::registerElementType('questioncategory', "$CFG->libdir/form/questioncategory.php", 'MoodleQuickForm_questioncategory');
MoodleQuickForm::registerElementType('radio', "$CFG->libdir/form/radio.php", 'MoodleQuickForm_radio');
MoodleQuickForm::registerElementType('recaptcha', "$CFG->libdir/form/recaptcha.php", 'MoodleQuickForm_recaptcha');
MoodleQuickForm::registerElementType('select', "$CFG->libdir/form/select.php", 'MoodleQuickForm_select');
MoodleQuickForm::registerElementType('selectgroups', "$CFG->libdir/form/selectgroups.php", 'MoodleQuickForm_selectgroups');
MoodleQuickForm::registerElementType('selectwithlink', "$CFG->libdir/form/selectwithlink.php", 'MoodleQuickForm_selectwithlink');
MoodleQuickForm::registerElementType('selectyesno', "$CFG->libdir/form/selectyesno.php", 'MoodleQuickForm_selectyesno');
MoodleQuickForm::registerElementType('static', "$CFG->libdir/form/static.php", 'MoodleQuickForm_static');
MoodleQuickForm::registerElementType('submit', "$CFG->libdir/form/submit.php", 'MoodleQuickForm_submit');
MoodleQuickForm::registerElementType('submitlink', "$CFG->libdir/form/submitlink.php", 'MoodleQuickForm_submitlink');
MoodleQuickForm::registerElementType('tags', "$CFG->libdir/form/tags.php", 'MoodleQuickForm_tags');
MoodleQuickForm::registerElementType('text', "$CFG->libdir/form/text.php", 'MoodleQuickForm_text');
MoodleQuickForm::registerElementType('textarea', "$CFG->libdir/form/textarea.php", 'MoodleQuickForm_textarea');
MoodleQuickForm::registerElementType('url', "$CFG->libdir/form/url.php", 'MoodleQuickForm_url');
MoodleQuickForm::registerElementType('warning', "$CFG->libdir/form/warning.php", 'MoodleQuickForm_warning');

MoodleQuickForm::registerRule('required', null, 'MoodleQuickForm_Rule_Required', "$CFG->libdir/formslib.php");
