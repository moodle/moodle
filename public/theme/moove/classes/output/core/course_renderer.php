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
 * Course renderer.
 *
 * @package    theme_moove
 * @copyright  2025 Willian Mano - willianmanoaraujo@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\output\core;

use stdClass;
use moodle_url;
use html_writer;
use coursecat_helper;
use core_course_list_element;
use theme_moove\util\course;
use core\lang_string;

/**
 * The core course renderer
 *
 * Can be retrieved with the following:
 * $renderer = $PAGE->get_renderer('core','course');
 */
class course_renderer extends \core_course_renderer {
    /**
     * Renders the list of courses
     *
     * This is internal function, please use core_course_renderer::courses_list or another public
     * method from outside of the class
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses the list of courses to display
     * @param int|null $totalcount total number of courses (affects display mode if it is AUTO or pagination if applicable),
     *     defaulted to count($courses)
     * @return string
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit.
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // Prepare content of paging bar if it is needed.
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // There are more results that can fit on one page.
            if ($paginationurl) {
                // The option paginationurl was specified, display pagingbar.
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar(
                    $totalcount,
                    $page,
                    $perpage,
                    $paginationurl->out(false, ['perpage' => $perpage])
                );
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag(
                        'div',
                        html_writer::link(
                            $paginationurl->out(false, ['perpage' => 'all']),
                            get_string('showall', '', $totalcount)
                        ),
                        ['class' => 'paging paging-showall']
                    );
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // The option for 'View more' link was specified, display more link.
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag(
                    'div',
                    html_writer::link($viewmoreurl, $viewmoretext, ['class' => 'btn btn-secondary']),
                    ['class' => 'paging paging-morelink']
                );
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // There are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode.
            $pagingbar = html_writer::tag(
                'div',
                html_writer::link(
                    $paginationurl->out(false, ['perpage' => $CFG->coursesperpage]),
                    get_string('showperpage', '', $CFG->coursesperpage)
                ),
                ['class' => 'paging paging-showperpage']
            );
        }

        // Display list of courses.
        $attributes = $chelper->get_and_erase_attributes('courses');
        $attributes['class'] .= ' row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-1';

        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        foreach ($courses as $course) {
            $content .= $this->coursecat_coursebox($chelper, $course);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // Closes courses div.
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use core_course_renderer::course_info_box
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }

        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }

        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        return $this->coursecat_coursebox_content($chelper, $course);
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $courseutil = new course($course);

        $coursecontacts = $courseutil->get_course_contacts();

        $courseenrolmenticons = $courseutil->get_enrolment_icons();
        $courseenrolmenticons = !empty($courseenrolmenticons) ? $this->render_enrolment_icons($courseenrolmenticons) : false;

        $courseprogress = $courseutil->get_progress();
        $hasprogress = $courseprogress != null;

        $data = [
            'id' => $course->id,
            'fullname' => $chelper->get_course_formatted_name($course),
            'visible' => $course->visible,
            'image' => $courseutil->get_summary_image(),
            'summary' => $courseutil->get_summary($chelper),
            'category' => $courseutil->get_category(),
            'customfields' => $courseutil->get_custom_fields(),
            'hasprogress' => $hasprogress,
            'progress' => (int) $courseprogress,
            'hasenrolmenticons' => $courseenrolmenticons != false,
            'enrolmenticons' => $courseenrolmenticons,
            'hascontacts' => !empty($coursecontacts),
            'contacts' => $coursecontacts,
            'courseurl' => $this->get_course_url($course->id),
        ];

        return $this->render_from_template('theme_moove/moove/coursecard', $data);
    }

    /**
     * Returns the course URL based on some criterias.
     *
     * @param int $courseid
     *
     * @return moodle_url
     * @throws \moodle_exception
     */
    private function get_course_url($courseid) {
        if (class_exists('\local_course\output\index')) {
            return new moodle_url('/local/course/index.php', ['id' => $courseid]);
        }

        return new moodle_url('/course/view.php', ['id' => $courseid]);
    }

    /**
     * Returns enrolment icons
     *
     * @param array $icons
     *
     * @return array
     */
    protected function render_enrolment_icons(array $icons): array {
        $data = [];

        foreach ($icons as $icon) {
            $data[] = $this->render($icon);
        }

        return $data;
    }
}
