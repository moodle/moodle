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

/**
 * Plugin renderer class.
 *
 * @package    report_configlog
 * @copyright  2019 Paul Holden (pholden@greenhead.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_configlog\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Plugin renderer class.
 *
 * @package    report_configlog
 * @copyright  2019 Paul Holden (pholden@greenhead.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /** @var int Page size for displaying report table. */
    const REPORT_TABLE_PAGESIZE = 30;

    /**
     * Return output to be rendered to page
     *
     * @param report_table $table
     * @return string HTML rendered table
     */
    protected function render_report_table(report_table $table) {
        ob_start();

        $table->out(self::REPORT_TABLE_PAGESIZE, false);
        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }
}
