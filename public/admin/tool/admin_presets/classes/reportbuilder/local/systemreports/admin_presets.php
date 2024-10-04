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

namespace tool_admin_presets\reportbuilder\local\systemreports;

use tool_admin_presets\reportbuilder\local\entities\admin_preset;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\action;
use core_reportbuilder\system_report;

/**
 * Admin presets system report class implementation
 *
 * @package    tool_admin_presets
 * @copyright  2024 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_presets extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $apentity = new admin_preset();
        $entityalias = $apentity->get_table_alias('adminpresets');

        $this->set_main_table('adminpresets', $entityalias);
        $this->add_entity($apentity);

        $apappalias = database::generate_alias();

        // We need to join the adminpresets_app table to check if the preset rollback has ben used.
        $this->add_join("LEFT JOIN {adminpresets_app} {$apappalias} ON {$entityalias}.id = {$apappalias}.adminpresetid");

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entityalias}.id, {$entityalias}.name, {$entityalias}.iscore, {$apappalias}.id as appid");

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
        return has_capability('moodle/site:config', \context_system::instance());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier. If custom columns are needed just for this report, they can be defined here.
     */
    public function add_columns(): void {
        $columns = [
            'admin_preset:name',
            'admin_preset:description',
        ];
        $this->add_columns_from_entities($columns);
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'admin_preset:name',
        ];
        $this->add_filters_from_entities($filters);
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {

        // Review settings and apply.
        $this->add_action((new action(
            new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'load', 'id' => ':id']),
            new \pix_icon('t/play', '', 'core'),
            [],
            false,
            new \lang_string('applyaction', 'tool_admin_presets')
        )));

        // Download.
        $this->add_action((new action(
            new \moodle_url('/admin/tool/admin_presets/index.php',
                ['action' => 'export', 'mode' => 'download_xml', 'sesskey' => sesskey(), 'id' => ':id']),
            new \pix_icon('t/download', '', 'core'),
            [],
            false,
            new \lang_string('download', 'core')
        )));

        // Delete button won't be displayed for the pre-installed core "Starter" and "Full" presets.
        $this->add_action((new action(
            new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'delete', 'id' => ':id']),
            new \pix_icon('i/delete', '', 'core'),
            [
                'data-action' => 'admin-preset-delete',
                'data-preset-name' => ':name',
                'data-preset-id' => ':id',
                'data-preset-rollback' => ':appid',
            ],
            false,
            new \lang_string('delete', 'core')
        ))->add_callback(function(\stdClass $row): bool {
            return (int)$row->iscore === \core_adminpresets\manager::NONCORE_PRESET;
        }));

        // Look for preset applications.
        $this->add_action((new action(
            new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'rollback', 'id' => ':id']),
            new \pix_icon('i/reload', '', 'core'),
            [],
            false,
            new \lang_string('showhistory', 'tool_admin_presets')
        ))->add_callback(function(\stdClass $row): bool {
            return (bool) $row->appid;
        }));
    }
}
