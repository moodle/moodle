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
 * Course section created event.
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Course section created event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int sectionnum: section number.
 * }
 *
 * @package    core
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_section_created extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = 'course_sections';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Creates event from the section object
     *
     * @param \stdClass $section
     * @return course_section_created
     */
    public static function create_from_section($section) {
        $event = self::create([
            'context' => \context_course::instance($section->course),
            'objectid' => $section->id,
            'other' => ['sectionnum' => $section->section]
        ]);
        $event->add_record_snapshot('course_sections', $section);
        return $event;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcoursesectioncreated');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' created section number '{$this->other['sectionnum']}' for the " .
        "course with id '$this->courseid'";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/course/editsection.php', array('id' => $this->objectid));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['sectionnum'])) {
            throw new \coding_exception('The \'sectionnum\' value must be set in other.');
        }
    }

    /**
     * Mapping for sections object during restore
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return array('db' => 'course_sections', 'restore' => 'course_section');
    }

    /**
     * Mapping for other fields during restore
     *
     * @return bool
     */
    public static function get_other_mapping() {
        // Sectionnum does not need mapping because it's relative.
        return false;
    }
}
