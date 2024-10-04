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

namespace core_admin\table;

use html_writer;
use moodle_url;
use stdClass;

/**
 * Tiny admin settings.
 *
 * @package core_admin
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_management_table extends \core_admin\table\plugin_management_table {

    /** @var plugininfo[] A list of blocks which cannot be deleted */
    protected array $undeletableblocktypes;

    /** @var stdClass[] A list of basic block data */
    protected array $blockdata;

    /** @var array<string,int> A list of course counts */
    protected array $courseblocks;

    public function __construct() {
        global $DB;
        parent::__construct();
        $this->undeletableblocktypes = \block_manager::get_undeletable_block_types();

        $sql = 'SELECT b.name,
                       b.id,
                       COUNT(DISTINCT binst.id) as totalcount
                  FROM {block} b
             LEFT JOIN {block_instances} binst ON binst.blockname = b.name
              GROUP BY b.id,
                       b.name
              ORDER BY b.name ASC';
        $this->blockdata = $DB->get_records_sql($sql);

        $sql = "SELECT blockname
                  FROM {block_instances}
                 WHERE pagetypepattern = 'course-view-*'
              GROUP BY blockname";
        $this->courseblocks = $DB->get_records_sql($sql);
    }

    protected function get_plugintype(): string {
        return 'block';
    }

    public function guess_base_url(): void {
        $this->define_baseurl(
            new moodle_url('/admin/blocks.php')
        );
    }

    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/blocks.php', $params);
    }


    protected function get_table_js_module(): string {
        return 'core_admin/block_management_table';
    }

    protected function get_column_list(): array {
        $columns = parent::get_column_list();
        return array_merge(
            array_slice($columns, 0, 1, true),
            ['instances' => get_string('blockinstances', 'admin')],
            array_slice($columns, 1, 2, true),
            ['protect' => get_string('blockprotect', 'admin')],
            array_slice($columns, 3, null, true),
        );
    }

    protected function get_columns_with_help(): array {
        return [
            'protect' => new \help_icon('blockprotect', 'admin'),
        ];
    }

    /**
     * Render the instances column
     * @param stdClass $row
     * @return string
     */
    protected function col_instances(stdClass $row): string {
        $blockdata = $this->blockdata[$row->plugininfo->name];
        if (array_key_exists($blockdata->name, $this->courseblocks)) {
            return html_writer::link(
                new moodle_url('/course/search.php', [
                    'blocklist' => $blockdata->id,
                ]),
                $blockdata->totalcount,
            );
        }

        return $blockdata->totalcount;
    }

    /**
     * Render the protect column.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_protect(stdClass $row): string {
        global $OUTPUT;

        $params = [
            'sesskey' => sesskey(),
        ];

        $protected = in_array($row->plugininfo->name, $this->undeletableblocktypes);

        $pluginname = $row->plugininfo->displayname;
        if ($protected) {
            $params['unprotect'] = $row->plugininfo->name;
            $icon = $OUTPUT->pix_icon('t/unlock', get_string('blockunprotectblock', 'admin', $pluginname));
        } else {
            $params['protect'] = $row->plugininfo->name;
            $icon = $OUTPUT->pix_icon('t/lock', get_string('blockprotectblock', 'admin', $pluginname));
        }

        return html_writer::link(
            $this->get_action_url($params),
            $icon,
            [
                'data-action' => 'toggleprotectstate',
                'data-plugin' => $row->plugin,
                'data-target-state' => $protected ? 0 : 1,
            ],
        );
        return '';
    }
}
