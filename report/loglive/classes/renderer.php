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
     * Render log report page.
     *
     * @param report_loglive_renderable $reportloglive object of report_log.
     */
    public function render_report_loglive_renderable(report_loglive_renderable $reportloglive) {
        if (empty($reportloglive->selectedlogreader)) {
            echo $this->output->notification(get_string('nologreaderenabled', 'report_loglive'), 'notifyproblem');
            return;
        }

        $reportloglive->setup_table();
        $reportloglive->tablelog->out($reportloglive->perpage, true);
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
            return;
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
        return null;
    }
}

