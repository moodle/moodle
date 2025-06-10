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
 * Library functions for the edit dates report.
 *
 * @package   report_editdates
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Simple class capturing the information needed to add
 * one date setting to the editing form.
 *
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_date_setting {
    /**
     * @var string label that should be displayed on the form.
     */
    public $label;
    /**
     * @var int the current value of this setting. Used to init the form.
     */
    public $currentvalue;
    /**
     * @var string one of the consts DATE or DATETIME defined below.
     */
    public $type;
    /**
     * @var bool whether this date can be enabled/disabled.
     * Option passed when adding the element to the form.
     */
    public $isoptional;
    /**
     * @var int only relevant for datetime elements.
     * Option passed when adding the element to the form.
     */
    public $getstep;
    /**
     * Constructor. A quick way to create an initialise an instance.
     */
    public function __construct($label, $currentvalue, $type, $isoptional, $getstep = 1) {
        $this->label = $label;
        $this->currentvalue = $currentvalue;
        $this->type = $type;
        $this->isoptional = $isoptional;
        $this->getstep = $getstep;
    }
}


/**
 * Base class for objects that handle the dates for a particular
 * type of activity module.
 *
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class report_editdates_mod_date_extractor {
    const DATE = 'date_selector';
    const DATETIME = 'date_time_selector';
    /** @var object the course database row. */
    protected $course;
    /**
     * @var string the type of activity we handle.
     * E.g. 'quiz' or 'forum'.
     */
    protected $type;
    /** @var array the data for these modules. */
    protected $mods;

    /** @var array a static array to cache the objects of child classes */
    private static $moddateextractor = array();

    /**
     * Constructor.
     * @param object $course the course database row.
     * @param unknown_type $type the type of module to handle.
     */
    public function __construct($course, $type) {
        $this->course = $course;
        $this->type = $type;
    }

    /**
     * This static function is used to create and cache objects of mod's date extractor classes
     * @param String $modname the name of activity/resource e.g 'assignment', 'quiz'
     * @return report_editdates_mod_date_extractor|null the extractor
     */
    public static function make($modname, $course) {
        global $CFG;
        // Check if static array already has an object for this mod extractor class.
        if (array_key_exists($modname, self::$moddateextractor)) {
            self::$moddateextractor[$modname];
        }

        // First, see if the plugin has implemented support within itself. This will be
        // a class in the plugins classes folder that can be loaded using auto-loading.
        $classname = 'mod_' . $modname . '_report_editdates_integration';
        if (class_exists($classname)) {
            self::$moddateextractor[$modname] = new $classname($course);
            return self::$moddateextractor[$modname];
        }

        // Create the new object of this mods date extractor file.
        $filename = $CFG->dirroot . '/report/editdates/mod/' . $modname . 'dates.php';
        if (file_exists($filename)) {
            include_once($filename);
            $classname = 'report_editdates_mod_'.$modname.'_date_extractor';
            if (class_exists($classname)) {
                self::$moddateextractor[$modname] = new $classname($course);
                return self::$moddateextractor[$modname];
            }
        }
        self::$moddateextractor[$modname] = null;
        return self::$moddateextractor[$modname];
    }

    /**
     * Load all the data we will need (in one go for efficiency).
     */
    public function load_data() {
        global $DB;
        $this->mods = $DB->get_records($this->type,    array('course' => $this->course->id));
    }

    /**
     * Get a list of the settings required for this course_module instance.
     * (See the quiz example below.)
     * @param cm_info $cm the activity to return the settings for.
     * @return array The array keys are strings that identif y each setting.
     * The values are report_editdates_date_setting objects.
     */
    abstract public function get_settings(cm_info $cm);

    /**
     * Validate the submitted dates for this course_module instance.
     * (See the quiz example below.)
     * @param cm_info $cm the activity to validate the dates for.
     * @param array $dates an array with array keys matching those
     * returned by get_settings(), and the new
     * dates as values.
     * @return array Any validation errors. The array keys need to
     * match the keys returned by get_settings().
     * Return an empty array if there are no erros.
     */
    abstract public function validate_dates(cm_info $cm, array $dates);

    /**
     * Save the new dates for this course_module instance.
     * @param cm_info $cm the activity to save the dates for.
     */
    public function save_dates(cm_info $cm, array $dates) {
        global $DB;
        $updateobj = new stdClass();
        $updateobj->id = $cm->instance;
        foreach ($this->get_settings($cm) as $name => $setting) {
            $updateobj->$name = $dates[$name];
        }
        $updateobj->timemodified = time();
        $DB->update_record($this->type, $updateobj);
    }
}


