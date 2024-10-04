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
 * Infected file report renderer
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_infectedfiles\output;
use report_infectedfiles\table\infectedfiles_table;

defined('MOODLE_INTERNAL') || die();

/**
 * Infected file report renderer
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render the table
     *
     * @param infectedfiles_table $table table of infected files
     * @return false|string return html code of the table
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function render_infectedfiles_table(infectedfiles_table $table) {
        ob_start();
        $table->display($table->pagesize, false);
        $o = ob_get_contents();
        ob_end_clean();
        return $o;
    }
}
