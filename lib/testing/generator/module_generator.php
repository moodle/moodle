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
 * Module generator base class.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Module generator base class.
 *
 * Extend in mod/xxxx/tests/generator/lib.php as class mod_xxxx_generator.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class testing_module_generator extends component_generator_base {

    /**
     * @var number of created instances
     */
    protected $instancecount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->instancecount = 0;
    }

    /**
     * Returns module name
     * @return string name of module that this class describes
     * @throws coding_exception if class invalid
     */
    public function get_modulename() {
        $matches = null;
        if (!preg_match('/^mod_([a-z0-9]+)_generator$/', get_class($this), $matches)) {
            throw new coding_exception('Invalid module generator class name: '.get_class($this));
        }

        if (empty($matches[1])) {
            throw new coding_exception('Invalid module generator class name: '.get_class($this));
        }
        return $matches[1];
    }

    /**
     * Create course module and link it to course
     *
     * Since 2.6 it is recommended to use function add_moduleinfo() to create a module.
     *
     * @deprecated since 2.6
     * @see testing_module_generator::create_instance()
     *
     * @param integer $courseid
     * @param array $options section, visible
     * @return integer $cm instance id
     */
    protected function precreate_course_module($courseid, array $options) {
        global $DB, $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $modulename = $this->get_modulename();
        $sectionnum = isset($options['section']) ? $options['section'] : 0;
        unset($options['section']); // Prevent confusion, it would be overridden later in course_add_cm_to_section() anyway.

        $cm = new stdClass();
        $cm->course             = $courseid;
        $cm->module             = $DB->get_field('modules', 'id', array('name'=>$modulename));
        $cm->instance           = 0;
        $cm->section            = 0;
        $cm->idnumber           = isset($options['idnumber']) ? $options['idnumber'] : 0;
        $cm->added              = time();

        $columns = $DB->get_columns('course_modules');
        foreach ($options as $key => $value) {
            if ($key === 'id' or !isset($columns[$key])) {
                continue;
            }
            if (property_exists($cm, $key)) {
                continue;
            }
            $cm->$key = $value;
        }

        $cm->id = $DB->insert_record('course_modules', $cm);

        course_add_cm_to_section($courseid, $cm->id, $sectionnum);

        return $cm->id;
    }

    /**
     * Called after *_add_instance()
     *
     * Since 2.6 it is recommended to use function add_moduleinfo() to create a module.
     *
     * @deprecated since 2.6
     * @see testing_module_generator::create_instance()
     *
     * @param int $id
     * @param int $cmid
     * @return stdClass module instance
     */
    protected function post_add_instance($id, $cmid) {
        global $DB;

        $DB->set_field('course_modules', 'instance', $id, array('id'=>$cmid));

        $instance = $DB->get_record($this->get_modulename(), array('id'=>$id), '*', MUST_EXIST);

        $cm = get_coursemodule_from_id($this->get_modulename(), $cmid, $instance->course, true, MUST_EXIST);
        context_module::instance($cm->id);

        $instance->cmid = $cm->id;

        return $instance;
    }

    /**
     * Merges together arguments $record and $options and fills default module
     * fields that are shared by all module types
     *
     * @param object|array $record fields (different from defaults) for this module
     * @param null|array $options for backward-compatibility this may include fields from course_modules
     *     table. They are merged into $record
     * @throws coding_exception if $record->course is not specified
     */
    protected function prepare_moduleinfo_record($record, $options) {
        global $DB;
        // Make sure we don't modify the original object.
        $moduleinfo = (object)(array)$record;

        if (empty($moduleinfo->course)) {
            throw new coding_exception('module generator requires $record->course');
        }

        $moduleinfo->modulename = $this->get_modulename();
        $moduleinfo->module = $DB->get_field('modules', 'id', array('name' => $moduleinfo->modulename));

        // Allow idnumber to be set as either $options['idnumber'] or $moduleinfo->cmidnumber or $moduleinfo->idnumber.
        // The actual field name is 'idnumber' but add_moduleinfo() expects 'cmidnumber'.
        if (isset($options['idnumber'])) {
            $moduleinfo->cmidnumber = $options['idnumber'];
        } else if (!isset($moduleinfo->cmidnumber) && isset($moduleinfo->idnumber)) {
            $moduleinfo->cmidnumber = $moduleinfo->idnumber;
        }

        // These are the fields from table 'course_modules' in 2.6 when the second
        // argument $options is being deprecated.
        // List excludes fields: instance (does not exist yet), course, module and idnumber (set above)
        $easymergefields = array('section', 'added', 'score', 'indent',
            'visible', 'visibleold', 'groupmode', 'groupingid',
            'completion', 'completiongradeitemnumber', 'completionview', 'completionexpected',
            'availability', 'showdescription');
        foreach ($easymergefields as $key) {
            if (isset($options[$key])) {
                $moduleinfo->$key = $options[$key];
            }
        }

        // Set default values. Note that visibleold and completiongradeitemnumber are not used when creating a module.
        $defaults = array(
            'section' => 0,
            'visible' => 1,
            'visibleoncoursepage' => 1,
            'cmidnumber' => '',
            'groupmode' => 0,
            'groupingid' => 0,
            'availability' => null,
            'completion' => 0,
            'completionview' => 0,
            'completionexpected' => 0,
            'conditiongradegroup' => array(),
            'conditionfieldgroup' => array(),
            'conditioncompletiongroup' => array()
        );
        foreach ($defaults as $key => $value) {
            if (!isset($moduleinfo->$key)) {
                $moduleinfo->$key = $value;
            }
        }

        return $moduleinfo;
    }

    /**
     * Creates an instance of the module for testing purposes.
     *
     * Module type will be taken from the class name. Each module type may overwrite
     * this function to add other default values used by it.
     *
     * @param array|stdClass $record data for module being generated. Requires 'course' key
     *     (an id or the full object). Also can have any fields from add module form.
     * @param null|array $options general options for course module. Since 2.6 it is
     *     possible to omit this argument by merging options into $record
     * @return stdClass record from module-defined table with additional field
     *     cmid (corresponding id in course_modules table)
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/course/modlib.php');

        $this->instancecount++;

        // Merge options into record and add default values.
        $record = $this->prepare_moduleinfo_record($record, $options);

        // Retrieve the course record.
        if (!empty($record->course->id)) {
            $course = $record->course;
            $record->course = $record->course->id;
        } else {
            $course = get_course($record->course);
        }

        // Fill the name and intro with default values (if missing).
        if (empty($record->name)) {
            $record->name = get_string('pluginname', $this->get_modulename()).' '.$this->instancecount;
        }
        if (empty($record->introeditor) && empty($record->intro)) {
            $record->intro = 'Test '.$this->get_modulename().' ' . $this->instancecount;
        }
        if (empty($record->introeditor) && empty($record->introformat)) {
            $record->introformat = FORMAT_MOODLE;
        }

        if (isset($record->tags) && !is_array($record->tags)) {
            $record->tags = preg_split('/\s*,\s*/', trim($record->tags), -1, PREG_SPLIT_NO_EMPTY);
        }

        // Before Moodle 2.6 it was possible to create a module with completion tracking when
        // it is not setup for course and/or site-wide. Display debugging message so it is
        // easier to trace an error in unittests.
        if ($record->completion && empty($CFG->enablecompletion)) {
            debugging('Did you forget to set $CFG->enablecompletion before generating module with completion tracking?', DEBUG_DEVELOPER);
        }
        if ($record->completion && empty($course->enablecompletion)) {
            debugging('Did you forget to enable completion tracking for the course before generating module with completion tracking?', DEBUG_DEVELOPER);
        }

        // Add the module to the course.
        $moduleinfo = add_moduleinfo($record, $course, $mform = null);

        // Prepare object to return with additional field cmid.
        $instance = $DB->get_record($this->get_modulename(), array('id' => $moduleinfo->instance), '*', MUST_EXIST);
        $instance->cmid = $moduleinfo->coursemodule;
        return $instance;
    }

    /**
     * Generates a piece of content for the module.
     * User is usually taken from global $USER variable.
     * @param stdClass $instance object returned from create_instance() call
     * @param stdClass|array $record
     * @return stdClass generated object
     * @throws coding_exception if function is not implemented by module
     */
    public function create_content($instance, $record = array()) {
        throw new coding_exception('Module generator for '.$this->get_modulename().' does not implement method create_content()');
    }
}
