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
 * The mod_assign submission status updated event.
 *
 * @package    mod_assign
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_assign submission status updated event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string newstatus: status of submission.
 * }
 *
 * @package    mod_assign
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_status_updated extends base {
    /**
     * Create instance of event.
     *
     * @since Moodle 2.7
     *
     * @param \assign $assign
     * @param \stdClass $submission
     * @return submission_status_updated
     */
    public static function create_from_submission(\assign $assign, \stdClass $submission) {
        $data = array(
            'context' => $assign->get_context(),
            'objectid' => $submission->id,
            'relateduserid' => ($assign->get_instance()->teamsubmission) ? null : $submission->userid,
            'other' => array(
                'newstatus' => $submission->status
            )
        );
        /** @var submission_status_updated $event */
        $event = self::create($data);
        $event->set_assign($assign);
        $event->add_record_snapshot('assign_submission', $submission);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has updated the status of the submission with id '$this->objectid' for " .
            "the assignment with course module id '$this->contextinstanceid' to the status '{$this->other['newstatus']}'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsubmissionstatusupdated', 'mod_assign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'assign_submission';
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['newstatus'])) {
            throw new \coding_exception('The \'newstatus\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'assign_submission', 'restore' => 'submission');
    }

    public static function get_other_mapping() {
        // Nothing to map.
        return false;
    }
}
