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

namespace mod_board\reportbuilder\local\systemreports;

use core_reportbuilder\local\report\action;
use core_reportbuilder\system_report;
use lang_string;
use mod_board\reportbuilder\local\entities\template;
use moodle_url;
use pix_icon;

/**
 * Templates report.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class templates extends system_report {
    /** @var template */
    protected $templateentity;

    #[\Override]
    protected function initialise(): void {
        global $PAGE;

        // Set context properly in AJAX scripts.
        if (defined('AJAX_SCRIPT') && AJAX_SCRIPT) {
            $PAGE->set_context($this->get_context());
        }

        $this->templateentity = new template();
        $this->add_entity($this->templateentity);
        $templatealias = $this->templateentity->get_table_alias('board_templates');
        $this->set_main_table('board_templates', $templatealias);

        $this->add_join($this->templateentity->get_context_join());

        $this->add_base_fields("{$templatealias}.id");

        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        $this->set_downloadable(false);

        $this->set_default_no_results_notice(new lang_string('error_notemplates', 'mod_board'));
    }

    #[\Override]
    protected function can_view(): bool {
        $context = \context_system::instance();
        if ($this->get_context()->id != $context->id) {
            return false;
        }
        return has_capability('mod/board:managetemplates', $context);
    }

    /**
     * Adds the columns we want to display in the report.
     */
    public function add_columns(): void {
        $this->add_column_from_entity('template:name');
        $this->add_column_from_entity('template:description');
        $this->add_column_from_entity('template:context');
        $this->add_column_from_entity('template:columns');
        $this->add_column_from_entity('template:settings');

        $this->set_initial_sort_column('template:name', SORT_ASC);
    }

    /**
     * Adds the filters we want to display in the report.
     */
    protected function add_filters(): void {
        $filters = [
            'template:name',
            'template:context',
        ];
        $this->add_filters_from_entities($filters);
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {
        $link = (new \mod_board\output\ajax_form\modal\link(
            formurl: new moodle_url('/mod/board/template/update_ajax.php', ['id' => ':id']),
            label: new lang_string('template_update', 'mod_board')
        ))
            ->set_icon(new pix_icon('i/settings', ''))
            ->set_form_size('lg');
        $this->add_action($link->create_report_action());

        $this->add_action(new action(
            new moodle_url('/mod/board/template/export.php', ['id' => ':id']),
            new pix_icon('t/download', ''),
            [],
            false,
            new lang_string('template_export', 'mod_board'),
        ));

        $link = (new \mod_board\output\ajax_form\modal\link(
            formurl: new moodle_url('/mod/board/template/delete_ajax.php', ['id' => ':id']),
            label: new lang_string('template_delete', 'mod_board')
        ))
            ->set_icon(new pix_icon('i/delete', ''));
        $this->add_action($link->create_report_action(['class' => 'text-danger']));
    }
}
