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
 * User deleted event.
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event when user profile is deleted.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      @type string username name of user.
 *      @type string email user email.
 *      @type string idnumber user idnumber.
 *      @type string picture user picture.
 *      @type int mnethostid mnet host id.
 * }
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_deleted extends base {

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'user';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventuserdeleted');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return 'User profile deleted for userid ' . $this->objectid;
    }

    /**
     * Return name of the legacy event, which is replaced by this event.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'user_deleted';
    }

    /**
     * Return user_deleted legacy event data.
     *
     * @return \stdClass user data.
     */
    protected function get_legacy_eventdata() {
        $user = $this->get_record_snapshot('user', $this->data['objectid']);
        $user->deleted = 0;
        $user->username = $this->data['other']['username'];
        $user->email = $this->data['other']['email'];
        $user->idnumber = $this->data['other']['idnumber'];
        $user->picture = $this->data['other']['picture'];
        return $user;
    }

    /**
     * Returns array of parameters to be passed to legacy add_to_log() function.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $user = $this->get_record_snapshot('user', $this->data['objectid']);
        return array(SITEID, 'user', 'delete', "view.php?id=".$user->id, $user->firstname.' '.$user->lastname);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['username'])) {
            throw new \coding_exception('username must be set in $other.');
        }

        if (!isset($this->other['email'])) {
            throw new \coding_exception('email must be set in $other.');
        }

        if (!isset($this->other['idnumber'])) {
            throw new \coding_exception('idnumber must be set in $other.');
        }

        if (!isset($this->other['picture'])) {
            throw new \coding_exception('picture must be set in $other.');
        }

        if (!isset($this->other['mnethostid'])) {
            throw new \coding_exception('mnethostid must be set in $other.');
        }
    }
}
