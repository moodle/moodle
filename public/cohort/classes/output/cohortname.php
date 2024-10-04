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
 * Contains class core_cohort\output\cohortname
 *
 * @package   core_cohort
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_cohort\output;

use core_external\external_api;
use lang_string;

/**
 * Class to prepare a cohort name for display.
 *
 * @package   core_cohort
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohortname extends \core\output\inplace_editable {
    /**
     * Constructor.
     *
     * @param stdClass $cohort
     */
    public function __construct($cohort) {
        $cohortcontext = \context::instance_by_id($cohort->contextid);
        $editable = has_capability('moodle/cohort:manage', $cohortcontext);
        $displayvalue = format_string($cohort->name, true, array('context' => $cohortcontext));
        parent::__construct('core_cohort', 'cohortname', $cohort->id, $editable,
            $displayvalue,
            $cohort->name,
            new lang_string('editcohortname', 'cohort'),
            new lang_string('newnamefor', 'cohort', $displayvalue));
    }

    /**
     * Updates cohort name and returns instance of this object
     *
     * @param int $cohortid
     * @param string $newvalue
     * @return static
     */
    public static function update($cohortid, $newvalue) {
        global $DB;
        $cohort = $DB->get_record('cohort', array('id' => $cohortid), '*', MUST_EXIST);
        $cohortcontext = \context::instance_by_id($cohort->contextid);
        external_api::validate_context($cohortcontext);
        require_capability('moodle/cohort:manage', $cohortcontext);
        $newvalue = clean_param($newvalue, PARAM_TEXT);
        if (strval($newvalue) !== '') {
            $record = (object)array('id' => $cohort->id, 'name' => $newvalue, 'contextid' => $cohort->contextid);
            cohort_update_cohort($record);
            $cohort->name = $newvalue;
        }
        return new static($cohort);
    }
}
