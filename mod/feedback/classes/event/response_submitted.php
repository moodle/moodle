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
 * The mod_feedback response submitted event.
 *
 * @package    mod_feedback
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace mod_feedback\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_feedback response submitted event class.
 *
 * This event is triggered when a feedback response is submitted.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int anonymous: if feedback is anonymous.
 *      - int cmid: course module id.
 *      - int instanceid: id of instance.
 * }
 *
 * @package    mod_feedback
 * @since      Moodle 2.6
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class response_submitted extends \core\event\base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        global $CFG;

        require_once($CFG->dirroot.'/mod/feedback/lib.php');
        $this->data['objecttable'] = 'feedback_completed';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventresponsesubmitted', 'mod_feedback');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' created feedback for the user with id '$this->relateduserid' " .
            "for the feedback activity with course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns relevant URL based on the anonymous mode of the response.
     * @return \moodle_url
     */
    public function get_url() {
        if ($this->other['anonymous'] == FEEDBACK_ANONYMOUS_YES) {
            return new \moodle_url('/mod/feedback/show_entries.php' , array('id' => $this->other['cmid'],
                    'do_show' => 'showoneentry' , 'userid' => $this->relateduserid));
        } else {
            return new \moodle_url('/mod/feedback/show_entries_anonym.php', array('id' => $this->other['cmid'],
                    'do_show' => 'showoneentry', 'showall' => 1, 'showcompleted' => $this->objectid));
        }
    }

    /**
     * Replace add_to_log() statement. Do this only for the case when anonymous mode is off,
     * since this is what was happening before.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        if ($this->anonymous) {
            return null;
        } else {
            return array($this->courseid, 'feedback', 'submit', 'view.php?id=' . $this->other['cmid'],
                    $this->other['instanceid'], $this->other['cmid'], $this->relateduserid);
        }
    }

    /**
     * Define whether a user can view the event or not. Make sure no one except admin can see details of an anonymous response.
     *
     * @deprecated since 2.7
     *
     * @param int|\stdClass $userorid ID of the user.
     * @return bool True if the user can view the event, false otherwise.
     */
    public function can_view($userorid = null) {
        global $USER;
        debugging('can_view() method is deprecated, use anonymous flag instead if necessary.', DEBUG_DEVELOPER);

        if (empty($userorid)) {
            $userorid = $USER;
        }
        if ($this->anonymous) {
            return is_siteadmin($userorid);
        } else {
            return has_capability('mod/feedback:viewreports', $this->context, $userorid);
        }
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception in case of any problems.
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
        if (!isset($this->other['anonymous'])) {
            throw new \coding_exception('The \'anonymous\' value must be set in other.');
        }
        if (!isset($this->other['cmid'])) {
            throw new \coding_exception('The \'cmid\' value must be set in other.');
        }
        if (!isset($this->other['instanceid'])) {
            throw new \coding_exception('The \'instanceid\' value must be set in other.');
        }
    }
}

