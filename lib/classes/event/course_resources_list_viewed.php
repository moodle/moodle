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
 * Event for viewing the list of course resources.
 *
 * @package    core
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event for viewing the list of course resources.
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_resources_list_viewed extends base {

    /** @var array list of resource types for legacy logging */
    private $resourceslist = null;

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with the id '$this->userid' viewed the list of resources in the course with the id '$this->courseid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcoursemoduleinstancelistviewed', 'core');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url("/course/resources.php", array('id' => $this->courseid));
    }

    /**
     * List of resource types enabled in the system. This is used for legacy logging to log one record for each resource type.
     *
     * There is no public getter for this data because it does not depend on the
     * course. It always includes the list of all resource types in the system
     * even when some of them are not present in the course.
     *
     * @param array $data
     */
    public function set_legacy_logdata($data) {
        $this->resourceslist = $data;
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        if (empty($this->resourceslist)) {
            return null;
        }
        $logs = array();
        foreach ($this->resourceslist as $resourcename) {
            $logs[] = array($this->courseid, $resourcename, 'view all', 'index.php?id=' . $this->courseid, '');
        }
        return $logs;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        if ($this->contextlevel != CONTEXT_COURSE) {
            throw new \coding_exception('Context passed must be course context.');
        }
    }
}
