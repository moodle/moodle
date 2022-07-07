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
 * H5P activity report viewed.
 *
 * @package     mod_h5pactivity
 * @copyright   2020 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The report_viewed event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int instanceid: The instance ID
 *      - int userid: The optional user ID
 *      - int attemptid: The optional attempt ID
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_viewed extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['objecttable'] = 'h5pactivity';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('report_viewed', 'mod_h5pactivity');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (empty($this->other['instanceid'])) {
            throw new \coding_exception('The \'instanceid\' value must be set in other.');
        }
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the H5P report for the H5P with " .
            "course module id '$this->contextinstanceid'.";
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        $params = ['a' => $this->other['instanceid']];

        if (!empty($this->other['userid'])) {
            $params['userid'] = $this->other['userid'];
        }

        if (!empty($this->other['attemptid'])) {
            $params['attemptid'] = $this->other['attemptid'];
        }

        return new \moodle_url('/mod/h5pactivity/report.php', $params);
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the objectid to it's new value in the new course.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'h5pactivity', 'restore' => 'h5pactivity'];
    }

    /**
     * Return the other field mapping.
     *
     * @return array
     */
    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['attemptid'] = array('db' => 'h5pactivity_attempts', 'restore' => 'h5pactivity_attempts');
        $othermapped['userid'] = array('db' => 'user', 'restore' => 'user');
        return $othermapped;
    }

}