/**
 * Base class for objects that handle the dates for blocks.
 *
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class report_editdates_block_date_extractor {
    const DATE = 'date_selector';
    const DATETIME = 'date_time_selector';
    /** @var object the course database row. */
    protected $course;
    /**
     * @var string the type of activity we handle.
     * E.g. 'quiz' or 'forum'.
     */
    protected $type;
    /** @var array the data for these modules. */
    protected $blocks;

    /** @var array a static array to cache the objects of child classes */
    private static $blockdateextractor = array();

    /**
     * Constructor.
     * @param object $course the course database row.
     * @param $type the type of block to handle.
     */
    public function __construct($course, $type="block_instance") {
        $this->course = $course;
        $this->type = $type;
    }

    /**
     * This static function is used to create and cache objects of block's date extractor classes
     * @param String $blockname the name of the block e.g 'html'
     * @return report_editdates_block_date_extractor|null the extractor
     */
    public static function make($blockname, $course) {
        global $CFG;
        // Check if static array already has an object for this mod extractor class.
        if (isset(self::$blockdateextractor[$blockname])) {
            self::$blockdateextractor[$blockname];
        }

        // First, see if the plugin has implemented support within itself. This will be
        // a class in the plugins classes folder that can be loaded using auto-loading.
        $classname = 'block_' . $blockname . '_report_editdates_integration';
        if (class_exists($classname)) {
            self::$blockdateextractor[$blockname] = new $classname($course);
            return self::$blockdateextractor[$blockname];
        }

        // Create the new object of this mods date exractor file.
        $filename = $CFG->dirroot . '/report/editdates/blocks/' . $blockname . 'dates.php';
        if (file_exists($filename)) {
            include_once($filename);
            $classname = 'report_editdates_block_'.$blockname.'_date_extractor';
            if (class_exists($classname)) {
                self::$blockdateextractor[$blockname] = new $classname($course);
                return self::$blockdateextractor[$blockname];
            }
        }

        self::$blockdateextractor[$blockname] = null;
        return self::$blockdateextractor[$blockname];
    }

    /**
     * Load all the data we will need (in one go for efficiency).
     */
    public function load_data() {
        global $DB;
        $coursecontext = context_course::instance($this->course->id);
        $this->blocks = $DB->get_records('block_instances',
                array('blockname' => $this->type, 'parentcontextid' => $coursecontext->id));
    }

    /**
     * Get a list of the settings required for this course_module instance.
     * (See the quiz example below.)
     * @param cm_info $cm the activity to return the settings for.
     * @return array The array keys are strings that identif y each setting.
     * The values are report_editdates_date_setting objects.
     */
    abstract public function get_settings(block_base $block);

    /**
     * Validate the submitted dates for this course_module instance.
     * (See the quiz example below.)
     * @param cm_info $cm the activity to validate the dates for.
     * @param array $dates an array with array keys matching those
     * returned by get_settings(), and the new
     * dates as values.
     * @return array Any validation errors. The array keys need to
     * match the keys returned by get_settings().
     * Return an empty array if there are no erros.
     */
    abstract public function validate_dates(block_base $block, array $dates);

    /**
     * Save the new dates for this course_module instance.
     * @param cm_info $cm the activity to save the dates for.
     */
    public function save_dates(block_base $block, array $dates) {
        global $DB;

        // Set the dates in block's config and update the config field in DB.
        foreach ($this->get_settings($block) as $name => $setting) {
            $block->config->$name = $dates[$name];
        }

        $DB->set_field('block_instances', 'configdata', base64_encode(serialize($block->config)),
        array('id' => $block->instance->id));

    }
}


