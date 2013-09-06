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
 * assignment_submitted assessable uploaded event.
 *
 * @package    assignment_submitted
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignment_upload\event;

defined('MOODLE_INTERNAL') || die();

/**
 * assignment_submitted assessable uploaded event class.
 *
 * @package    assignment_submitted
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assessable_submitted extends \core\event\assessable_submitted {

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "User {$this->userid} has submitted the upload submission {$this->objectid}.";
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return stdClass
     */
    protected function get_legacy_eventdata() {
        $eventdata = new \stdClass();
        $eventdata->modulename   = 'assignment';
        $eventdata->cmid         = $this->context->instanceid;
        $eventdata->itemid       = $this->objectid;
        $eventdata->courseid     = $this->courseid;
        $eventdata->userid       = $this->userid;
        return $eventdata;
    }

    /**
     * Return the legacy event name.
     *
     * @return string
     */
    public static function get_legacy_eventname() {
        return 'assessable_files_done';
    }

    /**
     * Get legacy log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'assignment', 'upload', 'view.php?a='.$this->other['assignmentid'],
            $this->other['assignmentid'], $this->context->instanceid);
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_assessable_submitted', 'assignment_submitted');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/assignment/view.php', array('id' => $this->context->instanceid));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'assignment_submissions';
    }

    /**
     * Custom validation
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['submission_editable'])) {
            throw new \coding_exception('Other must contain the key submission_editable.');
        }
    }
}
