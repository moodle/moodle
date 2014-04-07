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
 * Log live report ajax renderer.
 *
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Log live report ajax renderer.
 *
 * @since      Moodle 2.7
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_loglive_renderer_ajax extends plugin_renderer_base {

    /**
     * Render logs for ajax.
     *
     * @param report_loglive_renderable $reportloglive object of report_loglive_renderable.
     *
     * @return string html to be displayed to user.
     */
    public function render_report_loglive_renderable(report_loglive_renderable $reportloglive) {
        if (empty($reportloglive->selectedlogreader)) {
            return null;
        }
        $table = $reportloglive->get_table(true);
        return $table->out($reportloglive->perpage, false);
    }
}
