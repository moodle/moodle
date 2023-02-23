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
 * Moodleform.
 *
 * @package   core_course
 * @copyright Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->libdir.'/plagiarismlib.php');

use core\content\export\exporters\component_exporter;
use core_grades\component_gradeitems;

/**
 * This class adds extra methods to form wrapper specific to be used for module add / update forms
 * mod/{modname}/mod_form.php replaced deprecated mod/{modname}/mod.html Moodleform.
 *
 * @package   core_course
 * @copyright Andrew Nicols <andrew@nicols.co.uk>
 */
abstract class moodleform_mod extends moodleform {
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

        // Completion: If necessary, freeze fields
        $completion = new completion_info($COURSE);
        if ($completion->is_enabled()) {
            // If anybody has completed the activity, these options will be 'locked'
            $completedcount = empty($this->_cm)
                ? 0
                : $completion->count_user_data($this->_cm);

            $freeze = false;
            if (!$completedcount) {
                if ($mform->elementExists('unlockcompletion')) {
                    $mform->removeElement('unlockcompletion');
                }
                // Automatically set to unlocked (note: this is necessary
                // in order to make it recalculate completion once the option
                // is changed, maybe someone has completed it now)
                $mform->getElement('completionunlocked')->setValue(1);
            } else {
                // Has the element been unlocked, either by the button being pressed
                // in this request, or the field already being set from a previous one?
                if ($mform->exportValue('unlockcompletion') ||
                        $mform->exportValue('completionunlocked')) {
                    // Yes, add in warning text and set the hidden variable
                    $mform->insertElementBefore(
                        $mform->createElement('static', 'completedunlocked',
                            get_string('completedunlocked', 'completion'),
                            get_string('completedunlockedtext', 'completion')),
                        'unlockcompletion');
                    $mform->removeElement('unlockcompletion');
                    $mform->getElement('completionunlocked')->setValue(1);
                } else {
                    // No, add in the warning text with the count (now we know
                    // it) before the unlock button
                    $mform->insertElementBefore(
                        $mform->createElement('static', 'completedwarning',
                            get_string('completedwarning', 'completion'),
                            get_string('completedwarningtext', 'completion', $completedcount)),
                        'unlockcompletion');
                    $freeze = true;
                }
            }

            if ($freeze) {
                $mform->freeze('completion');
                if ($mform->elementExists('completionview')) {
                    $mform->freeze('completionview'); // don't use hardFreeze or checkbox value gets lost
                }
                if ($mform->elementExists('completionusegrade')) {
                    $mform->freeze('completionusegrade');
                }
                if ($mform->elementExists('completionpassgrade')) {
                    $mform->freeze('completionpassgrade');

                    // Has the completion pass grade completion criteria been set?
                    // If it has then we shouldn't change the gradepass field.
                    if ($mform->exportValue('completionpassgrade')) {
                        $mform->freeze('gradepass');
                    }
                }
                if ($mform->elementExists('completiongradeitemnumber')) {
                    $mform->freeze('completiongradeitemnumber');
                }
                $mform->freeze($this->_customcompletionelements);
            }
        }

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

        // Completion: Don't let them choose automatic completion without turning
        // on some conditions. Ignore this check when completion settings are
        // locked, as the options are then disabled.
        $automaticcompletion = array_key_exists('completion', $data);
        $automaticcompletion = $automaticcompletion && $data['completion'] == COMPLETION_TRACKING_AUTOMATIC;
        $automaticcompletion = $automaticcompletion && !empty($data['completionunlocked']);

