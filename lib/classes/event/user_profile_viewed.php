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
 * The user profile viewed event.
 *
 * @package    core
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The user profile viewed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int courseid: (optional) id of course.
 *      - string courseshortname: (optional) shortname of course.
 *      - string coursefullname: (optional) fullname of course.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_profile_viewed extends base {

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'user';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserprofileviewed');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        $desc = "The user with id '$this->userid' viewed the profile for the user with id '$this->relateduserid'";
        $desc .= ($this->contextlevel == CONTEXT_COURSE) ? " in the course with id '$this->courseid'." : ".";
        return $desc;
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        if ($this->contextlevel == CONTEXT_COURSE) {
            return new \moodle_url('/user/view.php', array('id' => $this->relateduserid, 'course' => $this->courseid));
        }
        return new \moodle_url('/user/profile.php', array('id' => $this->relateduserid));
    }

    /**
     * Returns array of parameters to be passed to legacy add_to_log() function.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        if ($this->contextlevel == CONTEXT_COURSE) {
            return array($this->courseid, 'user', 'view', 'view.php?id=' . $this->relateduserid . '&course=' .
                $this->courseid, $this->relateduserid);
        }
        return null;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
    }
}
