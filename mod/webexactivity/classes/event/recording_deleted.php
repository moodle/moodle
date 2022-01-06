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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity\event;

defined('MOODLE_INTERNAL') || die();

/**
 * mod_webexactiviy recording deleted event.
 *
 * @package    mod_webexactvity
 * @category   events
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recording_deleted extends \core\event\base {

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'User with id ' . $this->userid . ' deleted webex recording with id ' . $this->objectid;
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'webexactivity', 'recording deleted', 'view.php?id=' . $this->contextinstanceid,
                'Recording ID '.$this->objectid, $this->contextinstanceid);
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_recording_deleted', 'webexactivity');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/webexactivity/view.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        global $CFG;

        $this->data['crud'] = 'd';
        // Level needed for 2.6, but depeciated in 2.7.
        if ($CFG->version < 2013111899) {
            $this->data['level'] = self::LEVEL_OTHER;
        } else {
            $this->data['edulevel'] = self::LEVEL_OTHER;
        }

        $this->data['objecttable'] = 'webexactivity_recording';
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        // Hack to please the parent class. 'view' was the key used in old add_to_log().
        $this->data['other']['content'] = 'view';
        parent::validate_data();
    }

}
