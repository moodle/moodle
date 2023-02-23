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

use moodle_url;
use plugin_renderer_base;
use qbank_columnsortorder\column_manager;

/**
 * Class renderer.
 * @package    qbank_columnsortorder
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Render list of question bank plugin columns.
     *
     * @return string The rendered HTML.
     */
    public function render_column_sort_ui() {
        $columnsortorder = new column_manager();
        $enabledcolumns = $columnsortorder->get_columns();
        $disabledcolumns = $columnsortorder->get_disabled_columns();
        $params = [];
        foreach ($enabledcolumns as $columnname) {
            $name = $columnname->name;
            $colname = get_string('qbankcolumnname', 'qbank_columnsortorder', $columnname->colname);
            if ($columnname->class === 'qbank_customfields\custom_field_column') {
                $columnname->class .= "\\$columnname->colname";
            }
            $params['names'][] = ['name' => $name, 'colname' => $colname, 'class' => $columnname->class];
        }
        $params['disabled'] = $disabledcolumns;
        $params['columnsdisabled'] = (!empty($params['disabled'])) ? true : false;
        $urltoredirect = new moodle_url('/admin/settings.php', ['section' => 'manageqbanks']);

        $params['urltomanageqbanks'] = get_string('qbankgotomanageqbanks', 'qbank_columnsortorder', $urltoredirect->out());

        return $this->render_from_template('qbank_columnsortorder/columnsortorder', $params);
    }
}
