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

use tool_brickfield\accessibility;
use tool_brickfield\analysis;
use tool_brickfield\area_base;
use tool_brickfield\local\tool\filter;
use tool_brickfield\manager;
use tool_brickfield\registration;
use tool_brickfield\scheduler;
use tool_brickfield\sitedata;

/**
 * Definition of the accessreview block.
 *
 * @package   block_accessreview
 * @copyright 2019 Karen Holland LTS.ie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_accessreview extends block_base {
    /**
     * Sets the block title.
     */
    public function init(): void {
        $this->title = get_string('errorssummary', 'block_accessreview');
    }

    /**
     * Defines where the block can be added.
     *
     * @return array
     */
    public function applicable_formats(): array {
        return [
            'course-view' => true,
            'site' => true,
            'mod' => false,
            'my' => false,
        ];
    }

    /**
     * Controls global configurability of block.
     *
     * @return bool
     */
    public function has_config(): bool {
        return true;
    }

    /**
     * Controls whether multiple block instances are allowed.
     *
     * @return bool
     */
    public function instance_allow_multiple(): bool {
        return false;
    }

    /**
     * Creates the block's main content
     *
     * @return string|stdClass
     */
    public function get_content() {
        global $COURSE, $OUTPUT;

        // If Brickfield accessibility toolkit has been disabled, do nothing.
        if (!accessibility::is_accessibility_enabled()) {
            return '';
        }

        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';

        // Check to see user can view/use the accessmap.
        $context = context_course::instance($COURSE->id);
        if (!isloggedin() || isguestuser() || !has_capability('block/accessreview:view', $context)) {
            return $this->content;
        }

        // Check for valid registration.
        if (!(new registration())->toolkit_is_active()) {
            $this->content->text = manager::registration_message();
        } else if (scheduler::is_course_analyzed($COURSE->id)) {
            // Build error data table.
            $table = new html_table();
            $table->head = [
                get_string('checktypes', 'block_accessreview'), get_string('errors', 'block_accessreview')
            ];
            $table->align = ['left', 'center'];
            $tabledata = $this->get_table_data($COURSE->id);
            // Handling no data.
            if ($tabledata === null) {
                $this->content->text = get_string('nodata', 'block_accessreview');
                return $this->content;
            }
            $table->data = $tabledata;
            $table->attributes['class'] = 'generaltable table-sm block_accessreview_table';
            $this->content->text .= html_writer::table($table, true);

            // Check for compatible course formats for highlighting.
            $showhighlighting = false;
            switch ($COURSE->format) {
                case accessibility::TOOL_BRICKFIELD_FORMAT_TOPIC:
                case accessibility::TOOL_BRICKFIELD_FORMAT_WEEKLY:
                    $showhighlighting = true;
                    break;
            }

            // Toggle overlay link.
            $toggle = ($showhighlighting) ? $this->get_toggle_link() : '';
            // Report download link.
            $download = $this->get_download_link($context);
            // Report view link.
            $view = $this->get_report_link($context);

            $this->content->text .= html_writer::tag('div', $toggle . $view . $download, [
                    'class' => 'block_accessreview_links'
                ]
            );

            if ($showhighlighting) {
                // Setting up AMD module.
                $whattoshow = get_config('block_accessreview', 'whattoshow');
                $toggled = get_user_preferences('block_accessreviewtogglestate', true);
                $arguments = [$toggled, $whattoshow, $COURSE->id];
                $this->page->requires->js_call_amd('block_accessreview/module', 'init', $arguments);
            }
        } else if (scheduler::is_course_in_schedule($COURSE->id)) {
            // Display a message that the course is awaiting analysis.
            $this->content->text = get_string('schedule:scheduled', manager::PLUGINNAME);
        } else if (!analysis::is_enabled()) {
            $this->content->text = get_string('analysistypedisabled', manager::PLUGINNAME);
        } else {
            // Display a button to request analysis.
            $this->content->text = get_string('schedule:blocknotscheduled', manager::PLUGINNAME, manager::get_helpurl());

            $button = new single_button(
                new moodle_url(accessibility::get_plugin_url(), ['action' => 'requestanalysis', 'courseid' => $COURSE->id]),
                get_string('schedule:requestanalysis', manager::PLUGINNAME), 'post', single_button::BUTTON_PRIMARY,
                ['class' => 'block_accessreview_analysisbutton']);
            $this->content->text .= html_writer::tag('div', $OUTPUT->render($button),
                ['class' => 'block_accessreview_analysisbutton']);
        }

        return $this->content;
    }

    /**
     * This block shouldn't be added to a page if the accessibility tools setting is disabled.
     *
     * @param moodle_page $page
     * @return bool
     */
    public function can_block_be_added(moodle_page $page): bool {
        return accessibility::is_accessibility_enabled();
    }

    /**
     * Fetches and groups the relevent error data for the table to display.
     * @param int $courseid The ID of the course.
     * @return array The data required by the table.
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function get_table_data($courseid): array {
        global $OUTPUT;
        $datafilters = new filter($courseid, 0);
        $errordisplay = get_config('block_accessreview', 'errordisplay');
        $summarydata = (new sitedata())->get_checkgroup_data($datafilters);
        $data = [];
        $count = 0;
        for ($i = 1; $count < $summarydata[0]->groupcount; $i++) {
            if (isset($summarydata[0]->{'componentlabel' . $i})) {
                $data[$i] = $summarydata[0]->{'errorsvalue' . $i};
                $count++;
            }
        }
        $files = [
            'form' => '',
            'image' => '231/',
            'layout' => '234/',
            'link' => '237/',
            'media' => '240/',
            'table' => '243/',
            'text' => '246/',
        ];
        // Populating table data.
        $tabledata = [];
        foreach ($data as $key => $total) {
            // If the total is empty it means there is no results yet in the table.
            if ($total === null) {
                continue;
            }
            $type = area_base::checkgroup_name($key);
            // Error display data.
            $display = $total;
            // Icons.
            $typeicon = $OUTPUT->pix_icon('f/' . $type, '', 'block_accessreview');
            if ($errordisplay == 'showicon') {
                $thistype = $total == 0 ? 'smile' : 'frown';
                $display = $OUTPUT->pix_icon($thistype,
                    get_string($thistype, 'block_accessreview'), 'block_accessreview'
                );
            } else if ($errordisplay == 'showpercent') {
                $display = round(($total), 1) . '%';
            }
            $tabledata[] = [$typeicon . get_string('checktype:' . $type, manager::PLUGINNAME), $display];
        }
        return $tabledata;
    }

    /**
     * Get the link to toggle the heatmap.
     *
     * @return string
     * @throws coding_exception
     */
    protected function get_toggle_link(): string {
        global $OUTPUT;

        if (get_user_preferences('block_accessreviewtogglestate')) {
            $icon = 't/hide';
        } else {
            $icon = 't/show';
        }

        // Toggle overlay link.
        return html_writer::link(
            '#',
            $OUTPUT->pix_icon($icon, get_string('togglealt', 'block_accessreview'), 'moodle', ['class' => 'icon-accessmap']),
            [
                'title' => get_string('togglealt', 'block_accessreview'),
                'style' => 'cursor: pointer;',
                'id' => 'toggle-accessmap',
                'class' => 'block_accessreview_link',
            ]
        );
    }

    /**
     * Get the link to download a report for the specified context.
     *
     * @param context $context
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function get_download_link(context $context): string {
        global $OUTPUT, $COURSE;

        if (has_capability(accessibility::get_capability_name('viewcoursetools'), $context)) {
            return html_writer::link(
                new moodle_url(accessibility::get_plugin_url(),
                    [
                        'courseid' => $COURSE->id,
                        'tab' => 'printable',
                        'target' => 'pdf',
                    ]
                ),
                $OUTPUT->pix_icon('a/download_all', get_string('downloadreportalt', 'block_accessreview')),
                [
                    'title' => get_string('downloadreportalt', 'block_accessreview'),
                    'class' => 'block_accessreview_link download-accessmap',
                ]
            );
        } else {
            return '';
        }
    }

    /**
     * Get the report link for the specified context.
     *
     * @param context $context
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    protected function get_report_link(context $context): string {
        global $OUTPUT, $COURSE;

        if (has_capability(accessibility::get_capability_name('viewcoursetools'), $context)) {
            return html_writer::link(
                new moodle_url(accessibility::get_plugin_url(),
                    [
                        'courseid' => $COURSE->id,
                        'tab' => get_config('block_accessreview', 'toolpage'),
                    ]
                ),
                $OUTPUT->pix_icon('f/find', get_string('viewreportalt', 'block_accessreview'), 'block_accessreview'),
                [
                    'title' => get_string('viewreportalt', 'block_accessreview'),
                    'class' => 'block_accessreview_link report-accessmap',
                ]
            );
        } else {
            return '';
        }
    }
}
