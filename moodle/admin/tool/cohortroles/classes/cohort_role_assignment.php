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
 * Class for cohort_role_assignment persistence.
 *
 * @package    tool_cohortroles
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_cohortroles;

use lang_string;
use core_competency\persistent;

/**
 * Class for loading/storing cohort_role_assignments from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_role_assignment extends persistent {

    /** Table name for user_competency persistency */
    const TABLE = 'tool_cohortroles';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'userid' => array(
                'type' => PARAM_INT,
            ),
            'roleid' => array(
                'type' => PARAM_INT,
            ),
            'cohortid' => array(
                'type' => PARAM_INT,
            )
        );
    }

    /**
     * Validate the user ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_userid($value) {
        global $DB;

        if (!$DB->record_exists('user', array('id' => $value))) {
            return new lang_string('invaliduserid', 'error');
        }

        return true;
    }

    /**
     * Validate the role ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_roleid($value) {
        global $DB;

        if (!$DB->record_exists('role', array('id' => $value))) {
            return new lang_string('invalidroleid', 'error');
        }

        return true;
    }

    /**
     * Validate the cohort ID.
     *
     * @param int $value The value.
     * @return true|lang_string
     */
    protected function validate_cohortid($value) {
        global $DB;

        if (!$DB->record_exists('cohort', array('id' => $value))) {
            return new lang_string('invalidcohortid', 'error');
        }

        return true;
    }

}
