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
                'include-activity-selector d-inline-block me-3' );
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
                'activity-order-selector include-activity-selector d-inline-block');
    }

    /**
     * Render groups single select box.
     *
     * @param \moodle_url $url The base url.
     * @param \stdClass $course Current course.
     * @param int $activegroup Currently active group, defaults to 0. Has no effect if course is in separate groups mode.
     * @return string HTML
     */
    public function render_groups_select(\moodle_url $url, \stdClass $course, int $activegroup = 0): string {
        global $USER;
        $groupurl = fullclone($url);
        $groupurl->remove_params(['page', 'group']);
        $groupoutput = '';
        if ($course->groupmode == SEPARATEGROUPS) {
            $groupoutput = groups_print_course_menu($course, $groupurl, true);
        } else {
            if (has_capability('moodle/site:accessallgroups', \context_course::instance($course->id))) {
                $groups = groups_get_all_groups($course->id);
            } else {
                $groups = groups_get_all_groups($course->id, $USER->id);
            }

            if (count($groups) == 0) {
                return '';
            }
            $groupsmenu = [get_string('allparticipants')] + groups_list_to_menu($groups);
            $select = new single_select($groupurl, 'group', $groupsmenu, $activegroup, null, 'selectgroup');
            $select->label = get_string('groups');
            $groupoutput = $this->output->render($select);
        }

        if (empty($groupoutput)) {
            return $groupoutput;
        }

        return \html_writer::div($groupoutput, 'd-inline-block me-3');
    }

    /**
     * Render activity section single select box.
     *
     * @param \moodle_url $url The base url.
     * @param string $activitysection The current selected section.
     * @param array $sections An array containing all sections of the course
     * @return string HTML
     * @throws \coding_exception
     */
    public function render_activity_section_select(\moodle_url $url, string $activitysection, array $sections): string {
        $activitysectionurl = fullclone($url);
        $activitysectionurl->remove_params(['activitysection']);
        $options = $sections;
        $options[-1] = get_string('no_filter_by_section', 'report_progress');
        $sorttable = new single_select(
            $activitysectionurl, 'activitysection',
            $options, $activitysection, null, 'activity-section-select-report'
        );
        $sorttable->set_label(get_string('activitysection', 'report_progress'));
        return \html_writer::div($this->output->render($sorttable),
                'activity-section-selector include-activity-selector d-inline-block ms-3');
    }

    /**
     * Render download buttons.
     *
     * @param \moodle_url $url The base url.
     * @return string HTML
     * @throws \coding_exception
     * @deprecated since 5.1 MDL-83838 -  Please do not use this function any more.
     * #[\core\attribute\deprecated(null, reason: 'It is no longer used', since: '5.1', mdl: 'MDL-83838')]
     */
    public function render_download_buttons(\moodle_url $url): string {
        \core\deprecation::emit_deprecation_if_present([$this, __FUNCTION__]);
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
