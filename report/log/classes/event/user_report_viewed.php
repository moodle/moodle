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
 * The report_log user report viewed event.
 *
 * @package    report_log
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_log\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The report_log user report viewed event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string mode: display mode.
 * }
 *
 * @package    report_log
 * @since      Moodle 2.7
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_report_viewed extends \core\event\base {

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
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserreportviewed', 'report_log');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the user log report for the user with id '$this->relateduserid'.";
    }

    /**
     * Return the legacy event log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $url = 'report/log/user.php?id=' . $this->relateduserid . '&course=' . $this->courseid . '&mode=' . $this->other['mode'];
        return array($this->courseid, 'course', 'report log', $url, $this->courseid);
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/report/log/user.php', array('course' => $this->courseid, 'id' => $this->relateduserid,
                'mode' => $this->other['mode']));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (empty($this->other['mode'])) {
            throw new \coding_exception('The \'mode\' value must be set in other.');
        }

        if (empty($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
    }

    public static function get_other_mapping() {
        // Nothing to map.
        return false;
    }
}
