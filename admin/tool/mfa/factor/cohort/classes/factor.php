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

namespace factor_cohort;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../../../../../cohort/lib.php');

use stdClass;
use tool_mfa\local\factor\object_factor_base;

/**
 * cohort factor class.
 *
 * @package     factor_cohort
 * @author      Chris Pratt <tonyyeb@gmail.com>
 * @copyright   Chris Pratt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * cohort implementation.
     * This factor is a singleton, return single instance.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors(stdClass $user): array {
        global $DB;
        $records = $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);
        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $user->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * cohort implementation.
     * Factor has no input
     *
     * {@inheritDoc}
     */
    public function has_input(): bool {
        return false;
    }

    /**
     * cohort implementation.
     * Checks whether the user has selected cohorts in any context.
     *
     * {@inheritDoc}
     */
    public function get_state(): string {
        global $USER;
        $cohortstring = get_config('factor_cohort', 'cohorts');
        // Nothing selected, everyone passes.
        if (empty($cohortstring)) {
            return \tool_mfa\plugininfo\factor::STATE_PASS;
        }

        $selected = explode(',', $cohortstring);
        foreach ($selected as $id) {
            if (cohort_is_member($id, $USER->id)) {
                return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
            }
        }

        // If we got here, no cohorts matched, allow access.
        return \tool_mfa\plugininfo\factor::STATE_PASS;
    }

    /**
     * cohort implementation.
     * Cannot set state, return true.
     *
     * @param string $state the state constant to set
     * @return bool
     */
    public function set_state(string $state): bool {
        return true;
    }

    /**
     * cohort implementation.
     * User can not influence. Result is whatever current state is.
     *
     * @param stdClass $user
     */
    public function possible_states(stdClass $user): array {
        return [$this->get_state()];
    }

    /**
     * cohort implementation
     * Formats the cohort list nicely.
     *
     * {@inheritDoc}
     */
    public function get_summary_condition(): string {
        $selectedcohorts = get_config('factor_cohort', 'cohorts');
        if (empty($selectedcohorts)) {
            return get_string('summarycondition', 'factor_cohort', get_string('none'));
        }

        $selectedcohorts = $this->get_cohorts(explode(',', $selectedcohorts));
        if (empty($selectedcohorts)) {
            return get_string('summarycondition', 'factor_cohort', get_string('none'));
        }

        return get_string('summarycondition', 'factor_cohort', implode(', ', $selectedcohorts));
    }

    /**
     * Get cohorts information by given ids.
     *
     * @param array $selectedcohorts List of cohort ids.
     * @return array
     */
    public function get_cohorts(array $selectedcohorts): array {
        global $DB;

        [$insql, $inparams] = $DB->get_in_or_equal($selectedcohorts);
        $sql = "SELECT id, name FROM {cohort} WHERE id $insql";
        $cohorts = $DB->get_records_sql_menu($sql, $inparams);

        return $cohorts;
    }
}
