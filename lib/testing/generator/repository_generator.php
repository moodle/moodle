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
 * Repository data generator
 *
 * @package    repository
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Repository data generator class
 *
 * @package    core
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.5.1
 */
class testing_repository_generator extends component_generator_base {

    /**
     * Number of instances created
     * @var int
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
     * Returns repository type name
     *
     * @return string name of the type of repository
     * @throws coding_exception if class invalid
     */
    public function get_typename() {
        $matches = null;
        if (!preg_match('/^repository_([a-z0-9_]+)_generator$/', get_class($this), $matches)) {
            throw new coding_exception('Invalid repository generator class name: '.get_class($this));
        }
        if (empty($matches[1])) {
            throw new coding_exception('Invalid repository generator class name: '.get_class($this));
        }
        return $matches[1];
    }

    /**
     * Fill in record defaults.
     *
     * @param array $record
     * @return array
     */
    protected function prepare_record(array $record) {
        if (!isset($record['name'])) {
            $record['name'] = $this->get_typename() . ' ' . $this->instancecount;
        }
        if (!isset($record['contextid'])) {
            $record['contextid'] = context_system::instance()->id;
        }
        return $record;
    }

    /**
     * Fill in type record defaults.
     *
     * @param array $record
     * @return array
     */
    protected function prepare_type_record(array $record) {
        if (!isset($record['pluginname'])) {
            $record['pluginname'] = '';
        }
        if (!isset($record['enableuserinstances'])) {
            $record['enableuserinstances'] = 1;
        }
        if (!isset($record['enablecourseinstances'])) {
            $record['enablecourseinstances'] = 1;
        }
        return $record;
    }

    /**
     * Create a test repository instance.
     *
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass repository instance record
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/repository/lib.php');

        $this->instancecount++;
        $record = (array) $record;

        $typeid = $DB->get_field('repository', 'id', array('type' => $this->get_typename()), MUST_EXIST);
        $instanceoptions = repository::static_function($this->get_typename(), 'get_instance_option_names');

        if (empty($instanceoptions)) {
            // There can only be one instance of this repository, and it should have been created along with the type.
            $id = $DB->get_field('repository_instances', 'id', array('typeid' => $typeid), MUST_EXIST);
        } else {
            // Create the new instance, but first make sure all the required parameters are set.
            $record = $this->prepare_record($record);

            if (empty($record['contextid'])) {
                throw new coding_exception('contextid must be present in testing_repository_generator::create_instance() $record');
            }

            foreach ($instanceoptions as $option) {
                if (!isset($record[$option])) {
                    throw new coding_exception("$option must be present in testing_repository_generator::create_instance() \$record");
                }
            }

            $context = context::instance_by_id($record['contextid']);
            unset($record['contextid']);
            if (!in_array($context->contextlevel, array(CONTEXT_SYSTEM, CONTEXT_COURSE, CONTEXT_USER))) {
                throw new coding_exception('Wrong contextid passed in testing_repository_generator::create_instance() $record');
            }

            $id = repository::static_function($this->get_typename(), 'create', $this->get_typename(), 0, $context, $record);
        }

        return $DB->get_record('repository_instances', array('id' => $id), '*', MUST_EXIST);
    }

    /**
     * Create the type of repository.
     *
     * @param stdClass|array $record data to use to set up the type
     * @param array $options options for the set up of the type
     *
     * @return stdClass repository type record
     */
    public function create_type($record = null, array $options = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/repository/lib.php');

        $record = (array) $record;
        $type = $this->get_typename();

        $typeoptions = repository::static_function($type, 'get_type_option_names');
        $instanceoptions = repository::static_function($type, 'get_instance_option_names');

        // The type allow for user and course instances.
        if (!empty($instanceoptions)) {
            $typeoptions[] = 'enableuserinstances';
            $typeoptions[] = 'enablecourseinstances';
        }

        // Make sure all the parameters are set.
        $record = $this->prepare_type_record($record);
        foreach ($typeoptions as $option) {
            if (!isset($record[$option])) {
                throw new coding_exception("$option must be present in testing::create_repository_type() $record");
            }
        }

        // Limit to allowed options.
        $record = array_intersect_key($record, array_flip($typeoptions));

        // Create the type.
        $plugintype = new repository_type($type, $record);
        $plugintype->create(false);

        return $DB->get_record('repository', array('type' => $type));
    }
}
