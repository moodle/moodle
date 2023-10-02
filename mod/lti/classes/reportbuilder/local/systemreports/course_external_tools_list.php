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

namespace mod_lti\reportbuilder\local\systemreports;

use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\column;
use mod_lti\reportbuilder\local\entities\tool_types;
use core_reportbuilder\system_report;

/**
 * Course external tools list system report class implementation.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_external_tools_list extends system_report {

    /** @var \stdClass the course to constrain the report to. */
    protected \stdClass $course;

    /** @var int the usage count for the tool represented in a row, and set by row_callback(). */
    protected int $perrowtoolusage = 0;

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $this->course = get_course($this->get_context()->instanceid);

        // Our main entity, it contains all the column definitions that we need.
        $entitymain = new tool_types();
        $entitymainalias = $entitymain->get_table_alias('lti_types');

        $this->set_main_table('lti_types', $entitymainalias);
        $this->add_entity($entitymain);

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns($entitymain);
        $this->add_filters();
        $this->add_actions();

        // We need id and course in the actions column, without entity prefixes, so add these here.
        // We also need access to the tool usage count in a few places (the usage column as well as the actions column).
        $ti = database::generate_param_name(); // Tool instance param.
        $this->add_base_fields("{$entitymainalias}.id, {$entitymainalias}.course, ".
            "(SELECT COUNT($ti.id)
                FROM {lti} $ti
                WHERE $ti.typeid = {$entitymainalias}.id) AS toolusage");

        // Join the types_categories table, to include only tools available to the current course's category.
        $cattablealias = database::generate_alias();
        $joinsql = "LEFT JOIN {lti_types_categories} {$cattablealias}
                           ON ({$cattablealias}.typeid = {$entitymainalias}.id)";
        $this->add_join($joinsql);

        // Scope the report to the course context and include only those tools available to the category.
        $paramprefix = database::generate_param_name();
        $coursevisibleparam = database::generate_param_name();
        $categoryparam = database::generate_param_name();
        $toolstateparam = database::generate_param_name();
        [$insql, $params] = $DB->get_in_or_equal([get_site()->id, $this->course->id], SQL_PARAMS_NAMED, "{$paramprefix}_");
        $wheresql = "{$entitymainalias}.course {$insql} ".
            "AND {$entitymainalias}.coursevisible NOT IN (:{$coursevisibleparam}) ".
            "AND ({$cattablealias}.id IS NULL OR {$cattablealias}.categoryid = :{$categoryparam}) ".
            "AND {$entitymainalias}.state = :{$toolstateparam}";
        $params = array_merge(
            $params,
            [
                $coursevisibleparam => LTI_COURSEVISIBLE_NO,
                $categoryparam => $this->course->category,
                $toolstateparam => LTI_TOOL_STATE_CONFIGURED
            ]
        );
        $this->add_base_condition_sql($wheresql, $params);

        $this->set_downloadable(false, get_string('pluginname', 'mod_lti'));
        $this->set_default_per_page(10);
        $this->set_default_no_results_notice(null);
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('mod/lti:addpreconfiguredinstance', $this->get_context());
    }

    public function row_callback(\stdClass $row): void {
        $this->perrowtoolusage = $row->toolusage;
    }

    /**
     * Adds the columns we want to display in the report.
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     * @param tool_types $tooltypesentity
     * @return void
     */
    protected function add_columns(tool_types $tooltypesentity): void {
        $entitymainalias = $tooltypesentity->get_table_alias('lti_types');

        $columns = [
            'tool_types:name',
            'tool_types:description',
        ];

        $this->add_columns_from_entities($columns);

        // Tool usage column using a custom SQL subquery (defined in initialise method) to count tool instances within the course.
        // TODO: This should be replaced with proper column aggregation once that's added to system_report instances in MDL-76392.
        $this->add_column(new column(
            'usage',
            new \lang_string('usage', 'mod_lti'),
            $tooltypesentity->get_entity_name()
        ))
            ->set_type(column::TYPE_INTEGER)
            ->set_is_sortable(true)
            ->add_field("{$entitymainalias}.id")
            ->add_callback(fn() => $this->perrowtoolusage);

        // Enable toggle column.
        $this->add_column((new column(
            'showinactivitychooser',
            new \lang_string('showinactivitychooser', 'mod_lti'),
            $tooltypesentity->get_entity_name()
        ))
            // Site tools can be overridden on course level.
            ->add_join("LEFT JOIN {lti_coursevisible} lc ON lc.typeid = {$entitymainalias}.id AND lc.courseid = " . $this->course->id)
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$entitymainalias}.id, {$entitymainalias}.coursevisible, lc.coursevisible as coursevisibleoverridden")
            ->set_is_sortable(false)
            ->set_callback(function(int $id, \stdClass $row): string {
                global $PAGE;
                $coursevisible = $row->coursevisible;
                $courseid = $this->course->id;
                if (!empty($row->coursevisibleoverridden)) {
                    $coursevisible = $row->coursevisibleoverridden;
                }

                if ($coursevisible == LTI_COURSEVISIBLE_ACTIVITYCHOOSER) {
                    $coursevisible = true;
                } else {
                    $coursevisible = false;
                }

                $renderer = $PAGE->get_renderer('core_reportbuilder');
                $attributes = [
                    ['name' => 'id', 'value' => $row->id],
                    ['name' => 'courseid', 'value' => $courseid],
                    ['name' => 'action', 'value' => 'showinactivitychooser-toggle'],
                    ['name' => 'state', 'value' => $coursevisible],
                ];
                $label = $coursevisible ? get_string('dontshowinactivitychooser', 'mod_lti')
                    : get_string('showinactivitychooser', 'mod_lti');

                $disabled = !has_capability('mod/lti:addcoursetool', \context_course::instance($courseid));

                return $renderer->render_from_template('core/toggle', [
                    'id' => 'showinactivitychooser-toggle-' . $row->id,
                    'checked' => $coursevisible,
                    'disabled' => $disabled,
                    'dataattributes' => $attributes,
                    'label' => $label,
                    'labelclasses' => 'sr-only'
                ]);
            })
        );

        // Attempt to create a dummy actions column, working around the limitations of the official actions feature.
        $this->add_column(new column(
            'actions', new \lang_string('actions'),
            $tooltypesentity->get_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(false)
            ->add_fields("{$entitymainalias}.id, {$entitymainalias}.course, {$entitymainalias}.name")
            ->add_callback(function($field, $row) {
                global $OUTPUT;

                // Lock actions for site-level preconfigured tools.
                if (get_site()->id == $row->course) {
                    return \html_writer::div(
                        \html_writer::div(
                            $OUTPUT->pix_icon('t/locked', get_string('courseexternaltoolsnoeditpermissions', 'mod_lti')
                        ), 'tool-action-icon-container'), 'd-flex justify-content-end'
                    );
                }

                // Lock actions when the user can't add course tools.
                if (!has_capability('mod/lti:addcoursetool', \context_course::instance($row->course))) {
                    return \html_writer::div(
                        \html_writer::div(
                            $OUTPUT->pix_icon('t/locked', get_string('courseexternaltoolsnoeditpermissions', 'mod_lti')
                        ), 'tool-action-icon-container'), 'd-flex justify-content-end'
                    );
                }

                // Build and display an action menu.
                $menu = new \action_menu();
                $menu->set_menu_trigger($OUTPUT->pix_icon('i/moremenu', get_string('actions', 'core')),
                    'btn btn-icon d-flex align-items-center justify-content-center'); // TODO check 'actions' lang string with UX.

                $menu->add(new \action_menu_link(
                    new \moodle_url('/mod/lti/coursetooledit.php', ['course' => $row->course, 'typeid' => $row->id]),
                    null,
                    get_string('edit', 'core'),
                    null
                ));

                $menu->add(new \action_menu_link(
                    new \moodle_url('#'),
                    null,
                    get_string('delete', 'core'),
                    null,
                    [
                        'data-action' => 'course-tool-delete',
                        'data-course-tool-id' => $row->id,
                        'data-course-tool-name' => $row->name,
                        'data-course-tool-usage' => $this->perrowtoolusage
                    ],
                ));

                return $OUTPUT->render($menu);
            });

        // Default sorting.
        $this->set_initial_sort_column('tool_types:name', SORT_ASC);
    }

    /**
     * Add any actions for this report.
     *
     * @return void
     */
    protected function add_actions(): void {
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {

        $this->add_filters_from_entities([]);
    }
}
