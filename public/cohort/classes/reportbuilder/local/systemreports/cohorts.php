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

namespace core_cohort\reportbuilder\local\systemreports;

use context;
use context_coursecat;
use context_system;
use core_reportbuilder\local\aggregation\count;
use core_cohort\reportbuilder\local\entities\{cohort, cohort_member};
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\action;
use core_reportbuilder\local\report\column;
use html_writer;
use lang_string;
use moodle_url;
use pix_icon;
use core_reportbuilder\system_report;
use stdClass;

/**
 * Cohorts system report class implementation
 *
 * @package    core_cohort
 * @copyright  2021 David Matamoros <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohorts extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $cohortentity = new cohort();
        $entitymainalias = $cohortentity->get_table_alias('cohort');

        $this->set_main_table('cohort', $entitymainalias);
        $this->add_entity($cohortentity);

        // Join cohort member entity.
        $cohortmemberentity = new cohort_member();
        $cohortmemberalias = $cohortmemberentity->get_table_alias('cohort_members');
        $this->add_entity($cohortmemberentity
            ->add_join("LEFT JOIN {cohort_members} {$cohortmemberalias} ON {$cohortmemberalias}.cohortid = {$entitymainalias}.id")
        );

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entitymainalias}.id, {$entitymainalias}.contextid, {$entitymainalias}.visible, " .
            "{$entitymainalias}.component, {$entitymainalias}.name");

        $this->set_checkbox_toggleall(static function(stdClass $cohort): ?array {
            if (!empty($cohort->component) ||
                    !has_capability('moodle/cohort:manage', context::instance_by_id($cohort->contextid))) {
                return null;
            }

            return [
                $cohort->id,
                get_string('selectitem', 'moodle', $cohort->name),
            ];
        });

        // Check if report needs to show a specific category.
        if (!$this->get_context() instanceof context_system || !$this->get_parameter('showall', false, PARAM_BOOL)) {
            $paramcontextid = database::generate_param_name();
            $this->add_base_condition_sql("{$entitymainalias}.contextid = :{$paramcontextid}", [
                $paramcontextid => $this->get_context()->id,
            ]);
        }

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        // Set if report can be downloaded.
        $this->set_downloadable(false);
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_any_capability(['moodle/cohort:manage', 'moodle/cohort:view'], $this->get_context());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier. If custom columns are needed just for this report, they can be defined here.
     */
    protected function add_columns(): void {
        $cohortentity = $this->get_entity('cohort');
        $entitymainalias = $cohortentity->get_table_alias('cohort');

        // Category column. An extra callback is appended in order to extend the current column formatting.
        if ($this->get_context() instanceof context_system && $this->get_parameter('showall', false, PARAM_BOOL)) {
            $this->add_column_from_entity('cohort:context')
                ->add_callback(static function(string $value, stdClass $cohort): string {
                    $context = context::instance_by_id($cohort->ctxid);
                    if ($context instanceof context_coursecat) {
                        return html_writer::link(new moodle_url('/cohort/index.php',
                            ['contextid' => $context->id]), $value);
                    }

                    return $value;
                });
        }

        // Name column using the inplace editable component.
        $this->add_column(new column(
            'editablename',
            new lang_string('name', 'core_cohort'),
            $cohortentity->get_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true)
            ->add_fields("{$entitymainalias}.name, {$entitymainalias}.id, {$entitymainalias}.contextid")
            ->add_callback(static function(string $name, stdClass $cohort): string {
                global $OUTPUT, $PAGE;
                $renderer = $PAGE->get_renderer('core');

                $template = new \core_cohort\output\cohortname($cohort);
                return $renderer->render_from_template('core/inplace_editable', $template->export_for_template($OUTPUT));
            });

        // ID Number column using the inplace editable component.
        $this->add_column(new column(
            'editableidnumber',
            new lang_string('idnumber', 'core_cohort'),
            $cohortentity->get_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            ->set_is_sortable(true)
            ->add_fields("{$entitymainalias}.idnumber, {$entitymainalias}.id, {$entitymainalias}.contextid")
            ->add_callback(static function(?string $idnumber, stdClass $cohort): string {
                global $OUTPUT, $PAGE;
                $renderer = $PAGE->get_renderer('core');

                $template = new \core_cohort\output\cohortidnumber($cohort);
                return $renderer->render_from_template('core/inplace_editable', $template->export_for_template($OUTPUT));
            });

        // Description column.
        $this->add_column_from_entity('cohort:description');

        // Member count.
        $this->add_column_from_entity('cohort_member:timeadded')
            ->set_title(new lang_string('memberscount', 'cohort'))
            ->set_aggregation(count::get_class_name());

        // Component column. Override the display name of a column.
        $this->add_column_from_entity('cohort:component')
            ->set_title(new lang_string('source', 'core_plugin'));

        // It's possible to set a default initial sort direction for one column.
        $this->set_initial_sort_column('cohort:editablename', SORT_ASC);
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $this->add_filters_from_entity('cohort', ['name', 'idnumber', 'description', 'customfield*']);
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {

        $returnurl = (new moodle_url('/cohort/index.php', [
            'id' => ':id',
            'contextid' => $this->get_context()->id,
            'showall' => $this->get_parameter('showall', false, PARAM_BOOL),
        ]))->out(false);

        // Hide action. It will be only shown if the property 'visible' is true and user has 'moodle/cohort:manage' capabillity.
        $this->add_action((new action(
            new moodle_url('/cohort/edit.php', ['id' => ':id', 'sesskey' => sesskey(), 'hide' => 1, 'returnurl' => $returnurl]),
            new pix_icon('t/show', '', 'core'),
            [],
            false,
            new lang_string('hide')
        ))->add_callback(function(stdClass $row): bool {
            return empty($row->component) && $row->visible
                && has_capability('moodle/cohort:manage', context::instance_by_id($row->contextid));
        }));

        // Show action. It will be only shown if the property 'visible' is false and user has 'moodle/cohort:manage' capabillity.
        $this->add_action((new action(
            new moodle_url('/cohort/edit.php', ['id' => ':id', 'sesskey' => sesskey(), 'show' => 1, 'returnurl' => $returnurl]),
            new pix_icon('t/hide', '', 'core'),
            [],
            false,
            new lang_string('show')
        ))->add_callback(function(stdClass $row): bool {
            return empty($row->component) && !$row->visible
                && has_capability('moodle/cohort:manage', context::instance_by_id($row->contextid));
        }));

        // Edit action. It will be only shown if user has 'moodle/cohort:manage' capabillity.
        $this->add_action((new action(
            new moodle_url('/cohort/edit.php', ['id' => ':id', 'returnurl' => $returnurl]),
            new pix_icon('t/edit', '', 'core'),
            [],
            false,
            new lang_string('edit')
        ))->add_callback(function(stdClass $row): bool {
            return empty($row->component) && has_capability('moodle/cohort:manage', context::instance_by_id($row->contextid));
        }));

        // Delete action. It will be only shown if user has 'moodle/cohort:manage' capabillity.
        $this->add_action((new action(
            new moodle_url('#'),
            new pix_icon('t/delete', '', 'core'),
            ['class' => 'text-danger', 'data-action' => 'cohort-delete', 'data-cohort-id' => ':id', 'data-cohort-name' => ':name'],
            false,
            new lang_string('delete')
        ))->add_callback(function(stdClass $row): bool {
            return empty($row->component) && has_capability('moodle/cohort:manage', context::instance_by_id($row->contextid));
        }));

        // Assign members to cohort action. It will be only shown if user has 'moodle/cohort:assign' capabillity.
        $this->add_action((new action(
            new moodle_url('/cohort/assign.php', ['id' => ':id', 'returnurl' => $returnurl]),
            new pix_icon('i/users', '', 'core'),
            [],
            false,
            new lang_string('assign', 'core_cohort')
        ))->add_callback(function(stdClass $row): bool {
            return empty($row->component) && has_capability('moodle/cohort:assign', context::instance_by_id($row->contextid));
        }));
    }

    /**
     * CSS class for the row
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return (!$row->visible) ? 'text-muted' : '';
    }
}
