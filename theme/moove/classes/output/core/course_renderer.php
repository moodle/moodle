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
 * @copyright  2017 Willian Mano - conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\output\core;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use html_writer;
use core_course_category;
use coursecat_helper;
use stdClass;
use core_course_list_element;
use theme_moove\util\extras;

/**
 * Renderers to align Moove's course elements to what is expect
 *
 * @package    theme_moove
 * @copyright  2017 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {

    /**
     * Renders the list of courses
     *
     * This is internal function, please use core_course_renderer::courses_list() or another public
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
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;

        $theme = \theme_config::load('moove');

        if (!empty($theme->settings->courselistview)) {
            return parent::coursecat_courses($chelper, $courses, $totalcount);
        }

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
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // The option for 'View more' link was specified, display more link.
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new \lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // There are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode.
            $pagingbar = html_writer::tag(
                'div',
                html_writer::link(
                    $paginationurl->out(
                        false,
                        array('perpage' => $CFG->coursesperpage)
                    ),
                    get_string('showperpage', '', $CFG->coursesperpage)
                ),
                array('class' => 'paging paging-showperpage')
            );
        }

        // Display list of courses.
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 1;
        $content .= html_writer::start_tag('div', array('class' => 'card-deck mt-2'));
        foreach ($courses as $course) {
            $content .= $this->coursecat_coursebox($chelper, $course);

            if ($coursecount % 4 == 0) {
                $content .= html_writer::end_tag('div');
                $content .= html_writer::start_tag('div', array('class' => 'card-deck mt-2'));
            }

            $coursecount ++;
        }

        $content .= html_writer::end_tag('div');

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // End courses.
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use core_course_renderer::course_info_box()
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        $theme = \theme_config::load('moove');

        if (!empty($theme->settings->courselistview)) {
            return parent::coursecat_coursebox($chelper, $course, $additionalclasses);
        }

        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }

        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }

        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $classes = trim('card');
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // End coursebox.
        $content = html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= $this->coursecat_coursebox_content($chelper, $course);

        $content .= html_writer::end_tag('div'); // End coursebox.

        return $content;
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     *
     * @return string
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $coursename = $chelper->get_course_formatted_name($course);
        $courselink = new moodle_url('/course/view.php', array('id' => $course->id));
        $coursenamelink = html_writer::link($courselink, $coursename, array('class' => $course->visible ? '' : 'dimmed'));

        $content = extras::get_course_summary_image($course, $courselink);
        $content .= $this->course_contacts($course);
        $content .= $this->course_card_body($chelper, $course, $coursenamelink);
        $content .= $this->course_card_footer($course);

        return $content;
    }

    /**
     * Returns HTML to display course summary.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_summary(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';
        if ($course->has_summary()) {
            $content .= html_writer::start_tag('p', ['class' => 'card-text']);
            $content .= $chelper->get_course_formatted_summary($course,
                array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            $content .= html_writer::end_tag('p'); // End summary.
        }
        return $content;
    }

    /**
     * Returns HTML to display course contacts.
     *
     * @param core_course_list_element $course
     *
     * @return string
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function course_contacts(core_course_list_element $course) {
        global $CFG, $DB;

        $theme = \theme_config::load('moove');

        $content = '';
        if ($course->has_course_contacts() && !($theme->settings->disableteacherspic)) {
            $content .= html_writer::start_tag('div', ['class' => 'course-contacts']);

            $instructors = $course->get_course_contacts();
            foreach ($instructors as $key => $instructor) {
                $name = $instructor['username'];
                $url = $CFG->wwwroot.'/user/profile.php?id='.$key;
                $picture = extras::get_user_picture($DB->get_record('user', array('id' => $key)));

                $content .= "<a href='{$url}' class='contact' data-toggle='tooltip' title='{$name}'>";
                $content .= "<img src='{$picture}' class='rounded-circle' alt='{$name}'/>";
                $content .= "</a>";
            }

            $content .= html_writer::end_tag('div');
        }

        return $content;
    }

    /**
     * Generates the course card body html
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @param string $coursenamelink
     *
     * @return string
     *
     * @throws \moodle_exception
     */
    protected function course_card_body(coursecat_helper $chelper, core_course_list_element $course, $coursenamelink) {
        $content = html_writer::start_tag('div', ['class' => 'card-body']);

        $content .= $this->course_category_name($chelper, $course);

        $content .= html_writer::tag('h4', $coursenamelink, ['class' => 'card-title']);

        $content .= $this->course_summary($chelper, $course);

        $content .= html_writer::end_tag('div');

        return $content;
    }

    /**
     * Returns HTML to display course category name.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     *
     * @return string
     *
     * @throws \moodle_exception
     */
    protected function course_category_name(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';

        if ($cat = core_course_category::get($course->category, IGNORE_MISSING)) {
            $content .= html_writer::start_tag('div', ['class' => 'coursecat badge badge-info']);
            $content .= html_writer::link(new moodle_url('/course/index.php', ['categoryid' => $cat->id]),
                    $cat->get_formatted_name(), ['class' => $cat->visible ? 'text-white' : 'dimmed']);
            $content .= html_writer::end_tag('div');
        }

        return $content;
    }

    /**
     * Generates the course card footer html
     *
     * @param core_course_list_element $course
     *
     * @return string
     *
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function course_card_footer(core_course_list_element $course) {
        $content = '';

        if (isloggedin()) {
            $content .= $this->course_custom_fields($course);

            $content .= html_writer::start_tag('div', ['class' => 'card-footer']);

            $content .= $this->course_enrolment_icons($course);

            $content .= html_writer::start_tag('div', ['class' => 'pull-right']);
            $content .= html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
                get_string('access', 'theme_moove'), ['class' => 'card-link btn btn-primary']);
            $content .= html_writer::end_tag('div'); // End pull-right.

            $content .= html_writer::end_tag('div'); // End card-footer.
        }

        return $content;
    }

    /**
     * Returns HTML to display course custom fields.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_custom_fields(core_course_list_element $course): string {
        $content = '';

        if ($course->has_custom_fields()) {
            $handler = \core_course\customfield\course_handler::create();
            $customfields = $handler->display_custom_fields_data($course->get_custom_fields());
            $content .= \html_writer::tag('div', $customfields, ['class' => 'customfields-container card-footer']);
        }

        return $content;
    }

    /**
     * Returns HTML to display course enrolment icons.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_enrolment_icons(core_course_list_element $course): string {
        $content = '';

        if ($icons = enrol_get_course_info_icons($course)) {
            foreach ($icons as $icon) {
                $content .= $this->render($icon);
            }
        }

        return $content;
    }
}
