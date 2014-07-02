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
 * Loglive report renderer.
 *
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Report log renderer's for printing reports.
 *
 * @since      Moodle 2.7
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_loglive_renderer extends plugin_renderer_base {

    /**
     * This method should never be manually called, it should only be called by process.
     * Please call the render method instead.
     *
     * @deprecated since 2.8, to be removed in 2.9
     * @param report_loglive_renderable $reportloglive
     * @return string
     */
    public function render_report_loglive_renderable(report_loglive_renderable $reportloglive) {
        debugging('Do not call this method. Please call $renderer->render($reportloglive) instead.', DEBUG_DEVELOPER);
        return $this->render($reportloglive);
    }

    /**
     * Return html to render the loglive page..
     *
     * @param report_loglive_renderable $reportloglive object of report_log.
     *
     * @return string html used to render the page;
     */
    protected function render_report_loglive(report_loglive_renderable $reportloglive) {
        if (empty($reportloglive->selectedlogreader)) {
            return $this->output->notification(get_string('nologreaderenabled', 'report_loglive'), 'notifyproblem');
        }

        $table = $reportloglive->get_table();
        return $this->render_table($table, $reportloglive->perpage);
    }

    /**
     * Prints/return reader selector
     *
     * @param report_loglive_renderable $reportloglive log report.
     *
     * @return string Returns rendered widget
     */
    public function reader_selector(report_loglive_renderable $reportloglive) {
        $readers = $reportloglive->get_readers(true);
        if (count($readers) <= 1) {
            // One or no readers found, no need of this drop down.
            return '';
        }
        $select = new single_select($reportloglive->url, 'logreader', $readers, $reportloglive->selectedlogreader, null);
        $select->set_label(get_string('selectlogreader', 'report_loglive'));
        return $this->output->render($select);
    }

    /**
     * Prints a button to update/resume live updates.
     *
     * @param report_loglive_renderable $reportloglive log report.
     *
     * @return string Returns rendered widget
     */
    public function toggle_liveupdate_button(report_loglive_renderable $reportloglive) {
        // Add live log controls.
        if ($reportloglive->page == 0 && $reportloglive->selectedlogreader) {
            echo html_writer::tag('button' , get_string('pause', 'report_loglive'), array('id' => 'livelogs-pause-button'));
            $icon = new pix_icon('i/loading_small', 'loading', 'moodle', array('class' => 'spinner'));
            return $this->output->render($icon);
        }
        return '';
    }

    /**
     * Get the html for the table.
     *
     * @param report_loglive_table_log $table table object.
     * @param int $perpage entries to display perpage.
     *
     * @return string table html
     */
    protected function render_table(report_loglive_table_log $table, $perpage) {
        $o = '';
        ob_start();
        $table->out($perpage, true);
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }
}