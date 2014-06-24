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
 * Category deleted event.
 *
 * @package    core
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Category deleted event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string name: category name.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_category_deleted extends base {

    /**
     * The course category class used for legacy reasons.
     */
    private $coursecat;

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'course_categories';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcoursecategorydeleted');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' deleted the course category with id '$this->objectid'.";
    }

    /**
     * Returns the name of the legacy event.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'course_category_deleted';
    }

    /**
     * Returns the legacy event data.
     *
     * @return \coursecat the category that was deleted
     */
    protected function get_legacy_eventdata() {
        return $this->coursecat;
    }

    /**
     * Set custom data of the event - deleted coursecat.
     *
     * @param \coursecat $coursecat
     */
    public function set_coursecat(\coursecat $coursecat) {
        $this->coursecat = $coursecat;
    }

    /**
     * Returns deleted coursecat for event observers.
     *
     * @throws \coding_exception
     * @return \coursecat
     */
    public function get_coursecat() {
        if ($this->is_restored()) {
            throw new \coding_exception('Function get_coursecat() can not be used on restored events.');
        }
        return $this->coursecat;
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array(SITEID, 'category', 'delete', 'index.php', $this->other['name'] . '(ID ' . $this->objectid . ')');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }
    }
}
