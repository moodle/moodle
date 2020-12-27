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
 * The mod_assign marker updated event.
 *
 * @package    mod_assign
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_assign marker updated event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int markerid: user id of marker.
 * }
 *
 * @package    mod_assign
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marker_updated extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @since Moodle 2.7
     *
     * @param \assign $assign
     * @param \stdClass $user
     * @param \stdClass $marker
     * @return marker_updated
     */
    public static function create_from_marker(\assign $assign, \stdClass $user, \stdClass $marker) {
        $data = array(
            'context' => $assign->get_context(),
            'objectid' => $assign->get_instance()->id,
            'relateduserid' => $user->id,
            'other' => array(
                'markerid' => $marker->id,
            ),
        );
        self::$preventcreatecall = false;
        /** @var marker_updated $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_assign($assign);
        $event->add_record_snapshot('user', $user);
        $event->add_record_snapshot('user', $marker);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has set the marker for the user with id '$this->relateduserid' to " .
            "'{$this->other['markerid']}' for the assignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventmarkerupdated', 'mod_assign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'assign';
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $user = $this->get_record_snapshot('user', $this->relateduserid);
        $marker = $this->get_record_snapshot('user', $this->other['markerid']);
        $a = array('id' => $user->id, 'fullname' => fullname($user), 'marker' => fullname($marker));
        $logmessage = get_string('setmarkerallocationforlog', 'assign', $a);
        $this->set_legacy_logdata('set marking allocation', $logmessage);
        return parent::get_legacy_logdata();
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call marker_updated::create() directly, use marker_updated::create_from_marker() instead.');
        }

        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['markerid'])) {
            throw new \coding_exception('The \'markerid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'assign', 'restore' => 'assign');
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['markerid'] = array('db' => 'user', 'restore' => 'user');

        return $othermapped;
    }
}
