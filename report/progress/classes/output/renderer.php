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

namespace report_progress\output;

use single_select;
use plugin_renderer_base;
use html_writer;

/**
 * Renderer for report progress.
 *
 * @package   report_progress
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL Juv3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Render include activity single select box.
     *
     * @param \moodle_url $url The base url.
     * @param array $activitytypes The activity type options.
     * @param string $activityinclude The current selected option.
     * @return string HTML
     * @throws \coding_exception
     */
    public function render_include_activity_select(\moodle_url $url, array $activitytypes,
            string $activityinclude): string {
        $includeurl = fullclone($url);
        $includeurl->remove_params(['page', 'activityinclude']);
        $activityincludeselect = new single_select(
            $url, 'activityinclude',
            $activitytypes, $activityinclude, null, 'include-activity-select-report'
        );
        $activityincludeselect->set_label(get_string('include', 'report_progress'));
        return \html_writer::div($this->output->render($activityincludeselect),
                'include-activity-selector d-inline-block ml-3' );
    }

    /**
     * Render activity order single select box.
     *
     * @param \moodle_url $url The base url.
     * @param string $activityorder The current selected option.
     * @return string HTML
     * @throws \coding_exception
     */
    public function render_activity_order_select(\moodle_url $url, string $activityorder): string {
        $activityorderurl = fullclone($url);
        $activityorderurl->remove_params(['activityorder']);
        $options = ['orderincourse' => get_string('orderincourse', 'report_progress'),
            'alphabetical' => get_string('alphabetical', 'report_progress')];
        $sorttable = new single_select(
            $activityorderurl, 'activityorder',
            $options, $activityorder, null, 'activity-order-select-report'
        );
        $sorttable->set_label(get_string('activityorder', 'report_progress'));
        return \html_writer::div($this->output->render($sorttable),
                'activity-order-selector include-activity-selector d-inline-block ml-3');
    }

    /**
     * Render download buttons.
     *
     * @param \moodle_url $url The base url.
     * @return string HTML
     * @throws \coding_exception
     */
    public function render_download_buttons(\moodle_url $url): string {
        $downloadurl = fullclone($url);
        $downloadurl->remove_params(['page']);
        $downloadurl->param('format', 'csv');
        $downloadhtml = html_writer::start_tag('ul', ['class' => 'progress-actions']);
        $downloadhtml .= html_writer::tag('li', html_writer::link($downloadurl, get_string('csvdownload', 'completion')));
        $downloadurl->param('format', 'excelcsv');
        $downloadhtml .= html_writer::tag('li', html_writer::link($downloadurl, get_string('excelcsvdownload', 'completion')));
        $downloadhtml .= html_writer::end_tag('ul');

        return $downloadhtml;
    }
}
