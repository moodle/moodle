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

namespace core_reportbuilder\output;

use plugin_renderer_base;
use core_reportbuilder\table\system_report_table;

/**
 * Report renderer class
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render a system report
     *
     * @param system_report $report
     * @return string
     */
    protected function render_system_report(system_report $report): string {
        $context = $report->export_for_template($this);

        return $this->render_from_template('core_reportbuilder/system_report', $context);
    }

    /**
     * Render a system report table
     *
     * @param system_report_table $table
     * @return string
     */
    protected function render_system_report_table(system_report_table $table): string {
        ob_start();
        $table->out($table->get_default_per_page(), false);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
