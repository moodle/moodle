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

namespace qbank_columnsortorder\output;

use core_reportbuilder\local\models\column;
use qbank_columnsortorder\column_manager;
use moodle_url;
use renderer_base;

/**
 * Renderable for the "add column" dropdown list
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_column implements \renderable, \templatable {
    /** @var column_manager The column manager for getting the list of hidden columns. */
    protected column_manager $columnmanager;

    /** @var moodle_url The current page URL to redirect back to. */
    protected moodle_url $returnurl;

    /** @var bool True if we are changing global config, false for user preferences. */
    protected bool $global;

    /**
     * Store arguments for generating template context.
     *
     * @param column_manager $columnmanager
     * @param moodle_url $returnurl
     * @param bool $global
     */
    public function __construct(column_manager $columnmanager, moodle_url $returnurl, bool $global = false) {
        $this->columnmanager = $columnmanager;
        $this->returnurl = $returnurl;
        $this->global = $global;
    }

    public function export_for_template(renderer_base $output): array {
        $hiddencolumns = [];
        foreach ($this->columnmanager->get_hidden_columns() as $class => $name) {
            $addurl = new moodle_url('/question/bank/columnsortorder/actions.php', [
                'action' => 'add',
                'global' => $this->global,
                'column' => $class,
                'sesskey' => sesskey(),
                'returnurl' => $this->returnurl,
            ]);
            $hiddencolumns[] = [
                'name' => $name,
                'addurl' => $addurl->out(false),
                'column' => $class,
                'addtext' => get_string('addcolumn', 'qbank_columnsortorder', $name),
            ];
        }
        return [
            'hashiddencolumns' => !empty($hiddencolumns),
            'hiddencolumns' => $hiddencolumns,
        ];
    }
}
