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

namespace mod_assign\event;

use assign;
use coding_exception;
use stdClass;

/**
 * The mod_assign submission removed event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int submissionid: ID number of this submission.
 *      - int submissionattempt: Number of attempts made on this submission.
 *      - string submissionstatus: Status of the submission.
 *      - int groupid: (optional) The group ID if this is a teamsubmission.
 *      - string groupname: (optional) The name of the group if this is a teamsubmission.
 * }
 *
 * @package    mod_assign
 * @since      Moodle 4.0
 * @copyright  2022 TU Berlin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_removed extends base {
    /**
     * Create instance of event.
     *
     * @param assign $assign
     * @param stdClass $submission
     * @return submission_removed
     * @throws coding_exception
     */
    public static function create_from_submission(assign $assign, stdClass $submission): submission_removed {
        $groupname = null;
        $groupid = 0;
        if (empty($submission->userid) && !empty($submission->groupid)) {
            $groupname = groups_get_group_name($submission->groupid);
            $groupid = $submission->groupid;
        }
        $data = [
            'context' => $assign->get_context(),
            'objectid' => $submission->id,
            'relateduserid' => $assign->get_instance()->teamsubmission ? null : $submission->userid,
            'anonymous' => $assign->is_blind_marking() ? 1 : 0,
            'other' => [
                'submissionid' => $submission->id,
                'submissionattempt' => $submission->attemptnumber,
                'submissionstatus' => $submission->status,
                'groupid' => $groupid,
                'groupname' => $groupname
            ]
        ];
        /** @var submission_removed $event */
        $event = self::create($data);
        $event->set_assign($assign);
        $event->add_record_snapshot('assign_submission', $submission);
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'assign_submission';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     * @throws coding_exception
     */
    public static function get_name(): string {
        return get_string('eventsubmissionremoved', 'mod_assign');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        $descriptionstring = "The user with id '$this->userid' removed the submission with id '$this->objectid' in " .
            "the assignment with course module id '$this->contextinstanceid' submitted by ";
        if (!empty($this->other['groupid'])) {
            $groupname = $this->other['groupname'];
            $groupid = $this->other['groupid'];
            $descriptionstring .= "the group '$groupname' with id '$groupid'.";
        } else {
            $descriptionstring .= "the user with id '$this->relateduserid'.";
        }
        return $descriptionstring;
    }

    /**
     * Custom validation.
     *
     * @return void
     * @throws coding_exception
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['submissionid'])) {
            throw new coding_exception('The \'submissionid\' value must be set in other.');
        }
        if (!isset($this->other['submissionattempt'])) {
            throw new coding_exception('The \'submissionattempt\' value must be set in other.');
        }
        if (!isset($this->other['submissionstatus'])) {
            throw new coding_exception('The \'submissionstatus\' value must be set in other.');
        }
    }

    /**
     * Get objectid mapping.
     *
     * @return array
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'assign_submission', 'restore' => 'submission'];
    }

    /**
     * Get other mapping.
     *
     * @return array
     */
    public static function get_other_mapping(): array {
        $othermapped = [];
        $othermapped['submissionid'] = ['db' => 'assign_submission', 'restore' => 'submission'];
        $othermapped['groupid'] = ['db' => 'groups', 'restore' => 'group'];

        return $othermapped;
    }
}
