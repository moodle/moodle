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
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Module generator base class.
 *
 * Extend in mod/xxxx/tests/generator/lib.php as class mod_xxxx_generator.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class phpunit_module_generator {
    /** @var phpunit_data_generator@var  */
    protected $datagenerator;

    /** @var number of created instances */
    protected $instancecount = 0;

    public function __construct(phpunit_data_generator $datagenerator) {
        $this->datagenerator = $datagenerator;
    }

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
     * @param int $courseid
     * @param array $options: section, visible
     * @return int $cm instance id
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
        foreach ($options as $key=>$value) {
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
     * Create a test module
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record
     */
    abstract public function create_instance($record = null, array $options = null);
}
