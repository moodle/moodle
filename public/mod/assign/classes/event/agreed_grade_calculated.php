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
 * The mod_assign agreed grade calculated event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string method: Agreed grade multi marking method.
 *      - array markerids: User IDs of markers considered for the agreed grade.
 * }
 *
 * @package    mod_assign
 * @since      Moodle 5.3
 * @copyright  2026 Catalyst IT Australia Pty Ltd
 * @author     Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class agreed_grade_calculated extends base {
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
     * @param string $method Agreed grade multi marking method.
     * @param array $markerids User IDs of markers considered for the agreed grade.
     * @return agreed_grade_calculated
     */
    public static function create_from_grade(assign $assign, stdClass $grade, string $method, array $markerids) {
        $data = [
            'context' => $assign->get_context(),
            'objectid' => $grade->id,
            'relateduserid' => $grade->userid,
            'other' => [
                'method' => $method,
                'markerids' => $markerids,
            ],
        ];
        self::$preventcreatecall = false;
        /** @var agreed_grade_calculated $event */
        $event = self::create($data);
        self::$preventcreatecall = true;
        $event->set_assign($assign);
        $event->add_record_snapshot('assign_grades', $grade);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' calculated the agreed grade using the '{$this->other['method']}' method " .
            "with marks from users with ids '" . implode("', '", $this->other['markerids'] ?? []) . "' " .
            "for the user with id '$this->relateduserid' for the assignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventagreedgradecalculated', 'mod_assign');
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'assign_grades';
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
                'cannot call agreed_grade_calculated::create() directly, ' .
                'use agreed_grade_calculated::create_from_grade() instead.',
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
        return ['db' => 'assign_grades', 'restore' => 'grade'];
    }

    /**
     * Get other mapping.
     *
     * @return array
     */
    public static function get_other_mapping(): array {
        $othermapped = [];

        // Contributing markers are stored as an array of user IDs because the number
        // of markers are variable. Mapping of arrays is not currently supported.
        $othermapped['markerids'] = \core\event\base::NOT_MAPPED;

        return $othermapped;
    }
}
