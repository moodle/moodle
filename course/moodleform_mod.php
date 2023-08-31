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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->libdir.'/plagiarismlib.php');

use core_grades\component_gradeitems;


define('LTI_URL_DOMAIN_REGEX', '/(?:https?:\/\/)?(?:www\.)?([^\/]+)(?:\/|$)/i');

define('LTI_LAUNCH_CONTAINER_DEFAULT', 1);
define('LTI_LAUNCH_CONTAINER_EMBED', 2);
define('LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS', 3);
define('LTI_LAUNCH_CONTAINER_WINDOW', 4);
define('LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW', 5);

define('LTI_TOOL_STATE_ANY', 0);
define('LTI_TOOL_STATE_CONFIGURED', 1);
define('LTI_TOOL_STATE_PENDING', 2);
define('LTI_TOOL_STATE_REJECTED', 3);
define('LTI_TOOL_PROXY_TAB', 4);

define('LTI_TOOL_PROXY_STATE_CONFIGURED', 1);
define('LTI_TOOL_PROXY_STATE_PENDING', 2);
define('LTI_TOOL_PROXY_STATE_ACCEPTED', 3);
define('LTI_TOOL_PROXY_STATE_REJECTED', 4);

define('LTI_SETTING_NEVER', 0);
define('LTI_SETTING_ALWAYS', 1);
define('LTI_SETTING_DELEGATE', 2);

define('LTI_COURSEVISIBLE_NO', 0);
define('LTI_COURSEVISIBLE_PRECONFIGURED', 1);
define('LTI_COURSEVISIBLE_ACTIVITYCHOOSER', 2);

define('LTI_VERSION_1', 'LTI-1p0');
define('LTI_VERSION_2', 'LTI-2p0');
define('LTI_VERSION_1P3', '1.3.0');
define('LTI_RSA_KEY', 'RSA_KEY');
define('LTI_JWK_KEYSET', 'JWK_KEYSET');

define('LTI_DEFAULT_ORGID_SITEID', 'SITEID');
define('LTI_DEFAULT_ORGID_SITEHOST', 'SITEHOST');

define('LTI_ACCESS_TOKEN_LIFE', 3600);

// Standard prefix for JWT claims.
define('LTI_JWT_CLAIM_PREFIX', 'https://purl.imsglobal.org/spec/lti');


