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
 * This file contains an event for when user tracks is viewed.
 *
 * @package    mod_scorm
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_scorm\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event for when a user tracks is viewed.
 *
 * @property-read array $other {
 *      Extra information about event properties.
 *
 *      @type int attemptid Attempt id.
 *      @type int instanceid Instance id of the scorm activity.
 *      @type int scoid Sco Id for which the trackes are viewed.
 * }
 * @package    mod_scorm
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tracks_viewed extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'User with id ' . $this->userid . ' viewed interactions for user ' . $this->relateduserid;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventtracksviewed', 'mod_scorm');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        $params = array(
            'id' => $this->contextinstanceid,
            'user' => $this->relateduserid,
            'attempt' => $this->other['attemptid'],
            'scoid' => $this->other['scoid']
        );
        return new \moodle_url('/mod/scorm/userreporttracks.php', $params);
    }

    /**
     * Return the legacy event log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'scorm', 'userreporttracks', 'report/userreporttracks.php?id=' . $this->contextinstanceid
            . '&user=' . $this->relateduserid . '&attempt=' . $this->other['attemptid'] . '&scoid=' . $this->other['scoid'],
            $this->other['instanceid'], $this->contextinstanceid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        if (empty($this->other['attemptid'])) {
            throw new \coding_exception('The \\mod_scorm\\event\\tracks_viewed must specify attemptid.');
        }
        if (empty($this->other['instanceid'])) {
            throw new \coding_exception('The \\mod_scorm\\event\\tracks_viewed must specify instanceid of the activity.');
        }
        if (empty($this->other['scoid'])) {
            throw new \coding_exception('The \\mod_scorm\\event\\tracks_viewed must specify scoid for the report.');
        }
        parent::validate_data();
    }
}