/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_editdates_extend_navigation_course($navigation, $course, $context) {
    global $CFG, $OUTPUT;
    if (has_capability('report/editdates:view', $context)) {
        $url = new moodle_url('/report/editdates/index.php', array('id' => $course->id));
        if ($activitytype = optional_param('activitytype', '', PARAM_PLUGIN)) {
            $url->param('activitytype', $activitytype);
        }
        $navigation->add(get_string( 'editdates', 'report_editdates' ),
                $url, navigation_node::TYPE_SETTING, null, 'editdates', new pix_icon('i/report', ''));
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array
 */
function report_editdates_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array(
        '*'                      => get_string('page-x', 'pagetype'),
        'report-*'               => get_string('page-report-x', 'pagetype'),
        'report-editdates-index' => get_string('page-report-editdates-index',  'report_editdates'),
    );
}

/**
 * Update the dates in all the activities and resources in certain sections of the course.
 * This API will not handle course cache rebuild.
 * This should be handled by the calling implementation
 * @param int $courseid the course id.
 * @param array $sectionnums the section numbers to update the activity dates in.
 * @param string $offset a string that could be passed to the first argument
 *                         of the PHP function strtotime (for example "+7 days").
 */
function report_editdates_update_dates_by_section($courseid, array $sectionnums, $offset) {
    global $DB, $CFG;

    if ($courseid == SITEID) {
        return false;
    }

    if (!is_array($sectionnums)) {
        return false;
    }

    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $modinfo = get_fast_modinfo($course);

    $forceddatesettings = array();
    $moddatesettings = array();

    // Loop through each section in the course.
    foreach ($sectionnums as $sectionnum => $value) {
        // Course modules in the section.
        $cms = $modinfo->get_section_info($sectionnum);
        foreach ($cms as $key => $cmid) {
            $cm = $modinfo->get_cm($cmid);

            if (!$cm->has_view()) {
                continue;
            }

            if (!$cm->uservisible) {
                continue;
            }

            // Config date settings forced and this is one of the forced date setting.
            if ( ($CFG->enablecompletion || $CFG->enableavailability)
                    && ($cm->completionexpected != 0 || $cm->availablefrom != 0
                    || $cm->availableuntil != 0 ) ) {
                // Competionexpected is set for this module.
                if ($cm->completionexpected != 0) {
                    $forceddatesettings[$cm->id]['completionexpected'] =
                    strtotime($offset, $cm->completionexpected);
                }
                if ($cm->availablefrom != 0) {
                    // Availablefrom is set for this module.
                    $forceddatesettings[$cm->id]['availablefrom'] =
                    strtotime($offset, $cm->availablefrom);
                }
                if ($cm->availableuntil != 0) {
                    // Availableuntil is set for this module.
                    $forceddatesettings[$cm->id]['availableuntil'] =
                    strtotime($offset, $cm->availableuntil);
                }
            } else {
                // It is module date setting.

                $mod = report_editdates_mod_data_date_extractor::make($cm->modname, $course);
                if ($mod) {
                    // Received date settings of the module.
                    if ($cmdatesettings = $mod->get_settings($cm)) {
                        // Loop through each setting and add to the array.
                        foreach ($cmdatesettings as $cmdatetype => $cmdatesetting) {
                            // Value should be updated only if this mod is enabled.
                            if ($cmdatesetting->currentvalue != 0 ) {
                                $moddatesettings[$cm->id][$cmdatetype] =
                                strtotime($offset, $cmdatesetting->currentvalue);
                            }
                        }
                    }
                }
            }
        } // End of $cms loop for each course module in section.
    } // End of loop for each section in course.

    $transaction = $DB->start_delegated_transaction();
    try {
        // Updating forced settings applied to modules.
        foreach ($forceddatesettings as $cmid => $cmdatsettings) {
            $cm = new stdClass();
            $cm->id = $cmid;
            foreach ($cmdatsettings as $datetype => $value) {
                $cm->$datetype = $value;
            }
            // Update object in course_modules class.
            $DB->update_record('course_modules', $cm, true);
        }

        // Updating mod date settings.
        foreach ($moddatesettings as $cmid => $datesettings) {
            $cm = $modinfo->get_cm($cmid);;
            $modname = $cm->modname;

            $modinstance = report_editdates_mod_data_date_extractor::make($cm->modname, $course);
            if ($modinstance) {
                $modinstance->save_dates($cm, $datesettings);
            }
        }
        $transaction->allow_commit();

    } catch (Exception $e) {
        $transaction->rollback($e);
    }
}

/**
 * Does this cm have any date settings?
 * @param stdClass $cm the course_module settings.
 * @param stdClass $course the course settings.
 * @return bool whether there are any dates to edit for this activity.
 */
function report_editdates_cm_has_dates($cm, $course) {
    global $CFG;

    $coursehasavailability = !empty($CFG->enableavailability);
    $coursehascompletion   = !empty($CFG->enablecompletion) && !empty($course->enablecompletion);
    if ($coursehasavailability || $coursehascompletion) {
        return true;
    }

    return (bool) report_editdates_mod_date_extractor::make($cm->modname, $course);
}
