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

use core_question\local\bank\column_base;
use qbank_columnsortorder\local\bank\column_action_remove;
use moodle_url;
use qbank_columnsortorder\column_manager;
use renderable;
use templatable;

/**
 * Renderable for the column sort admin UI.
 *
 * Displays a list of the currently enabled columns and allows them to be sorted, hidden, and resized.
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_sort_ui implements renderable, templatable {
    /**
     * The minimum custom width for a column.
     *
     * This is based on the minimum possible width of the smallest core column (question type).
     * When viewed, the width will be resized to the minimum width of the column header, if too small.
     *
     * @var int
     */
    const MIN_COLUMN_WIDTH = 30;

    public function export_for_template(\renderer_base $output): array {
        $columnmanager = new column_manager(true);
        $enabledcolumns = $columnmanager->get_columns();
        $disabledcolumns = $columnmanager->get_disabled_columns();
        $columnsizes = $columnmanager->get_colsize_map();
        $qbank = $columnmanager->get_questionbank();
        $returnurl = new moodle_url('/question/bank/columnsortorder/sortcolumns.php');
        $params = [];
        $params['formaction'] = new moodle_url('/question/bank/columnsortorder/actions.php');
        $params['sesskey'] = sesskey();
        $params['disabled'] = $disabledcolumns;
        $params['contextid'] = \context_system::instance()->id;
        $params['minwidth'] = self::MIN_COLUMN_WIDTH;
        foreach ($enabledcolumns as $column) {
            if (in_array($column->id, $columnmanager->hiddencolumns) || array_key_exists($column->id, $disabledcolumns)) {
                continue;
            }
            $name = $column->name;
            $colname = get_string('qbankcolumnname', 'qbank_columnsortorder', $column->colname);

            $removeaction = new column_action_remove($qbank);
            $removeaction->set_global(true);
            $actionmenu = new \action_menu([
                $removeaction->get_action_menu_link($column->class::from_column_name($qbank, $column->colname)),
            ]);
            $params['names'][] = [
                'name' => $name,
                'colname' => $colname,
                'class' => $column->class,
                'width' => $columnsizes[$column->id] ?? null,
                'widthlabel' => get_string('width', 'qbank_columnsortorder', $name),
                'actionmenu' => $actionmenu->export_for_template($output),
                'columnid' => $column->id,
                'escapedid' => str_replace('\\', '__', $column->id),
            ];
        }

        $params['disabled'] = array_values($disabledcolumns);
        $params['columnsdisabled'] = !empty($params['disabled']);
        $addcolumn = new add_column($columnmanager, $returnurl);
        $params['addcolumn'] = $addcolumn->export_for_template($output);
        $resetcolums = new reset_columns($returnurl);
        $params['resetcolumns'] = $resetcolums->export_for_template($output);
        $params['extraclasses'] = 'pe-1';
        $urltoredirect = new moodle_url('/admin/settings.php', ['section' => 'manageqbanks']);

        $params['urltomanageqbanks'] = get_string('qbankgotomanageqbanks', 'qbank_columnsortorder', $urltoredirect->out());
        $params['previewurl'] = new moodle_url('/question/bank/columnsortorder/sortcolumns.php', [
            'preview' => true,
        ]);
        return $params;
    }
}