        if ($automaticcompletion) {
            // View to complete.
            $rulesenabled = !empty($data['completionview']);

            // Use grade to complete (only one grade item).
            $rulesenabled = $rulesenabled || !empty($data['completionusegrade']) || !empty($data['completionpassgrade']);

            // Use grade to complete (specific grade item).
            if (!$rulesenabled && isset($data['completiongradeitemnumber'])) {
                $rulesenabled = $data['completiongradeitemnumber'] != '';
            }

            // Module-specific completion rules.
            $rulesenabled = $rulesenabled || $this->completion_rule_enabled($data);

            if (!$rulesenabled) {
                // No rules are enabled. Can't set automatically completed without rules.
                $errors['completion'] = get_string('badautocompletion', 'completion');
            }
        }

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
                    get_string('accessrestrictions', 'availability'));
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
        if ($completion->is_enabled()) {
            $mform->addElement('header', 'activitycompletionheader', get_string('activitycompletion', 'completion'));
            // Unlock button for if people have completed it (will
            // be removed in definition_after_data if they haven't)
            $mform->addElement('submit', 'unlockcompletion', get_string('unlockcompletion', 'completion'));
            $mform->registerNoSubmitButton('unlockcompletion');
            $mform->addElement('hidden', 'completionunlocked', 0);
            $mform->setType('completionunlocked', PARAM_INT);

            $trackingdefault = COMPLETION_TRACKING_NONE;
            // If system and activity default is on, set it.
            if ($CFG->completiondefault && $this->_features->defaultcompletion) {
                $hasrules = plugin_supports('mod', $this->_modname, FEATURE_COMPLETION_HAS_RULES, true);
                $tracksviews = plugin_supports('mod', $this->_modname, FEATURE_COMPLETION_TRACKS_VIEWS, true);
                if ($hasrules || $tracksviews) {
                    $trackingdefault = COMPLETION_TRACKING_AUTOMATIC;
                } else {
                    $trackingdefault = COMPLETION_TRACKING_MANUAL;
                }
            }

            $mform->addElement('select', 'completion', get_string('completion', 'completion'),
                array(COMPLETION_TRACKING_NONE=>get_string('completion_none', 'completion'),
                COMPLETION_TRACKING_MANUAL=>get_string('completion_manual', 'completion')));
            $mform->setDefault('completion', $trackingdefault);
            $mform->addHelpButton('completion', 'completion', 'completion');

            // Automatic completion once you view it
            $gotcompletionoptions = false;
            if (plugin_supports('mod', $this->_modname, FEATURE_COMPLETION_TRACKS_VIEWS, false)) {
                $mform->addElement('checkbox', 'completionview', get_string('completionview', 'completion'),
                    get_string('completionview_desc', 'completion'));
                $mform->hideIf('completionview', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
                // Check by default if automatic completion tracking is set.
                if ($trackingdefault == COMPLETION_TRACKING_AUTOMATIC) {
                    $mform->setDefault('completionview', 1);
                }
                $gotcompletionoptions = true;
            }

            if (plugin_supports('mod', $this->_modname, FEATURE_GRADE_HAS_GRADE, false)) {
                // This activity supports grading.
                $gotcompletionoptions = true;

                $component = "mod_{$this->_modname}";
                $itemnames = component_gradeitems::get_itemname_mapping_for_component($component);

                if (count($itemnames) === 1) {
                    // Only one gradeitem in this activity.
                    // We use the completionusegrade field here.
                    $mform->addElement(
                        'checkbox',
                        'completionusegrade',
                        get_string('completionusegrade', 'completion'),
                        get_string('completionusegrade_desc', 'completion')
                    );
                    $mform->hideIf('completionusegrade', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
                    $mform->addHelpButton('completionusegrade', 'completionusegrade', 'completion');

                    // Complete if the user has reached the pass grade.
                    $mform->addElement(
                        'checkbox',
                        'completionpassgrade', null,
                        get_string('completionpassgrade_desc', 'completion')
                    );
                    $mform->hideIf('completionpassgrade', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
                    $mform->disabledIf('completionpassgrade', 'completionusegrade', 'notchecked');
                    $mform->addHelpButton('completionpassgrade', 'completionpassgrade', 'completion');

                    // The disabledIf logic differs between ratings and other grade items due to different field types.
                    if ($this->_features->rating) {
                        // If using the rating system, there is no grade unless ratings are enabled.
                        $mform->disabledIf('completionusegrade', 'assessed', 'eq', 0);
                        $mform->disabledIf('completionpassgrade', 'assessed', 'eq', 0);
                    } else {
                        // All other field types use the '$gradefieldname' field's modgrade_type.
                        $itemnumbers = array_keys($itemnames);
                        $itemnumber = array_shift($itemnumbers);
                        $gradefieldname = component_gradeitems::get_field_name_for_itemnumber($component, $itemnumber, 'grade');
                        $mform->disabledIf('completionusegrade', "{$gradefieldname}[modgrade_type]", 'eq', 'none');
                        $mform->disabledIf('completionpassgrade', "{$gradefieldname}[modgrade_type]", 'eq', 'none');
                    }
                } else if (count($itemnames) > 1) {
                    // There are multiple grade items in this activity.
                    // Show them all.
                    $options = [
                        '' => get_string('activitygradenotrequired', 'completion'),
                    ];
                    foreach ($itemnames as $itemnumber => $itemname) {
                        $options[$itemnumber] = get_string("grade_{$itemname}_name", $component);
                    }

                    $mform->addElement(
                        'select',
                        'completiongradeitemnumber',
                        get_string('completionusegrade', 'completion'),
                        $options
                    );
                    $mform->hideIf('completiongradeitemnumber', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);

                    // Complete if the user has reached the pass grade.
                    $mform->addElement(
                        'checkbox',
                        'completionpassgrade', null,
                        get_string('completionpassgrade_desc', 'completion')
                    );
                    $mform->hideIf('completionpassgrade', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
                    $mform->disabledIf('completionpassgrade', 'completiongradeitemnumber', 'eq', '');
                    $mform->addHelpButton('completionpassgrade', 'completionpassgrade', 'completion');
                }
            }

            // Automatic completion according to module-specific rules
            $this->_customcompletionelements = $this->add_completion_rules();
            foreach ($this->_customcompletionelements as $element) {
                $mform->hideIf($element, 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
            }

            $gotcompletionoptions = $gotcompletionoptions ||
                count($this->_customcompletionelements)>0;

            // Automatic option only appears if possible
            if ($gotcompletionoptions) {
                $mform->getElement('completion')->addOption(
                    get_string('completion_automatic', 'completion'),
                    COMPLETION_TRACKING_AUTOMATIC);
            }

            // Completion expected at particular date? (For progress tracking)
            $mform->addElement('date_time_selector', 'completionexpected', get_string('completionexpected', 'completion'),
                    array('optional' => true));
            $mform->addHelpButton('completionexpected', 'completionexpected', 'completion');
            $mform->hideIf('completionexpected', 'completion', 'eq', COMPLETION_TRACKING_NONE);
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
}
