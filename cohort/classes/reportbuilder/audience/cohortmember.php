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

declare(strict_types=1);

namespace core_cohort\reportbuilder\audience;

use context;
use context_system;
use core_course_category;
use stdClass;
use core_reportbuilder\local\audiences\base;
use core_reportbuilder\local\helpers\database;
use MoodleQuickForm;

/**
 * The backend class for Cohort member audience type
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohortmember extends base {

    /**
     * Adds audience's elements to the given mform
     *
     * @param MoodleQuickForm $mform The form to add elements to
     */
    public function get_config_form(MoodleQuickForm $mform): void {
        $cohorts = self::get_cohorts();
        $mform->addElement('autocomplete', 'cohorts', get_string('selectfromcohort', 'cohort'),
            $cohorts, ['multiple' => true]);
        $mform->addRule('cohorts', null, 'required', null, 'client');
    }

    /**
     * Helps to build SQL to retrieve users that matches the current report audience
     *
     * @param string $usertablealias
     * @return array array of three elements [$join, $where, $params]
     */
    public function get_sql(string $usertablealias): array {
        global $DB;

        $cohorts = $this->get_configdata()['cohorts'];
        [$insql, $inparams] = $DB->get_in_or_equal($cohorts, SQL_PARAMS_NAMED, database::generate_param_name('_'));

        $cm = database::generate_alias();
        $join = "JOIN {cohort_members} {$cm}
                   ON ({$cm}.userid = {$usertablealias}.id)";

        return [$join, "{$cm}.cohortid " . $insql, $inparams];
    }

    /**
     * Return user friendly name of this audience type
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('memberofcohort', 'cohort');
    }

    /**
     * Return the description for the audience.
     *
     * @return string
     */
    public function get_description(): string {
        global $DB;

        $cohortlist = [];

        $cohortids = $this->get_configdata()['cohorts'];
        $cohorts = $DB->get_records_list('cohort', 'id', $cohortids, 'name');
        foreach ($cohorts as $cohort) {
            $cohortlist[] = format_string($cohort->name, true, ['context' => $cohort->contextid, 'escape' => false]);
        }

        return $this->format_description_for_multiselect($cohortlist);
    }

    /**
     * If the current user is able to add this audience.
     *
     * @return bool
     */
    public function user_can_add(): bool {
        // Check system context first.
        if (has_capability('moodle/cohort:view', context_system::instance())) {
            return true;
        }
        // If there is at least one category with given permissions, user can add.
        return !empty(core_course_category::make_categories_list('moodle/cohort:view'));
    }

    /**
     * Returns if this audience type is available for the user
     *
     * Check if there are available cohorts in the system for this user to use.
     *
     * @return bool
     */
    public function is_available(): bool {
        return !empty(self::get_cohorts());
    }

    /**
     * If the current user is able to edit this audience.
     *
     * @return bool
     */
    public function user_can_edit(): bool {
        global $DB;

        $canedit = true;
        $cohortids = $this->get_configdata()['cohorts'];
        $cohorts = $DB->get_records_list('cohort', 'id', $cohortids);
        foreach ($cohorts as $cohort) {
            $context = context::instance_by_id($cohort->contextid, MUST_EXIST);
            $canedit = $canedit && has_capability('moodle/cohort:view', $context);
            if ($canedit === false) {
                break;
            }
        }

        return $canedit;
    }

    /**
     * Cohorts selector.
     *
     * @return array
     */
    private static function get_cohorts(): array {
        global $CFG;

        require_once($CFG->dirroot.'/cohort/lib.php');

        $cohortslist = [];

        // Search cohorts user can view.
        $usercohorts = cohort_get_all_cohorts(0, 0);

        // The previous method doesn't check cohorts on system context.
        $syscontext = context_system::instance();
        $cohorts = array_filter($usercohorts['cohorts'], static function(stdClass $cohort) use ($syscontext): bool {
            return ($cohort->contextid != $syscontext->id) || has_capability('moodle/cohort:view', $syscontext);
        });

        foreach ($cohorts as $cohort) {
            $cohortslist[$cohort->id] = format_string($cohort->name, true, [
                'context' => $cohort->contextid,
                'escape' => false,
            ]);
        }

        return $cohortslist;
    }
}
