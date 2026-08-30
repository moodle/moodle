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
 * Contains the class for the Timeline block.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_timeline extends block_base {

    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_timeline');
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {
        global $CFG, $OUTPUT;

        if (isset($this->content)) {
            return $this->content;
        }

        require_once($CFG->dirroot . '/blocks/timeline/lib.php');

        $courses = enrol_get_my_courses(['id'], null, 1);

        $filter = get_user_preferences('block_timeline_user_filter_preference') ?: BLOCK_TIMELINE_FILTER_BY_30_DAYS;
        $order = get_user_preferences('block_timeline_user_sort_preference') ?: BLOCK_TIMELINE_SORT_BY_DATES;
        $limit = get_user_preferences('block_timeline_user_limit_preference') ?: BLOCK_TIMELINE_ACTIVITIES_LIMIT_DEFAULT;

        $props = [
            'midnight'           => usergetmidnight(\core\di::get(\core\clock::class)->time()),
            'filter'             => $filter,
            'order'              => $order,
            'limit'              => (int) $limit,
            'nocoursesurl'       => $OUTPUT->image_url('courses', 'block_timeline')->out(false),
            'noeventsurl'        => $OUTPUT->image_url('activities', 'block_timeline')->out(false),
            'hasenrolledcourses' => !empty($courses),
        ];

        // Mount the React Timeline component directly — no renderer, no template.
        $this->content = (object) [
            'text' => \core\output\html_writer::react_component(
                '@moodle/lms/block_timeline/Timeline',
                $props,
            ),
            'footer' => '',
        ];

        return $this->content;
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return ['my' => true];
    }
}
