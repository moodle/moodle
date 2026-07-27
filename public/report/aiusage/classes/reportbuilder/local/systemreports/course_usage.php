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

namespace report_aiusage\reportbuilder\local\systemreports;

use core_reportbuilder\system_report;
use core_reportbuilder\local\helpers\database;
use core_ai\reportbuilder\local\entities\ai_action_register;
use core\reportbuilder\local\entities\context;
use core_reportbuilder\local\entities\user;

/**
 * Course-level AI usage system report.
 *
 * Reuses the core_ai reportbuilder entities used by the sitewide admin report
 * ({@see \core_ai\reportbuilder\local\systemreports\usage}), restricted to a single course, and
 * further restricted to a single user's own actions when the caller lacks the "view all" capability.
 *
 * @package    report_aiusage
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_usage extends system_report {
    #[\Override]
    protected function initialise(): void {
        $entitymain = new ai_action_register();
        $entitymainalias = $entitymain->get_table_alias('ai_action_register');

        $this->set_main_table('ai_action_register', $entitymainalias);
        $this->add_entity($entitymain);

        // Restrict to the course this report was created for.
        $this->add_base_condition_simple(
            "{$entitymainalias}.courseid",
            $this->get_parameter('courseid', 0, PARAM_INT),
        );

        // Restrict to a single user's own actions when the caller cannot view the whole course.
        $restricttouserid = $this->get_parameter('restricttouserid', 0, PARAM_INT);
        if ($restricttouserid > 0) {
            $this->add_base_condition_simple("{$entitymainalias}.userid", $restricttouserid);
        }

        $this->add_group_restriction($entitymainalias, $restricttouserid);

        // Join the 'user' entity to our main entity.
        $entityuser = new user();
        $entituseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser->add_join(
            "LEFT JOIN {user} {$entituseralias} ON {$entituseralias}.id = {$entitymainalias}.userid",
        ));

        // Join the 'context' entity to our main entity.
        $entitycontext = new context();
        $entitycontextalias = $entitycontext->get_table_alias('context');
        $this->add_entity($entitycontext->add_join(
            "LEFT JOIN {context} {$entitycontextalias} ON {$entitycontextalias}.id = {$entitymainalias}.contextid",
        ));

        $this->add_columns();
        $this->add_filters();

        $this->set_downloadable(true, get_string('pluginname', 'report_aiusage'));
    }

    /**
     * Honour separate groups mode: a viewer of the whole course who cannot access all groups only
     * sees actions from members of their own groups.
     *
     * @param string $entitymainalias The ai_action_register table alias.
     * @param int $restricttouserid The "own actions only" restriction already applied, 0 for none.
     */
    private function add_group_restriction(string $entitymainalias, int $restricttouserid): void {
        global $DB, $USER;

        $courseid = $this->get_parameter('courseid', 0, PARAM_INT);
        if ($restricttouserid > 0 || $courseid <= 0) {
            // A viewer already restricted to their own actions needs no group restriction.
            return;
        }

        $course = get_course($courseid);
        if (
            groups_get_course_groupmode($course) != SEPARATEGROUPS
            || has_capability('moodle/site:accessallgroups', \context_course::instance($course->id))
        ) {
            return;
        }

        $groupids = array_keys(groups_get_all_groups($course->id, $USER->id));
        if ($groupids) {
            [$groupselect, $groupparams] = $DB->get_in_or_equal(
                $groupids,
                SQL_PARAMS_NAMED,
                database::generate_param_name(),
            );
            $this->add_base_condition_sql(
                "{$entitymainalias}.userid IN (
                    SELECT gm.userid
                      FROM {groups_members} gm
                     WHERE gm.groupid {$groupselect})",
                $groupparams,
            );
        } else {
            // The viewer is in no group, so no rows are visible to them.
            $this->add_base_condition_sql('1 = 2');
        }
    }

    #[\Override]
    protected function can_view(): bool {
        global $USER;

        // The courseid and restricttouserid parameters round-trip through the client on every AJAX
        // request, so the access check must be derived from them rather than from the stored report
        // context alone: "view all" is checked in the requested course's own context, and "view own"
        // additionally requires the report to be restricted to the viewer's own actions.
        $courseid = $this->get_parameter('courseid', 0, PARAM_INT);
        if ($courseid <= 0) {
            return false;
        }

        $context = \context_course::instance($courseid, IGNORE_MISSING);
        if (!$context) {
            return false;
        }

        if (has_capability('report/aiusage:view', $context)) {
            return true;
        }

        return has_capability('report/aiusage:viewown', $context)
            && $this->get_parameter('restricttouserid', 0, PARAM_INT) === (int) $USER->id;
    }

    #[\Override]
    public static function get_name(): string {
        return get_string('pluginname', 'report_aiusage');
    }

    #[\Override]
    public function get_exclude_columns_for_download(): array {
        // The detail column is a link to the detail page, which is meaningless in a download.
        return ['ai_action_register:detail'];
    }

    /**
     * Adds the columns we want to display in the report.
     */
    public function add_columns(): void {
        $this->add_columns_from_entities([
            'ai_action_register:provider',
            'ai_action_register:actionname',
            'ai_action_register:timecreated',
            'ai_action_register:prompttokens',
            'ai_action_register:completiontokens',
            'ai_action_register:success',
        ]);

        // Link the context column to the closest context (e.g. the course activity the action relates to)
        // rather than just showing its name as plain text.
        $this->add_column_from_entity('context:link')
            ->set_title(new \lang_string('contextname'));

        $this->add_column_from_entity('user:fullnamewithlink');

        // Link to the full detail of the action (full prompt, full generated response, etc), shown last.
        $this->add_column_from_entity('ai_action_register:detail');

        $this->set_initial_sort_column('ai_action_register:timecreated', SORT_DESC);
    }

    /**
     * Adds the filters we want to display in the report.
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'ai_action_register:actionname',
            'ai_action_register:provider',
            'ai_action_register:timecreated',
            'ai_action_register:prompttokens',
            'ai_action_register:completiontokens',
            'ai_action_register:success',
            'context:level',
            'user:fullname',
        ]);
    }
}
