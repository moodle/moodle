<?php
require_once ($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');

/**
 * This class adds extra methods to form wrapper specific to be used for module
 * add / update forms mod/{modname}/mod_form.php replaced deprecated mod/{modname}/mod.html
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
     * @var mixed
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
     * List of modform features
     */
    protected $_features;
    /**
     * @var array Custom completion-rule elements, if enabled
     */
    protected $_customcompletionelements;
    /**
     * @var string name of module
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

    function moodleform_mod($current, $section, $cm, $course) {
        global $CFG;

        $this->current   = $current;
        $this->_instance = $current->instance;
        $this->_section  = $section;
        $this->_cm       = $cm;
        if ($this->_cm) {
            $this->context = context_module::instance($this->_cm->id);
        } else {
            $this->context = context_course::instance($course->id);
        }

        // Set the course format.
        require_once($CFG->dirroot . '/course/format/lib.php');
        $this->courseformat = course_get_format($course);

        // Guess module name
        $matches = array();
        if (!preg_match('/^mod_([^_]+)_mod_form$/', get_class($this), $matches)) {
            debugging('Use $modname parameter or rename form to mod_xx_mod_form, where xx is name of your module');
            print_error('unknownmodulename');
        }
        $this->_modname = $matches[1];
        $this->init_features();
        parent::moodleform('modedit.php');
    }

    protected function init_features() {
        global $CFG;

        $this->_features = new stdClass();
        $this->_features->groups            = plugin_supports('mod', $this->_modname, FEATURE_GROUPS, true);
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
    }

    /**
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

            if ($this->_features->gradecat) {
                $gradecat = false;
                if (!empty($CFG->enableoutcomes) and $this->_features->outcomes) {
                    $outcomes = grade_outcome::fetch_all_available($COURSE->id);
                    if (!empty($outcomes)) {
                        $gradecat = true;
                    }
                }

                $items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename,'iteminstance'=>$instance, 'courseid'=>$COURSE->id));
                //will be no items if, for example, this activity supports ratings but rating aggregate type == no ratings
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if (!empty($item->outcomeid)) {
                            $elname = 'outcome_'.$item->outcomeid;
                            if ($mform->elementExists($elname)) {
                                $mform->hardFreeze($elname); // prevent removing of existing outcomes
                            }
                        }
                    }

                    foreach ($items as $item) {
                        if (is_bool($gradecat)) {
                            $gradecat = $item->categoryid;
                            continue;
                        }
                        if ($gradecat != $item->categoryid) {
                            //mixed categories
                            $gradecat = false;
                            break;
                        }
                    }
                }

                if ($gradecat === false) {
                    // items and outcomes in different categories - remove the option
                    // TODO: add a "Mixed categories" text instead of removing elements with no explanation
                    if ($mform->elementExists('gradecat')) {
                        $mform->removeElement('gradecat');
                        if ($this->_features->rating) {
                            //if supports ratings then the max grade dropdown wasnt added so the grade box can be removed entirely
                            $mform->removeElement('modstandardgrade');
                        }
                    }
                }
            }
        }

        if ($COURSE->groupmodeforce) {
            if ($mform->elementExists('groupmode')) {
                $mform->hardFreeze('groupmode'); // groupmode can not be changed if forced from course settings
            }
        }

        // Don't disable/remove groupingid if it is currently set to something,
        // otherwise you cannot turn it off at same time as turning off other
        // option (MDL-30764)
        if (empty($this->_cm) || !$this->_cm->groupingid) {
            if ($mform->elementExists('groupmode') && empty($COURSE->groupmodeforce)) {
                $mform->disabledIf('groupingid', 'groupmode', 'eq', NOGROUPS);

            } else if (!$mform->elementExists('groupmode')) {
                // Groupings have no use without groupmode.
                if ($mform->elementExists('groupingid')) {
                    $mform->removeElement('groupingid');
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
                $mform->freeze($this->_customcompletionelements);
            }
        }

        // Freeze admin defaults if required (and not different from default)
        $this->apply_admin_locked_flags();
    }

    // form verification
    function validation($data, $files) {
        global $COURSE, $DB, $CFG;
        $errors = parent::validation($data, $files);

        $mform =& $this->_form;

        $errors = array();

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

        // Ratings: Don't let them select an aggregate type without selecting a scale.
        // If the user has selected to use ratings but has not chosen a scale or set max points then the form is
        // invalid. If ratings have been selected then the user must select either a scale or max points.
        // This matches (horrible) logic in data_preprocessing.
        if (isset($data['assessed']) && $data['assessed'] > 0 && empty($data['scale'])) {
            $errors['assessed'] = get_string('scaleselectionrequired', 'rating');
        }

        // Check that the grade pass is a valid number.
        $gradepassvalid = false;
        if (isset($data['gradepass'])) {
            if (unformat_float($data['gradepass'], true) === false) {
                $errors['gradepass'] = get_string('err_numeric', 'form');
            } else {
                $gradepassvalid = true;
            }
        }

        // Grade to pass: ensure that the grade to pass is valid for points and scales.
        // If we are working with a scale, convert into a positive number for validation.
        if ($gradepassvalid && isset($data['gradepass']) && (!empty($data['grade']) || !empty($data['scale']))) {
            $scale = !empty($data['grade']) ? $data['grade'] : $data['scale'];
            if ($scale < 0) {
                $scalevalues = $DB->get_record('scale', array('id' => -$scale));
                $grade = count(explode(',', $scalevalues->scale));
            } else {
                $grade = $scale;
            }
            if ($data['gradepass'] > $grade) {
                $errors['gradepass'] = get_string('gradepassgreaterthangrade', 'grades', $grade);
            }
        }

        // Completion: Don't let them choose automatic completion without turning
        // on some conditions. Ignore this check when completion settings are
        // locked, as the options are then disabled.
        if (array_key_exists('completion', $data) &&
                $data['completion'] == COMPLETION_TRACKING_AUTOMATIC &&
                !empty($data['completionunlocked'])) {
            if (empty($data['completionview']) && empty($data['completionusegrade']) &&
                !$this->completion_rule_enabled($data)) {
                $errors['completion'] = get_string('badautocompletion', 'completion');
            }
        }

        // Availability: Check availability field does not have errors.
        if (!empty($CFG->enableavailability)) {
            \core_availability\frontend::report_validation_errors($data, $errors);
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
    function standard_coursemodule_elements(){
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
            require_once($CFG->dirroot.'/rating/lib.php');
            $rm = new rating_manager();

            $mform->addElement('header', 'modstandardratings', get_string('ratings', 'rating'));

            $permission=CAP_ALLOW;
            $rolenamestring = null;
            if (!empty($this->_cm)) {
                $context = context_module::instance($this->_cm->id);

                $rolenames = get_role_names_with_caps_in_context($context, array('moodle/rating:rate', 'mod/'.$this->_cm->modname.':rate'));
                $rolenamestring = implode(', ', $rolenames);
            } else {
                $rolenamestring = get_string('capabilitychecknotavailable','rating');
            }
            $mform->addElement('static', 'rolewarning', get_string('rolewarning','rating'), $rolenamestring);
            $mform->addHelpButton('rolewarning', 'rolewarning', 'rating');

            $mform->addElement('select', 'assessed', get_string('aggregatetype', 'rating') , $rm->get_aggregate_types());
            $mform->setDefault('assessed', 0);
            $mform->addHelpButton('assessed', 'aggregatetype', 'rating');

            $mform->addElement('modgrade', 'scale', get_string('scale'), false);
            $mform->disabledIf('scale', 'assessed', 'eq', 0);
            $mform->addHelpButton('scale', 'modgrade', 'grades');
            $mform->setDefault('scale', $CFG->gradepointdefault);

            $mform->addElement('checkbox', 'ratingtime', get_string('ratingtime', 'rating'));
            $mform->disabledIf('ratingtime', 'assessed', 'eq', 0);

            $mform->addElement('date_time_selector', 'assesstimestart', get_string('from'));
            $mform->disabledIf('assesstimestart', 'assessed', 'eq', 0);
            $mform->disabledIf('assesstimestart', 'ratingtime');

            $mform->addElement('date_time_selector', 'assesstimefinish', get_string('to'));
            $mform->disabledIf('assesstimefinish', 'assessed', 'eq', 0);
            $mform->disabledIf('assesstimefinish', 'ratingtime');
        }

        //doing this here means splitting up the grade related settings on the lesson settings page
        //$this->standard_grading_coursemodule_elements();

        $mform->addElement('header', 'modstandardelshdr', get_string('modstandardels', 'form'));

        $mform->addElement('modvisible', 'visible', get_string('visible'));
        if (!empty($this->_cm)) {
            $context = context_module::instance($this->_cm->id);
            if (!has_capability('moodle/course:activityvisibility', $context)) {
                $mform->hardFreeze('visible');
            }
        }

        if ($this->_features->idnumber) {
            $mform->addElement('text', 'cmidnumber', get_string('idnumbermod'));
            $mform->setType('cmidnumber', PARAM_RAW);
            $mform->addHelpButton('cmidnumber', 'idnumbermod');
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
            if ($this->_features->groups || $this->_features->groupings) {
                $mform->addElement('static', 'restrictgroupbutton', '',
                        html_writer::tag('button', get_string('restrictbygroup', 'availability'),
                        array('id' => 'restrictbygroup', 'disabled' => 'disabled')));
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
                $trackingdefault = COMPLETION_TRACKING_MANUAL;
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
                $mform->disabledIf('completionview', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
                $gotcompletionoptions = true;
            }

            // Automatic completion once it's graded
            if (plugin_supports('mod', $this->_modname, FEATURE_GRADE_HAS_GRADE, false)) {
                $mform->addElement('checkbox', 'completionusegrade', get_string('completionusegrade', 'completion'),
                    get_string('completionusegrade_desc', 'completion'));
                $mform->disabledIf('completionusegrade', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
                $mform->addHelpButton('completionusegrade', 'completionusegrade', 'completion');
                $gotcompletionoptions = true;

                // If using the rating system, there is no grade unless ratings are enabled.
                if ($this->_features->rating) {
                    $mform->disabledIf('completionusegrade', 'assessed', 'eq', 0);
                }
            }

            // Automatic completion according to module-specific rules
            $this->_customcompletionelements = $this->add_completion_rules();
            foreach ($this->_customcompletionelements as $element) {
                $mform->disabledIf($element, 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
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
            $mform->addElement('date_selector', 'completionexpected', get_string('completionexpected', 'completion'), array('optional'=>true));
            $mform->addHelpButton('completionexpected', 'completionexpected', 'completion');
            $mform->disabledIf('completionexpected', 'completion', 'eq', COMPLETION_TRACKING_NONE);
        }

        $this->standard_hidden_coursemodule_elements();
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
        $mform->setType('add', PARAM_ALPHA);

        $mform->addElement('hidden', 'update', 0);
        $mform->setType('update', PARAM_INT);

        $mform->addElement('hidden', 'return', 0);
        $mform->setType('return', PARAM_BOOL);

        $mform->addElement('hidden', 'sr', 0);
        $mform->setType('sr', PARAM_INT);
    }

    public function standard_grading_coursemodule_elements() {
        global $COURSE, $CFG;
        $mform =& $this->_form;

        if ($this->_features->hasgrades) {

            if (!$this->_features->rating || $this->_features->gradecat) {
                $mform->addElement('header', 'modstandardgrade', get_string('grade'));
            }

            //if supports grades and grades arent being handled via ratings
            if (!$this->_features->rating) {
                $mform->addElement('modgrade', 'grade', get_string('grade'));
                $mform->addHelpButton('grade', 'modgrade', 'grades');
                $mform->setDefault('grade', $CFG->gradepointdefault);
            }

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
                    if (!$this->_features->rating) {
                        $mform->disabledIf('advancedgradingmethod_'.$areaname, 'grade[modgrade_type]', 'eq', 'none');
                    }

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
                $mform->addElement('select', 'gradecat',
                        get_string('gradecategoryonmodform', 'grades'),
                        grade_get_categories_menu($COURSE->id, $this->_outcomesused));
                $mform->addHelpButton('gradecat', 'gradecategoryonmodform', 'grades');
                if (!$this->_features->rating) {
                    $mform->disabledIf('gradecat', 'grade[modgrade_type]', 'eq', 'none');
                }
            }

            // Grade to pass.
            $mform->addElement('text', 'gradepass', get_string('gradepass', 'grades'));
            $mform->addHelpButton('gradepass', 'gradepass', 'grades');
            $mform->setDefault('gradepass', '');
            $mform->setType('gradepass', PARAM_RAW);
            if (!$this->_features->rating) {
                $mform->disabledIf('gradepass', 'grade[modgrade_type]', 'eq', 'none');
            }
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
            $mform->addElement('checkbox', 'showdescription', get_string('showdescription'));
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
        $mform->closeHeaderBefore('buttonar');
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
     * Get the list of admin settings for this module and apply any defaults/advanced/locked settings.
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
            }
        }
    }
}


