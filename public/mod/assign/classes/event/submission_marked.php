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
 * The mod_assign submission marked event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string mark: Mark value.
 *      - bool draft: (optional) Is marking still in progress?
 * }
 *
 * @package    mod_assign
 * @since      Moodle 5.3
 * @copyright  2026 Catalyst IT Australia Pty Ltd
 * @author     Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_marked extends base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create instance of event.
     *
     * @param assign $assign
     * @param stdClass $grade
     * @param stdClass $mark
     * @return submission_marked
     */
    public static function create_from_mark(assign $assign, stdClass $grade, stdClass $mark) {
        $data = [
            'context' => $assign->get_context(),
            'objectid' => $mark->id,
            'relateduserid' => $grade->userid,
            'other' => [
                'mark' => $mark->mark,
            ],
        ];
        if ($mark->workflowstate !== ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW) {
            $data['other']['draft'] = true;
        }
        self::$preventcreatecall = false;
        /** @var submission_marked $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_assign($assign);
        $event->add_record_snapshot('assign_grades', $grade);
        $event->add_record_snapshot('assign_mark', $mark);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has marked the submission '$this->objectid' for the user with " .
            "id '$this->relateduserid' for the assignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsubmissionmarked', 'mod_assign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'assign_mark';
    }

    /**
     * Custom validation.
     *
     * @throws coding_exception
     * @return void
     */
    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new coding_exception(
                'cannot call submission_marked::create() directly, use submission_marked::create_from_mark() instead.',
            );
        }

        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new coding_exception('The \'relateduserid\' must be set.');
        }
    }

    /**
     * Get objectid mapping.
     *
     * @return array
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'assign_mark', 'restore' => 'mark'];
    }
}