/**
 * This class adds extra methods to form wrapper specific to be used for module add / update forms
 * mod/{modname}/mod_form.php replaced deprecated mod/{modname}/mod.html Moodleform.
 *
 * @package   core_course
 * @copyright Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class moodleform_mod extends moodleform {

    use \core_completion\form\form_trait;

    /** Current data */
    protected $current;
    /**
     * Instance of the module that is being updated. This is the id of the {prefix}{modulename}
     * record. Can be used in form definition. Will be "" if this is an 'add' form and not an
     * update one.
     *
     * @var mixed
     */
    protected $_instance;
    /**
     * Section of course that module instance will be put in or is in.
     * This is always the section number itself (column 'section' from 'course_sections' table).
     *
     * @var int
     */
    protected $_section;
    /**
     * Course module record of the module that is being updated. Will be null if this is an 'add' form and not an
     * update one.
      *
     * @var mixed
     */
    protected $_cm;

    /**
     * Current course.
     *
     * @var mixed
     */
    protected $_course;

    /**
     * List of modform features
     */
    protected $_features;
    /**
     * @var array Custom completion-rule elements, if enabled
     */
    protected $_customcompletionelements;
    /**
     * @var string name of module.
     */
    protected $_modname;
    /** current context, course or module depends if already exists*/
    protected $context;

    /** a flag indicating whether outcomes are being used*/
    protected $_outcomesused;

    /**
     * @var bool A flag used to indicate that this module should lock settings
     *           based on admin settings flags in definition_after_data.
     */
    protected $applyadminlockedflags = false;

    /** @var object The course format of the current course. */
    protected $courseformat;

    /** @var string Whether this is graded or rated. */
    private $gradedorrated = null;

    public function __construct($current, $section, $cm, $course) {
        global $CFG;

        $this->current   = $current;
        $this->_instance = $current->instance;
        $this->_section  = $section;
        $this->_cm       = $cm;
        $this->_course   = $course;
        if ($this->_cm) {
            $this->context = context_module::instance($this->_cm->id);
        } else {
            $this->context = context_course::instance($course->id);
        }

        // Set the course format.
        require_once($CFG->dirroot . '/course/format/lib.php');
        $this->courseformat = course_get_format($course);

        // Guess module name if not set.
        if (is_null($this->_modname)) {
            $matches = array();
            if (!preg_match('/^mod_([^_]+)_mod_form$/', get_class($this), $matches)) {
                debugging('Rename form to mod_xx_mod_form, where xx is name of your module');
                throw new \moodle_exception('unknownmodulename');
            }
            $this->_modname = $matches[1];
        }
        $this->init_features();
        parent::__construct('modedit.php');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function moodleform_mod($current, $section, $cm, $course) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($current, $section, $cm, $course);
    }

    /**
     * Get the current data for the form.
     * @return stdClass|null
     */
    public function get_current() {
        return $this->current;
    }

    /**
     * Get the DB record for the current instance.
     * @return stdClass|null
     */
    public function get_instance() {
        return $this->_instance;
    }

    /**
     * Get the course section number (relative).
     * @return int
     */
    public function get_section() {
        return $this->_section;
    }

    /**
     * Get the course id.
     * @return int
     */
    public function get_course() {
        return $this->_course;
    }

    /**
     * Get the course module object.
     * @return stdClass|null
     */
    public function get_coursemodule() {
        return $this->_cm;
    }

    /**
     * Return the course context for new modules, or the module context for existing modules.
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Return the features this module supports.
     * @return stdClass
     */
    public function get_features() {
        return $this->_features;
    }

    protected function init_features() {
        global $CFG;

        $this->_features = new stdClass();
        $this->_features->groups            = plugin_supports('mod', $this->_modname, FEATURE_GROUPS, false);
        $this->_features->groupings         = plugin_supports('mod', $this->_modname, FEATURE_GROUPINGS, false);
        $this->_features->outcomes          = (!empty($CFG->enableoutcomes) and plugin_supports('mod', $this->_modname, FEATURE_GRADE_OUTCOMES, true));
        $this->_features->hasgrades         = plugin_supports('mod', $this->_modname, FEATURE_GRADE_HAS_GRADE, false);
        $this->_features->idnumber          = plugin_supports('mod', $this->_modname, FEATURE_IDNUMBER, true);
        $this->_features->introeditor       = plugin_supports('mod', $this->_modname, FEATURE_MOD_INTRO, true);
        $this->_features->defaultcompletion = plugin_supports('mod', $this->_modname, FEATURE_MODEDIT_DEFAULT_COMPLETION, true);
        $this->_features->rating            = plugin_supports('mod', $this->_modname, FEATURE_RATE, false);
        $this->_features->showdescription   = plugin_supports('mod', $this->_modname, FEATURE_SHOW_DESCRIPTION, false);
        $this->_features->gradecat          = ($this->_features->outcomes or $this->_features->hasgrades);
        $this->_features->advancedgrading   = plugin_supports('mod', $this->_modname, FEATURE_ADVANCED_GRADING, false);
        $this->_features->hasnoview         = plugin_supports('mod', $this->_modname, FEATURE_NO_VIEW_LINK, false);
        $this->_features->canrescale = (component_callback_exists('mod_' . $this->_modname, 'rescale_activity_grades') !== false);
    }

    /**
     * Allows module to modify data returned by get_moduleinfo_data() or prepare_new_moduleinfo_data() before calling set_data()
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param array $default_values passed by reference
     */
    function data_preprocessing(&$default_values){
        if (empty($default_values['scale'])) {
            $default_values['assessed'] = 0;
        }

        if (empty($default_values['assessed'])){
            $default_values['ratingtime'] = 0;
        } else {
            $default_values['ratingtime']=
                ($default_values['assesstimestart'] && $default_values['assesstimefinish']) ? 1 : 0;
        }
    }

    /**
     * Each module which defines definition_after_data() must call this method using parent::definition_after_data();
     */
    function definition_after_data() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        if ($id = $mform->getElementValue('update')) {
            $modulename = $mform->getElementValue('modulename');
            $instance   = $mform->getElementValue('instance');
            $component = "mod_{$modulename}";

            if ($this->_features->gradecat) {
                $hasgradeitems = false;
                $items = grade_item::fetch_all([
                    'itemtype' => 'mod',
                    'itemmodule' => $modulename,
                    'iteminstance' => $instance,
                    'courseid' => $COURSE->id,
                ]);

                $gradecategories = [];
                $removecategories = [];
                //will be no items if, for example, this activity supports ratings but rating aggregate type == no ratings
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if (!empty($item->outcomeid)) {
                            $elname = 'outcome_'.$item->outcomeid;
                            if ($mform->elementExists($elname)) {
                                $mform->hardFreeze($elname); // prevent removing of existing outcomes
                            }
                        } else {
                            $hasgradeitems = true;
                        }
                    }

                    foreach ($items as $item) {
                        $gradecatfieldname = component_gradeitems::get_field_name_for_itemnumber(
                            $component,
                            $item->itemnumber,
                            'gradecat'
                        );

                        if (!isset($gradecategories[$gradecatfieldname])) {
                            $gradecategories[$gradecatfieldname] = $item->categoryid;
                        } else if ($gradecategories[$gradecatfieldname] != $item->categoryid) {
                            $removecategories[$gradecatfieldname] = true;
                        }
                    }
                }

                foreach ($removecategories as $toremove) {
                    if ($mform->elementExists($toremove)) {
                        $mform->removeElement($toremove);
                    }
                }
            }
        }

        if ($COURSE->groupmodeforce) {
            if ($mform->elementExists('groupmode')) {
                // The groupmode can not be changed if forced from course settings.
                $mform->hardFreeze('groupmode');
            }
        }

        // Don't disable/remove groupingid if it is currently set to something, otherwise you cannot turn it off at same
        // time as turning off other option (MDL-30764).
        if (empty($this->_cm) || !$this->_cm->groupingid) {
            if ($mform->elementExists('groupmode') && empty($COURSE->groupmodeforce)) {
                $mform->hideIf('groupingid', 'groupmode', 'eq', NOGROUPS);

            } else if (!$mform->elementExists('groupmode')) {
                // Groupings have no use without groupmode.
                if ($mform->elementExists('groupingid')) {
                    $mform->removeElement('groupingid');
                }
                // Nor does the group restrictions button.
                if ($mform->elementExists('restrictgroupbutton')) {
                    $mform->removeElement('restrictgroupbutton');
                }
            }
        }

        // Completion: If necessary, freeze fields.
        $this->definition_after_data_completion();

        // Freeze admin defaults if required (and not different from default)
        $this->apply_admin_locked_flags();

        $this->plugin_extend_coursemodule_definition_after_data();
    }

    // form verification
    function validation($data, $files) {
        global $COURSE, $DB, $CFG;

        $mform =& $this->_form;

        $errors = parent::validation($data, $files);

        if ($mform->elementExists('name')) {
            $name = trim($data['name']);
            if ($name == '') {
                $errors['name'] = get_string('required');
            }
        }

        $grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$data['modulename'],
                     'iteminstance'=>$data['instance'], 'itemnumber'=>0, 'courseid'=>$COURSE->id));
        if ($data['coursemodule']) {
            $cm = $DB->get_record('course_modules', array('id'=>$data['coursemodule']));
        } else {
            $cm = null;
        }

        if ($mform->elementExists('cmidnumber')) {
            // verify the idnumber
            if (!grade_verify_idnumber($data['cmidnumber'], $COURSE->id, $grade_item, $cm)) {
                $errors['cmidnumber'] = get_string('idnumbertaken');
            }
        }

        $component = "mod_{$this->_modname}";
        $itemnames = component_gradeitems::get_itemname_mapping_for_component($component);
        foreach ($itemnames as $itemnumber => $itemname) {
            $gradefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'grade');
            $gradepassfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'gradepass');
            $assessedfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'assessed');
            $scalefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'scale');

            // Ratings: Don't let them select an aggregate type without selecting a scale.
            // If the user has selected to use ratings but has not chosen a scale or set max points then the form is
            // invalid. If ratings have been selected then the user must select either a scale or max points.
            // This matches (horrible) logic in data_preprocessing.
            if (isset($data[$assessedfieldname]) && $data[$assessedfieldname] > 0 && empty($data[$scalefieldname])) {
                $errors[$assessedfieldname] = get_string('scaleselectionrequired', 'rating');
            }

            // Check that the grade pass is a valid number.
            $gradepassvalid = false;
            if (isset($data[$gradepassfieldname])) {
                if (unformat_float($data[$gradepassfieldname], true) === false) {
                    $errors[$gradepassfieldname] = get_string('err_numeric', 'form');
                } else {
                    $gradepassvalid = true;
                }
            }

            // Grade to pass: ensure that the grade to pass is valid for points and scales.
            // If we are working with a scale, convert into a positive number for validation.
            if ($gradepassvalid && isset($data[$gradepassfieldname]) && (!empty($data[$gradefieldname]) || !empty($data[$scalefieldname]))) {
                $scale = !empty($data[$gradefieldname]) ? $data[$gradefieldname] : $data[$scalefieldname];
                if ($scale < 0) {
                    $scalevalues = $DB->get_record('scale', array('id' => -$scale));
                    $grade = count(explode(',', $scalevalues->scale));
                } else {
                    $grade = $scale;
                }
                if (unformat_float($data[$gradepassfieldname]) > $grade) {
                    $errors[$gradepassfieldname] = get_string('gradepassgreaterthangrade', 'grades', $grade);
                }
            }

            // We have a grade if there is a non-falsey value for:
            // - the assessedfieldname for Ratings there; or
            // - the gradefieldname for Ratings there.
            if (empty($data[$assessedfieldname]) && empty($data[$gradefieldname])) {
                // There are no grades set therefore completion is not allowed.
                if (isset($data['completiongradeitemnumber']) && $data['completiongradeitemnumber'] == (string) $itemnumber) {
                    $errors['completiongradeitemnumber'] = get_string(
                        'badcompletiongradeitemnumber',
                        'completion',
                        get_string("grade_{$itemname}_name", $component)
                    );
                }
            }

            if (isset($data['completionpassgrade']) && $data['completionpassgrade']) {
                // We need to check whether there's a valid gradepass value.
                // This can either be in completiongradeitemnumber when there are multiple options OR,
                // The first grade item if completionusegrade is specified.
                $validategradepass = false;
                if (isset($data['completiongradeitemnumber'])) {
                    if ($data['completiongradeitemnumber'] == (string)$itemnumber) {
                        $validategradepass = true;
                    }
                } else if (isset($data['completionusegrade']) && $data['completionusegrade']) {
                    $validategradepass = true;
                }

                // We need to make all the validations related with $gradepassfieldname
                // with them being correct floats, keeping the originals unmodified for
                // later validations / showing the form back...
                // TODO: Note that once MDL-73994 is fixed we'll have to re-visit this and
                // adapt the code below to the new values arriving here, without forgetting
                // the special case of empties and nulls.
                $gradepass = isset($data[$gradepassfieldname]) ? unformat_float($data[$gradepassfieldname]) : null;

                // Confirm gradepass is a valid non-empty (null or zero) value.
                if ($validategradepass && (is_null($gradepass) || $gradepass == 0)) {
                    $errors['completionpassgrade'] = get_string(
                        'activitygradetopassnotset',
                        'completion'
                    );
                }
            }
        }

        // Completion: Check completion fields don't have errors.
        $errors = array_merge($errors, $this->validate_completion($data));

        // Availability: Check availability field does not have errors.
        if (!empty($CFG->enableavailability)) {
            \core_availability\frontend::report_validation_errors($data, $errors);
        }

        $pluginerrors = $this->plugin_extend_coursemodule_validation($data);
        if (!empty($pluginerrors)) {
            $errors = array_merge($errors, $pluginerrors);
        }

        return $errors;
    }

    /**
     * Extend the validation function from any other plugin.
     *
     * @param stdClass $data The form data.
     * @return array $errors The list of errors keyed by element name.
     */
    protected function plugin_extend_coursemodule_validation($data) {
        $errors = array();

        $callbacks = get_plugins_with_function('coursemodule_validation', 'lib.php');
        foreach ($callbacks as $type => $plugins) {
            foreach ($plugins as $plugin => $pluginfunction) {
                // We have exposed all the important properties with public getters - the errors array should be pass by reference.
                $pluginerrors = $pluginfunction($this, $data);
                if (!empty($pluginerrors)) {
                    $errors = array_merge($errors, $pluginerrors);
                }
            }
        }
        return $errors;
    }

    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param mixed $default_values object or array of default values
     */
    function set_data($default_values) {
        if (is_object($default_values)) {
            $default_values = (array)$default_values;
        }

        $this->data_preprocessing($default_values);
        parent::set_data($default_values);
    }

    /**
     * Adds all the standard elements to a form for the lti module.
     */
    protected function standard_lti_elements() {
        global $COURSE, $CFG, $DB, $OUTPUT, $PAGE;
        $mform =& $this->_form;

        $mform->addElement('header', 'externaltool', get_string('externaltool', 'ltix'));

        $mform->addElement('checkbox', 'showtitlelaunch', get_string('display_name', 'core_ltix'));
        $mform->setAdvanced('showtitlelaunch');
        $mform->setDefault('showtitlelaunch', true);
        $mform->addHelpButton('showtitlelaunch', 'display_name', 'assign');

        $mform->addElement('checkbox', 'showdescriptionlaunch', get_string('display_description', 'core_ltix'));
        $mform->setAdvanced('showdescriptionlaunch');
        $mform->addHelpButton('showdescriptionlaunch', 'display_description', 'core_ltix');

        //Show type
        if ($type = optional_param('type', false, PARAM_ALPHA)) {
            component_callback("ltisource_$type", 'add_instance_hook');
        }

        // Type ID parameter being passed when adding an preconfigured tool from activity chooser.
        $typeid = optional_param('typeid', false, PARAM_INT);

        $showoptions = has_capability('moodle/ltix:addmanualinstance', $this->context);
        // Show configuration details only if not preset (when new) or user has the capabilities to do so (when editing).
        if ($this->_instance) {
            $showtypes = has_capability('moodle/ltix:addpreconfiguredinstance', $this->context);
            if (!$showoptions && $this->current->typeid == 0) {
                // If you cannot add a manual instance and this is already a manual instance, then
                // remove the 'types' selector.
                $showtypes = false;
            }
        } else {
            $showtypes = !$typeid;
        }

        // Tool settings.
        $toolproxy = array();
        // Array of tool type IDs that don't support ContentItemSelectionRequest.
        $noncontentitemtypes = [];

        if ($showtypes) {
            $tooltypes = $mform->addElement('select', 'typeid', get_string('external_tool_type', 'ltix'));
            if ($typeid) {
                $mform->getElement('typeid')->setValue($typeid);
            }
            $mform->addHelpButton('typeid', 'external_tool_type', 'ltix');

            foreach ($this->lti_get_types_for_add_instance() as $id => $type) {
                if (!empty($type->toolproxyid)) {
                    $toolproxy[] = $type->id;
                    $attributes = array('globalTool' => 1, 'toolproxy' => 1);
                    $enabledcapabilities = explode("\n", $type->enabledcapability);
                    if (!in_array('Result.autocreate', $enabledcapabilities) ||
                        in_array('BasicOutcome.url', $enabledcapabilities)) {
                        $attributes['nogrades'] = 1;
                    }
                    if (!in_array('Person.name.full', $enabledcapabilities) &&
                        !in_array('Person.name.family', $enabledcapabilities) &&
                        !in_array('Person.name.given', $enabledcapabilities)) {
                        $attributes['noname'] = 1;
                    }
                    if (!in_array('Person.email.primary', $enabledcapabilities)) {
                        $attributes['noemail'] = 1;
                    }
                } else if ($type->course == $COURSE->id) {
                    $attributes = array('editable' => 1, 'courseTool' => 1, 'domain' => $type->tooldomain);
                } else if ($id != 0) {
                    $attributes = array('globalTool' => 1, 'domain' => $type->tooldomain);
                } else {
                    $attributes = array();
                }

                if ($id) {
                    $config = $this->lti_get_type_config($id);
                    if (!empty($config['contentitem'])) {
                        $attributes['data-contentitem'] = 1;
                        $attributes['data-id'] = $id;
                    } else {
                        $noncontentitemtypes[] = $id;
                    }
                }
                $tooltypes->addOption($type->name, $id, $attributes);
            }
        } else {
            $mform->addElement('hidden', 'typeid', $typeid);
            $mform->setType('typeid', PARAM_INT);
            if ($typeid) {
                $config = $this->lti_get_type_config($typeid);
                if (!empty($config['contentitem'])) {
                    $mform->addElement('hidden', 'contentitem', 1);
                    $mform->setType('contentitem', PARAM_INT);
                }
            }
        }
        // Add button that launches the content-item selection dialogue.
        // Set contentitem URL.
        $contentitemurl = new moodle_url('/mod/lti/contentitem.php');
        $contentbuttonattributes = [
            'data-contentitemurl' => $contentitemurl->out(false)
        ];
        if (!$showtypes) {
            if (!$typeid || empty($this->lti_get_type_config($typeid)['contentitem'])) {
                $contentbuttonattributes['disabled'] = 'disabled';
            }
        }
        $contentbuttonlabel = get_string('selectcontent', 'ltix');
        $contentbutton = $mform->addElement('button', 'selectcontent', $contentbuttonlabel, $contentbuttonattributes);
        // Disable select content button if the selected tool doesn't support content item or it's set to Automatic.
        if ($showtypes) {
            $allnoncontentitemtypes = $noncontentitemtypes;
            $allnoncontentitemtypes[] = '0'; // Add option value for "Automatic, based on tool URL".
            $mform->disabledIf('selectcontent', 'typeid', 'in', $allnoncontentitemtypes);
        }

        if ($showoptions) {
            $mform->addElement('text', 'toolurl', get_string('launch_url', 'ltix'), array('size' => '64'));
            $mform->setType('toolurl', PARAM_URL);
            $mform->addHelpButton('toolurl', 'launch_url', 'core_ltix');
            $mform->hideIf('toolurl', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('text', 'securetoolurl', get_string('secure_launch_url', 'core_ltix'), array('size' => '64'));
            $mform->setType('securetoolurl', PARAM_URL);
            $mform->setAdvanced('securetoolurl');
            $mform->addHelpButton('securetoolurl', 'secure_launch_url', 'core_ltix');
            $mform->hideIf('securetoolurl', 'typeid', 'in', $noncontentitemtypes);
        } else {
            // We still need those on page to support deep linking return, but hidden to avoid instructor modification.
            $mform->addElement('hidden', 'toolurl', '', array('id' => 'id_toolurl'));
            $mform->setType('toolurl', PARAM_URL);
            $mform->addElement('hidden', 'securetoolurl', '', array('id' => 'id_securetoolurl'));
            $mform->setType('securetoolurl', PARAM_URL);
        }

        $mform->addElement('hidden', 'urlmatchedtypeid', '', array('id' => 'id_urlmatchedtypeid'));
        $mform->setType('urlmatchedtypeid', PARAM_INT);

        $mform->addElement('hidden', 'lineitemresourceid', '', array( 'id' => 'id_lineitemresourceid' ));
        $mform->setType('lineitemresourceid', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemtag', '', array( 'id' => 'id_lineitemtag'));
        $mform->setType('lineitemtag', PARAM_TEXT);

        $mform->addElement('hidden', 'lineitemsubreviewurl', '', array( 'id' => 'id_lineitemsubreviewurl'));
        $mform->setType('lineitemsubreviewurl', PARAM_URL);

        $mform->addElement('hidden', 'lineitemsubreviewparams', '', array( 'id' => 'id_lineitemsubreviewparams'));
        $mform->setType('lineitemsubreviewparams', PARAM_TEXT);


        $launchoptions = array();
        $launchoptions[LTI_LAUNCH_CONTAINER_DEFAULT] = get_string('default', 'core_ltix');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'core_ltix');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'core_ltix');
        $launchoptions[LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW] = get_string('existing_window', 'core_ltix');
        $launchoptions[LTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'core_ltix');

        $mform->addElement('select', 'launchcontainer', get_string('launchinpopup', 'core_ltix'), $launchoptions);
        $mform->setDefault('launchcontainer', LTI_LAUNCH_CONTAINER_DEFAULT);
        $mform->addHelpButton('launchcontainer', 'launchinpopup', 'core_ltix');
        $mform->setAdvanced('launchcontainer');

        if ($showoptions) {
            $mform->addElement('text', 'resourcekey', get_string('resourcekey', 'core_ltix'));
            $mform->setType('resourcekey', PARAM_TEXT);
            $mform->setAdvanced('resourcekey');
            $mform->addHelpButton('resourcekey', 'resourcekey', 'core_ltix');
            $mform->setForceLtr('resourcekey');
            $mform->hideIf('resourcekey', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('passwordunmask', 'password', get_string('password', 'core_ltix'));
            $mform->setType('password', PARAM_TEXT);
            $mform->setAdvanced('password');
            $mform->addHelpButton('password', 'password', 'core_ltix');
            $mform->hideIf('password', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('textarea', 'instructorcustomparameters', get_string('custom', 'core_ltix'), array('rows' => 4, 'cols' => 60));
            $mform->setType('instructorcustomparameters', PARAM_TEXT);
            $mform->setAdvanced('instructorcustomparameters');
            $mform->addHelpButton('instructorcustomparameters', 'custom', 'core_ltix');
            $mform->setForceLtr('instructorcustomparameters');

            $mform->addElement('text', 'icon', get_string('icon_url', 'ltix'), array('size' => '64'));
            $mform->setType('icon', PARAM_URL);
            $mform->setAdvanced('icon');
            $mform->addHelpButton('icon', 'icon_url', 'core_ltix');
            $mform->hideIf('icon', 'typeid', 'in', $noncontentitemtypes);

            $mform->addElement('text', 'secureicon', get_string('secure_icon_url', 'core_ltix'), array('size' => '64'));
            $mform->setType('secureicon', PARAM_URL);
            $mform->setAdvanced('secureicon');
            $mform->addHelpButton('secureicon', 'secure_icon_url', 'core_ltix');
            $mform->hideIf('secureicon', 'typeid', 'in', $noncontentitemtypes);
        } else {
            // Keep those in the form to allow deep linking.
            $mform->addElement('hidden', 'resourcekey', '', array('id' => 'id_resourcekey'));
            $mform->setType('resourcekey', PARAM_TEXT);
            $mform->addElement('hidden', 'password', '', array('id' => 'id_password'));
            $mform->setType('password', PARAM_TEXT);
            $mform->addElement('hidden', 'instructorcustomparameters', '', array('id' => 'id_instructorcustomparameters'));
            $mform->setType('instructorcustomparameters', PARAM_TEXT);
            $mform->addElement('hidden', 'icon', '', array('id' => 'id_icon'));
            $mform->setType('icon', PARAM_URL);
            $mform->addElement('hidden', 'secureicon', '', array('id' => 'id_secureicon'));
            $mform->setType('secureicon', PARAM_URL);
        }

        // Add privacy preferences fieldset where users choose whether to send their data.
        $mform->addElement('header', 'privacy', get_string('privacy', 'ltix'));

        $mform->addElement('advcheckbox', 'instructorchoicesendname', get_string('share_name', 'core_ltix'));
        $mform->setDefault('instructorchoicesendname', '1');
        $mform->addHelpButton('instructorchoicesendname', 'share_name', 'core_ltix');
        $mform->disabledIf('instructorchoicesendname', 'typeid', 'in', $toolproxy);

        $mform->addElement('advcheckbox', 'instructorchoicesendemailaddr', get_string('share_email', 'core_ltix'));
        $mform->setDefault('instructorchoicesendemailaddr', '1');
        $mform->addHelpButton('instructorchoicesendemailaddr', 'share_email', 'core_ltix');
        $mform->disabledIf('instructorchoicesendemailaddr', 'typeid', 'in', $toolproxy);

        $mform->addElement('advcheckbox', 'instructorchoiceacceptgrades', get_string('accept_grades', 'core_ltix'));
        $mform->setDefault('instructorchoiceacceptgrades', '0');
        $mform->addHelpButton('instructorchoiceacceptgrades', 'accept_grades', 'core_ltix');
        $mform->disabledIf('instructorchoiceacceptgrades', 'typeid', 'in', $toolproxy);

        // Add standard course module grading elements.
        //$this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        //$this->standard_coursemodule_elements();
        /*$mform->addElement('text', 'cmidnumber', get_string('idnumbermod'));
        $mform->setType('cmidnumber', PARAM_RAW);
        $mform->addHelpButton('cmidnumber', 'idnumbermod');*/
        $mform->setAdvanced('cmidnumber');

        // Add standard buttons, common to all modules.
        //$this->add_action_buttons();

        $editurl = new moodle_url('/ltix/instructor_edit_tool_type.php',
            array('sesskey' => sesskey(), 'course' => $COURSE->id));
        $ajaxurl = new moodle_url('/ltix/ajax.php');

        // All these icon uses are incorrect. LTI JS needs updating to use AMD modules and templates so it can use
        // the mustache pix helper - until then LTI will have inconsistent icons.
        $jsinfo = (object)array(
            'edit_icon_url' => (string)$OUTPUT->image_url('t/edit'),
            'add_icon_url' => (string)$OUTPUT->image_url('t/add'),
            'delete_icon_url' => (string)$OUTPUT->image_url('t/delete'),
            'green_check_icon_url' => (string)$OUTPUT->image_url('i/valid'),
            'warning_icon_url' => (string)$OUTPUT->image_url('warning', 'lti'),
            'instructor_tool_type_edit_url' => $editurl->out(false),
            'ajax_url' => $ajaxurl->out(true),
            'courseId' => $COURSE->id
        );

        $module = array(
            'name' => 'core_ltix_edit',
            'fullpath' => '/ltix/mod_form.js',
            'requires' => array('base', 'io', 'querystring-stringify-simple', 'node', 'event', 'json-parse'),
            'strings' => array(
                array('addtype', 'core_ltix'),
                array('edittype', 'core_ltix'),
                array('deletetype', 'core_ltix'),
                array('delete_confirmation', 'core_ltix'),
                array('cannot_edit', 'core_ltix'),
                array('cannot_delete', 'core_ltix'),
                array('global_tool_types', 'core_ltix'),
                array('course_tool_types', 'core_ltix'),
                array('using_tool_configuration', 'ltix'),
                array('using_tool_cartridge', 'core_ltix'),
                array('domain_mismatch', 'core_ltix'),
                array('custom_config', 'core_ltix'),
                array('tool_config_not_found', 'core_ltix'),
                array('tooltypeadded', 'core_ltix'),
                array('tooltypedeleted', 'core_ltix'),
                array('tooltypenotdeleted', 'core_ltix'),
                array('tooltypeupdated', 'core_ltix'),
                array('forced_help', 'core_ltix')
            ),
        );

        if (!empty($typeid)) {
            $mform->setAdvanced('typeid');
            $mform->setAdvanced('toolurl');
        }

        $PAGE->requires->js_init_call('M.mod_lti.editor.init', array(json_encode($jsinfo)), true, $module);
    }

    /**
     * Adds all the standard elements to a form to edit the settings for an activity module.
     */
    protected function standard_coursemodule_elements() {
        global $COURSE, $CFG, $DB;
        $mform =& $this->_form;

        $this->_outcomesused = false;
        if ($this->_features->outcomes) {
            if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
                $this->_outcomesused = true;
                $mform->addElement('header', 'modoutcomes', get_string('outcomes', 'grades'));
                foreach($outcomes as $outcome) {
                    $mform->addElement('advcheckbox', 'outcome_'.$outcome->id, $outcome->get_name());
                }
            }
        }

        if ($this->_features->rating) {
            $this->add_rating_settings($mform, 0);
        }

        $mform->addElement('header', 'modstandardelshdr', get_string('modstandardels', 'form'));

        $section = get_fast_modinfo($COURSE)->get_section_info($this->_section);
        $allowstealth =
            !empty($CFG->allowstealth) &&
            $this->courseformat->allow_stealth_module_visibility($this->_cm, $section) &&
            !$this->_features->hasnoview;
        if ($allowstealth && $section->visible) {
            $modvisiblelabel = 'modvisiblewithstealth';
        } else if ($section->visible) {
            $modvisiblelabel = 'modvisible';
        } else {
            $modvisiblelabel = 'modvisiblehiddensection';
        }
        $mform->addElement('modvisible', 'visible', get_string($modvisiblelabel), null,
                array('allowstealth' => $allowstealth, 'sectionvisible' => $section->visible, 'cm' => $this->_cm));
        $mform->addHelpButton('visible', $modvisiblelabel);
        if (!empty($this->_cm) && !has_capability('moodle/course:activityvisibility', $this->get_context())) {
            $mform->hardFreeze('visible');
        }

        if ($this->_features->idnumber) {
            $mform->addElement('text', 'cmidnumber', get_string('idnumbermod'));
            $mform->setType('cmidnumber', PARAM_RAW);
            $mform->addHelpButton('cmidnumber', 'idnumbermod');
        }

        if (has_capability('moodle/course:setforcedlanguage', $this->get_context())) {
            $languages = ['' => get_string('forceno')];
            $languages += get_string_manager()->get_list_of_translations();

            $mform->addElement('select', 'lang', get_string('forcelanguage'), $languages);
        }

        if ($CFG->downloadcoursecontentallowed) {
                $choices = [
                    DOWNLOAD_COURSE_CONTENT_DISABLED => get_string('no'),
                    DOWNLOAD_COURSE_CONTENT_ENABLED => get_string('yes'),
                ];
                $mform->addElement('select', 'downloadcontent', get_string('downloadcontent', 'course'), $choices);
                $downloadcontentdefault = $this->_cm->downloadcontent ?? DOWNLOAD_COURSE_CONTENT_ENABLED;
                $mform->addHelpButton('downloadcontent', 'downloadcontent', 'course');
                if (has_capability('moodle/course:configuredownloadcontent', $this->get_context())) {
                    $mform->setDefault('downloadcontent', $downloadcontentdefault);
                } else {
                    $mform->hardFreeze('downloadcontent');
                    $mform->setConstant('downloadcontent', $downloadcontentdefault);
                }
        }

        if ($this->_features->groups) {
            $options = array(NOGROUPS       => get_string('groupsnone'),
                             SEPARATEGROUPS => get_string('groupsseparate'),
                             VISIBLEGROUPS  => get_string('groupsvisible'));
            $mform->addElement('select', 'groupmode', get_string('groupmode', 'group'), $options, NOGROUPS);
            $mform->addHelpButton('groupmode', 'groupmode', 'group');
        }

        if ($this->_features->groupings) {
            // Groupings selector - used to select grouping for groups in activity.
            $options = array();
            if ($groupings = $DB->get_records('groupings', array('courseid'=>$COURSE->id))) {
                foreach ($groupings as $grouping) {
                    $options[$grouping->id] = format_string($grouping->name);
                }
            }
            core_collator::asort($options);
            $options = array(0 => get_string('none')) + $options;
            $mform->addElement('select', 'groupingid', get_string('grouping', 'group'), $options);
            $mform->addHelpButton('groupingid', 'grouping', 'group');
        }

        if (!empty($CFG->enableavailability)) {
            // Add special button to end of previous section if groups/groupings
            // are enabled.

            $availabilityplugins = \core\plugininfo\availability::get_enabled_plugins();
            $groupavailability = $this->_features->groups && array_key_exists('group', $availabilityplugins);
            $groupingavailability = $this->_features->groupings && array_key_exists('grouping', $availabilityplugins);

            if ($groupavailability || $groupingavailability) {
                // When creating the button, we need to set type=button to prevent it behaving as a submit.
                $mform->addElement('static', 'restrictgroupbutton', '',
                    html_writer::tag('button', get_string('restrictbygroup', 'availability'), [
                        'id' => 'restrictbygroup',
                        'type' => 'button',
                        'disabled' => 'disabled',
                        'class' => 'btn btn-secondary',
                        'data-groupavailability' => $groupavailability,
                        'data-groupingavailability' => $groupingavailability
                    ])
                );
            }

            // Availability field. This is just a textarea; the user interface
            // interaction is all implemented in JavaScript.
            $mform->addElement('header', 'availabilityconditionsheader',
                    get_string('restrictaccess', 'availability'));
            // Note: This field cannot be named 'availability' because that
            // conflicts with fields in existing modules (such as assign).
            // So it uses a long name that will not conflict.
            $mform->addElement('textarea', 'availabilityconditionsjson',
                    get_string('accessrestrictions', 'availability'),
                    ['class' => 'd-none']
            );

            // The _cm variable may not be a proper cm_info, so get one from modinfo.
            if ($this->_cm) {
                $modinfo = get_fast_modinfo($COURSE);
                $cm = $modinfo->get_cm($this->_cm->id);
            } else {
                $cm = null;
            }
            \core_availability\frontend::include_all_javascript($COURSE, $cm);
        }

        // Conditional activities: completion tracking section
        if(!isset($completion)) {
            $completion = new completion_info($COURSE);
        }

        // Add the completion tracking elements to the form.
        if ($completion->is_enabled()) {
            $mform->addElement('header', 'activitycompletionheader', get_string('activitycompletion', 'completion'));
            $this->add_completion_elements();
        }

        // Populate module tags.
        if (core_tag_tag::is_enabled('core', 'course_modules')) {
            $mform->addElement('header', 'tagshdr', get_string('tags', 'tag'));
            $mform->addElement('tags', 'tags', get_string('tags'), array('itemtype' => 'course_modules', 'component' => 'core'));
            if ($this->_cm) {
                $tags = core_tag_tag::get_item_tags_array('core', 'course_modules', $this->_cm->id);
                $mform->setDefault('tags', $tags);
            }
        }

        $this->standard_hidden_coursemodule_elements();

        $this->plugin_extend_coursemodule_standard_elements();
    }

    /**
     * Add rating settings.
     *
     * @param moodleform_mod $mform
     * @param int $itemnumber
     */
    protected function add_rating_settings($mform, int $itemnumber) {
        global $CFG, $COURSE;

        if ($this->gradedorrated && $this->gradedorrated !== 'rated') {
            return;
        }
        $this->gradedorrated = 'rated';

        require_once("{$CFG->dirroot}/rating/lib.php");
        $rm = new rating_manager();

        $component = "mod_{$this->_modname}";
        $gradecatfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'gradecat');
        $gradepassfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'gradepass');
        $assessedfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'assessed');
        $scalefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'scale');

        $mform->addElement('header', 'modstandardratings', get_string('ratings', 'rating'));

        $isupdate = !empty($this->_cm);

        $rolenamestring = null;
        if ($isupdate) {
            $capabilities = ['moodle/rating:rate', "mod/{$this->_cm->modname}:rate"];
            $rolenames = get_role_names_with_caps_in_context($this->get_context(), $capabilities);
            $rolenamestring = implode(', ', $rolenames);
        } else {
            $rolenamestring = get_string('capabilitychecknotavailable', 'rating');
        }

        $mform->addElement('static', 'rolewarning', get_string('rolewarning', 'rating'), $rolenamestring);
        $mform->addHelpButton('rolewarning', 'rolewarning', 'rating');

        $mform->addElement('select', $assessedfieldname, get_string('aggregatetype', 'rating') , $rm->get_aggregate_types());
        $mform->setDefault($assessedfieldname, 0);
        $mform->addHelpButton($assessedfieldname, 'aggregatetype', 'rating');

        $gradeoptions = [
            'isupdate' => $isupdate,
            'currentgrade' => false,
            'hasgrades' => false,
            'canrescale' => false,
            'useratings' => true,
        ];
        if ($isupdate) {
            $gradeitem = grade_item::fetch([
                'itemtype' => 'mod',
                'itemmodule' => $this->_cm->modname,
                'iteminstance' => $this->_cm->instance,
                'itemnumber' => $itemnumber,
                'courseid' => $COURSE->id,
            ]);
            if ($gradeitem) {
                $gradeoptions['currentgrade'] = $gradeitem->grademax;
                $gradeoptions['currentgradetype'] = $gradeitem->gradetype;
                $gradeoptions['currentscaleid'] = $gradeitem->scaleid;
                $gradeoptions['hasgrades'] = $gradeitem->has_grades();
            }
        }

        $mform->addElement('modgrade', $scalefieldname, get_string('scale'), $gradeoptions);
        $mform->hideIf($scalefieldname, $assessedfieldname, 'eq', 0);
        $mform->addHelpButton($scalefieldname, 'modgrade', 'grades');
        $mform->setDefault($scalefieldname, $CFG->gradepointdefault);

        $mform->addElement('checkbox', 'ratingtime', get_string('ratingtime', 'rating'));
        $mform->hideIf('ratingtime', $assessedfieldname, 'eq', 0);

        $mform->addElement('date_time_selector', 'assesstimestart', get_string('from'));
        $mform->hideIf('assesstimestart', $assessedfieldname, 'eq', 0);
        $mform->hideIf('assesstimestart', 'ratingtime');

        $mform->addElement('date_time_selector', 'assesstimefinish', get_string('to'));
        $mform->hideIf('assesstimefinish', $assessedfieldname, 'eq', 0);
        $mform->hideIf('assesstimefinish', 'ratingtime');

        if ($this->_features->gradecat) {
            $mform->addElement(
                'select',
                $gradecatfieldname,
                get_string('gradecategoryonmodform', 'grades'),
                grade_get_categories_menu($COURSE->id, $this->_outcomesused)
            );
            $mform->addHelpButton($gradecatfieldname, 'gradecategoryonmodform', 'grades');
            $mform->hideIf($gradecatfieldname, $assessedfieldname, 'eq', 0);
            $mform->hideIf($gradecatfieldname, "{$scalefieldname}[modgrade_type]", 'eq', 'none');
        }

        // Grade to pass.
        $mform->addElement('float', $gradepassfieldname, get_string('gradepass', 'grades'));
        $mform->addHelpButton($gradepassfieldname, 'gradepass', 'grades');
        $mform->setDefault($gradepassfieldname, '');
        $mform->hideIf($gradepassfieldname, $assessedfieldname, 'eq', '0');
        $mform->hideIf($gradepassfieldname, "{$scalefieldname}[modgrade_type]", 'eq', 'none');
    }

    /**
     * Plugins can extend the coursemodule settings form.
     */
    protected function plugin_extend_coursemodule_standard_elements() {
        $callbacks = get_plugins_with_function('coursemodule_standard_elements', 'lib.php');
        foreach ($callbacks as $type => $plugins) {
            foreach ($plugins as $plugin => $pluginfunction) {
                // We have exposed all the important properties with public getters - and the callback can manipulate the mform
                // directly.
                $pluginfunction($this, $this->_form);
            }
        }
    }

    /**
     * Plugins can extend the coursemodule settings form after the data is set.
     */
    protected function plugin_extend_coursemodule_definition_after_data() {
        $callbacks = get_plugins_with_function('coursemodule_definition_after_data', 'lib.php');
        foreach ($callbacks as $type => $plugins) {
            foreach ($plugins as $plugin => $pluginfunction) {
                $pluginfunction($this, $this->_form);
            }
        }
    }

    /**
     * Can be overridden to add custom completion rules if the module wishes
     * them. If overriding this, you should also override completion_rule_enabled.
     * <p>
     * Just add elements to the form as needed and return the list of IDs. The
     * system will call disabledIf and handle other behaviour for each returned
     * ID.
     * @return array Array of string IDs of added items, empty array if none
     */
    function add_completion_rules() {
        return array();
    }

    /**
     * Called during validation. Override to indicate, based on the data, whether
     * a custom completion rule is enabled (selected).
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are;
     *   default returns false
     */
    function completion_rule_enabled($data) {
        return false;
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
        $mform->setType('modulename', PARAM_PLUGIN);

        $mform->addElement('hidden', 'instance', 0);
        $mform->setType('instance', PARAM_INT);

        $mform->addElement('hidden', 'add', 0);
        $mform->setType('add', PARAM_ALPHANUM);

        $mform->addElement('hidden', 'update', 0);
        $mform->setType('update', PARAM_INT);

        $mform->addElement('hidden', 'return', 0);
        $mform->setType('return', PARAM_BOOL);

        $mform->addElement('hidden', 'sr', 0);
        $mform->setType('sr', PARAM_INT);

        $mform->addElement('hidden', 'beforemod', 0);
        $mform->setType('beforemod', PARAM_INT);

        $mform->addElement('hidden', 'showonly', '');
        $mform->setType('showonly', PARAM_ALPHANUMEXT);
    }

    public function standard_grading_coursemodule_elements() {
        global $COURSE, $CFG;

        if ($this->gradedorrated && $this->gradedorrated !== 'graded') {
            return;
        }
        if ($this->_features->rating) {
            return;
        }
        $this->gradedorrated = 'graded';

        $itemnumber = 0;
        $component = "mod_{$this->_modname}";
        $gradefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'grade');
        $gradecatfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'gradecat');
        $gradepassfieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'gradepass');

        $mform =& $this->_form;
        $isupdate = !empty($this->_cm);
        $gradeoptions = array('isupdate' => $isupdate,
                              'currentgrade' => false,
                              'hasgrades' => false,
                              'canrescale' => $this->_features->canrescale,
                              'useratings' => $this->_features->rating);

        if ($this->_features->hasgrades) {
            if ($this->_features->gradecat) {
                $mform->addElement('header', 'modstandardgrade', get_string('gradenoun'));
            }

            //if supports grades and grades arent being handled via ratings
            if ($isupdate) {
                $gradeitem = grade_item::fetch(array('itemtype' => 'mod',
                                                        'itemmodule' => $this->_cm->modname,
                                                        'iteminstance' => $this->_cm->instance,
                                                        'itemnumber' => 0,
                                                        'courseid' => $COURSE->id));
                if ($gradeitem) {
                    $gradeoptions['currentgrade'] = $gradeitem->grademax;
                    $gradeoptions['currentgradetype'] = $gradeitem->gradetype;
                    $gradeoptions['currentscaleid'] = $gradeitem->scaleid;
                    $gradeoptions['hasgrades'] = $gradeitem->has_grades();
                }
            }
            $mform->addElement('modgrade', $gradefieldname, get_string('gradenoun'), $gradeoptions);
            $mform->addHelpButton($gradefieldname, 'modgrade', 'grades');
            $mform->setDefault($gradefieldname, $CFG->gradepointdefault);

            if ($this->_features->advancedgrading
                    and !empty($this->current->_advancedgradingdata['methods'])
                    and !empty($this->current->_advancedgradingdata['areas'])) {

                if (count($this->current->_advancedgradingdata['areas']) == 1) {
                    // if there is just one gradable area (most cases), display just the selector
                    // without its name to make UI simplier
                    $areadata = reset($this->current->_advancedgradingdata['areas']);
                    $areaname = key($this->current->_advancedgradingdata['areas']);
                    $mform->addElement('select', 'advancedgradingmethod_'.$areaname,
                        get_string('gradingmethod', 'core_grading'), $this->current->_advancedgradingdata['methods']);
                    $mform->addHelpButton('advancedgradingmethod_'.$areaname, 'gradingmethod', 'core_grading');
                    $mform->hideIf('advancedgradingmethod_'.$areaname, "{$gradefieldname}[modgrade_type]", 'eq', 'none');

                } else {
                    // the module defines multiple gradable areas, display a selector
                    // for each of them together with a name of the area
                    $areasgroup = array();
                    foreach ($this->current->_advancedgradingdata['areas'] as $areaname => $areadata) {
                        $areasgroup[] = $mform->createElement('select', 'advancedgradingmethod_'.$areaname,
                            $areadata['title'], $this->current->_advancedgradingdata['methods']);
                        $areasgroup[] = $mform->createElement('static', 'advancedgradingareaname_'.$areaname, '', $areadata['title']);
                    }
                    $mform->addGroup($areasgroup, 'advancedgradingmethodsgroup', get_string('gradingmethods', 'core_grading'),
                        array(' ', '<br />'), false);
                }
            }

            if ($this->_features->gradecat) {
                $mform->addElement('select', $gradecatfieldname,
                        get_string('gradecategoryonmodform', 'grades'),
                        grade_get_categories_menu($COURSE->id, $this->_outcomesused));
                $mform->addHelpButton($gradecatfieldname, 'gradecategoryonmodform', 'grades');
                $mform->hideIf($gradecatfieldname, "{$gradefieldname}[modgrade_type]", 'eq', 'none');
            }

            // Grade to pass.
            $mform->addElement('float', $gradepassfieldname, get_string($gradepassfieldname, 'grades'));
            $mform->addHelpButton($gradepassfieldname, $gradepassfieldname, 'grades');
            $mform->setDefault($gradepassfieldname, '');
            $mform->hideIf($gradepassfieldname, "{$gradefieldname}[modgrade_type]", 'eq', 'none');
        }
    }

    /**
     * Add an editor for an activity's introduction field.
     * @deprecated since MDL-49101 - use moodleform_mod::standard_intro_elements() instead.
     * @param null $required Override system default for requiremodintro
     * @param null $customlabel Override default label for editor
     * @throws coding_exception
     */
    protected function add_intro_editor($required=null, $customlabel=null) {
        $str = "Function moodleform_mod::add_intro_editor() is deprecated, use moodleform_mod::standard_intro_elements() instead.";
        debugging($str, DEBUG_DEVELOPER);

        $this->standard_intro_elements($customlabel);
    }


    /**
     * Add an editor for an activity's introduction field.
     *
     * @param null $customlabel Override default label for editor
     * @throws coding_exception
     */
    protected function standard_intro_elements($customlabel=null) {
        global $CFG;

        $required = $CFG->requiremodintro;

        $mform = $this->_form;
        $label = is_null($customlabel) ? get_string('moduleintro') : $customlabel;

        $mform->addElement('editor', 'introeditor', $label, array('rows' => 10), array('maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true, 'context' => $this->context, 'subdirs' => true));
        $mform->setType('introeditor', PARAM_RAW); // no XSS prevention here, users must be trusted
        if ($required) {
            $mform->addRule('introeditor', get_string('required'), 'required', null, 'client');
        }

        // If the 'show description' feature is enabled, this checkbox appears below the intro.
        // We want to hide that when using the singleactivity course format because it is confusing.
        if ($this->_features->showdescription  && $this->courseformat->has_view_page()) {
            $mform->addElement('advcheckbox', 'showdescription', get_string('showdescription'));
            $mform->addHelpButton('showdescription', 'showdescription');
        }
    }

    /**
     * Overriding formslib's add_action_buttons() method, to add an extra submit "save changes and return" button.
     *
     * @param bool $cancel show cancel button
     * @param string $submitlabel null means default, false means none, string is label text
     * @param string $submit2label  null means default, false means none, string is label text
     * @return void
     */
    function add_action_buttons($cancel=true, $submitlabel=null, $submit2label=null) {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('savechangesanddisplay');
        }

        if (is_null($submit2label)) {
            $submit2label = get_string('savechangesandreturntocourse');
        }

        $mform = $this->_form;

        $mform->addElement('checkbox', 'coursecontentnotification', get_string('coursecontentnotification', 'course'));
        $mform->addHelpButton('coursecontentnotification', 'coursecontentnotification', 'course');
        $mform->closeHeaderBefore('coursecontentnotification');

        // elements in a row need a group
        $buttonarray = array();

        // Label for the submit button to return to the course.
        // Ignore this button in single activity format because it is confusing.
        if ($submit2label !== false && $this->courseformat->has_view_page()) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', $submit2label);
        }

        if ($submitlabel !== false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        }

        if ($cancel) {
            $buttonarray[] = &$mform->createElement('cancel');
        }

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
    }

    /**
     * Get the list of admin settings for this module and apply any locked settings.
     * This cannot happen in apply_admin_defaults because we do not the current values of the settings
     * in that function because set_data has not been called yet.
     *
     * @return void
     */
    protected function apply_admin_locked_flags() {
        global $OUTPUT;

        if (!$this->applyadminlockedflags) {
            return;
        }

        $settings = get_config($this->_modname);
        $mform = $this->_form;
        $lockedicon = html_writer::tag('span',
                                       $OUTPUT->pix_icon('t/locked', get_string('locked', 'admin')),
                                       array('class' => 'action-icon'));
        $isupdate = !empty($this->_cm);

        foreach ($settings as $name => $value) {
            if (strpos('_', $name) !== false) {
                continue;
            }
            if ($mform->elementExists($name)) {
                $element = $mform->getElement($name);
                $lockedsetting = $name . '_locked';
                if (!empty($settings->$lockedsetting)) {
                    // Always lock locked settings for new modules,
                    // for updates, only lock them if the current value is the same as the default (or there is no current value).
                    $value = $settings->$name;
                    if ($isupdate && isset($this->current->$name)) {
                        $value = $this->current->$name;
                    }
                    if ($value == $settings->$name) {
                        $mform->setConstant($name, $settings->$name);
                        $element->setLabel($element->getLabel() . $lockedicon);
                        // Do not use hardfreeze because we need the hidden input to check dependencies.
                        $element->freeze();
                    }
                }
            }
        }
    }

    /**
     * Get the list of admin settings for this module and apply any defaults/advanced/locked/required settings.
     *
     * @param $datetimeoffsets array - If passed, this is an array of fieldnames => times that the
     *                         default date/time value should be relative to. If not passed, all
     *                         date/time fields are set relative to the users current midnight.
     * @return void
     */
    public function apply_admin_defaults($datetimeoffsets = array()) {
        // This flag triggers the settings to be locked in apply_admin_locked_flags().
        $this->applyadminlockedflags = true;

        $settings = get_config($this->_modname);
        $mform = $this->_form;
        $usermidnight = usergetmidnight(time());
        $isupdate = !empty($this->_cm);

        foreach ($settings as $name => $value) {
            if (strpos('_', $name) !== false) {
                continue;
            }
            if ($mform->elementExists($name)) {
                $element = $mform->getElement($name);
                if (!$isupdate) {
                    if ($element->getType() == 'date_time_selector') {
                        $enabledsetting = $name . '_enabled';
                        if (empty($settings->$enabledsetting)) {
                            $mform->setDefault($name, 0);
                        } else {
                            $relativetime = $usermidnight;
                            if (isset($datetimeoffsets[$name])) {
                                $relativetime = $datetimeoffsets[$name];
                            }
                            $mform->setDefault($name, $relativetime + $settings->$name);
                        }
                    } else {
                        $mform->setDefault($name, $settings->$name);
                    }
                }
                $advancedsetting = $name . '_adv';
                if (!empty($settings->$advancedsetting)) {
                    $mform->setAdvanced($name);
                }
                $requiredsetting = $name . '_required';
                if (!empty($settings->$requiredsetting)) {
                    $mform->addRule($name, null, 'required', null, 'client');
                }
            }
        }
    }

    /**
     * Allows modules to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data passed by reference
     */
    public function data_postprocessing($data) {
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * Do not override this method, override data_postprocessing() instead.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            // Convert the grade pass value - we may be using a language which uses commas,
            // rather than decimal points, in numbers. These need to be converted so that
            // they can be added to the DB.
            if (isset($data->gradepass)) {
                $data->gradepass = unformat_float($data->gradepass);
            }

            // Trim name for all activity name.
            if (isset($data->name)) {
                $data->name = trim($data->name);
            }

            $this->data_postprocessing($data);
        }
        return $data;
    }

    /**
     * Returns tool types for lti add instance and edit page
     *
     * @return array Array of lti types
     */
    function lti_get_types_for_add_instance() {
        global $COURSE;
        $admintypes = $this->lti_get_lti_types_by_course($COURSE->id);

        $types = array();
        if (has_capability('moodle/ltix:addmanualinstance', context_course::instance($COURSE->id))) {
            $types[0] = (object)array('name' => get_string('automatic', 'ltix'), 'course' => 0, 'toolproxyid' => null);
        }

        foreach ($admintypes as $type) {
            $types[$type->id] = $type;
        }

        return $types;
    }

    /**
     * Returns all lti types visible in this course
     *
     * @param int $courseid The id of the course to retieve types for
     * @param array $coursevisible options for 'coursevisible' field,
     *        default [LTI_COURSEVISIBLE_PRECONFIGURED, LTI_COURSEVISIBLE_ACTIVITYCHOOSER]
     * @return stdClass[] All the lti types visible in the given course
     */
    function lti_get_lti_types_by_course($courseid, $coursevisible = null) {
        global $DB, $SITE;

        if ($coursevisible === null) {
            $coursevisible = [LTI_COURSEVISIBLE_PRECONFIGURED, LTI_COURSEVISIBLE_ACTIVITYCHOOSER];
        }

        list($coursevisiblesql, $coursevisparams) = $DB->get_in_or_equal($coursevisible, SQL_PARAMS_NAMED, 'coursevisible');
        $courseconds = [];
        if (has_capability('moodle/ltix:addmanualinstance', context_course::instance($courseid))) {
            $courseconds[] = "course = :courseid";
        }
        if (has_capability('moodle/ltix:addpreconfiguredinstance', context_course::instance($courseid))) {
            $courseconds[] = "course = :siteid";
        }
        if (!$courseconds) {
            return [];
        }
        $coursecond = implode(" OR ", $courseconds);
        $query = "SELECT *
                FROM {lti_types}
               WHERE coursevisible $coursevisiblesql
                 AND ($coursecond)
                 AND state = :active
            ORDER BY name ASC";

        return $DB->get_records_sql($query,
            array('siteid' => $SITE->id, 'courseid' => $courseid, 'active' => LTI_TOOL_STATE_CONFIGURED) + $coursevisparams);
    }
    /**
     * Returns configuration details for the tool
     *
     * @param int $typeid   Basic LTI tool typeid
     *
     * @return array        Tool Configuration
     */
    function lti_get_type_config($typeid) {
        global $DB;

        $query = "SELECT name, value
                FROM {lti_types_config}
               WHERE typeid = :typeid1
           UNION ALL
              SELECT 'toolurl' AS name, baseurl AS value
                FROM {lti_types}
               WHERE id = :typeid2
           UNION ALL
              SELECT 'icon' AS name, icon AS value
                FROM {lti_types}
               WHERE id = :typeid3
           UNION ALL
              SELECT 'secureicon' AS name, secureicon AS value
                FROM {lti_types}
               WHERE id = :typeid4";

        $typeconfig = array();
        $configs = $DB->get_records_sql($query,
            array('typeid1' => $typeid, 'typeid2' => $typeid, 'typeid3' => $typeid, 'typeid4' => $typeid));

        if (!empty($configs)) {
            foreach ($configs as $config) {
                $typeconfig[$config->name] = $config->value;
            }
        }

        return $typeconfig;
    }
}
