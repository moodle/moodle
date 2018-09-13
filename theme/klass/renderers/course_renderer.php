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
 * renderers/course_renderer.php
 *
 * @package    theme_klass
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team , lmsace.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/course/renderer.php");

/**
 * Klass theme course renderer class
 *
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_klass_core_course_renderer extends core_course_renderer {

    /**
     * Create the new course block to display in the frontpage.
     */
    public function new_courses() {
        /* New Courses */
        global $CFG, $OUTPUT;
        $newcourse = get_string('newcourses', 'theme_klass');
        $header = '<div id="frontpage-course-list">
        <h2>'.$newcourse.'</h2>
        <div class="courses frontpage-course-list-all">
        <div class="row">';
        $footer = '</div>
        </div>
        </div>';
        $cocnt = 1;
        $content = '';
        if ($ccc = get_courses('all', 'c.id DESC, c.sortorder ASC', 'c.id, c.shortname, c.visible')) {
            foreach ($ccc as $cc) {
                if ($cocnt > 8) {
                    break;
                }
                if ( $cc->visible == "0" || $cc->id == "1") {
                    continue;
                }
                $courseid = $cc->id;
                $course = get_course($courseid);
                $noimgurl = $OUTPUT->image_url('no-image', 'theme');
                $courseurl = new moodle_url('/course/view.php', array('id' => $courseid ));
                if ($course instanceof stdClass) {
                    require_once($CFG->libdir. '/coursecatlib.php');
                    $course = new course_in_list($course);
                }
                $imgurl = '';
                $context = context_course::instance($course->id);
                foreach ($course->get_course_overviewfiles() as $file) {
                    $isimage = $file->is_valid_image();
                    $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                    if (!$isimage) {
                        $imgurl = $noimgurl;
                    }
                }
                if (empty($imgurl)) {
                    $imgurl = $noimgurl;
                }
                 $icon = "fa-angle-double-right";
                if (right_to_left()) {
                    $icon = "fa-angle-double-left";
                }
                $content .= '<div class="col-md-3">
                <div class="fp-coursebox">
                <div class="fp-coursethumb">
                <a href="'.$courseurl.'">
                <img src="'.$imgurl.'" width="243" height="165" alt="'.$course->fullname.'">
                </a>
                </div>
                <div class="fp-courseinfo">
                <h5><a href="'.$courseurl.'">'.$course->fullname.'</a></h5>
                <div class="readmore"><a href="'.$courseurl.'">'.get_string("readmore",
                 "theme_klass").'<i class="fa '.$icon.'"></i></a></div>
                </div>
                </div>
                </div>';
                if ( ( $cocnt % 4) == "0") {
                    $content .= '<div class="clearfix hidexs"></div>';
                }
                $cocnt++;
            }
        }
        $coursehtml = $header.$content.$footer;
        $frontpage = isset($CFG->frontpage) ? $CFG->frontpage : '';
        $frontpageloggedin = isset($CFG->frontpageloggedin) ? $CFG->frontpageloggedin : '';
        $f1pos = strpos($frontpage, '6');
        $f2pos = strpos($frontpageloggedin, '6');
        $btnhtml = '';
        if ($cocnt <= 1 && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
            $btnhtml = $this->add_new_course_button();
        }
        if (!isloggedin() or isguestuser()) {
            if ($f1pos === false) {
                if ($cocnt > 1) {
                    echo $coursehtml;
                }
            }
        } else {
            if ($f2pos === false) {
                echo $coursehtml."<br/>".$btnhtml;
            }
        }
    }

    /**
     * Renderer for the frontpage available course
     * @return type|string
     */
    public function frontpage_available_courses() {
        /* available courses */
        global $CFG, $OUTPUT;
        require_once($CFG->libdir. '/coursecatlib.php');
        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options(array(
        'recursive' => true,
        'limit' => $CFG->frontpagecourselimit,
        'viewmoreurl' => new moodle_url('/course/index.php'),
        'viewmoretext' => new lang_string('fulllistofcourses')));
        $chelper->set_attributes(array('class' => 'frontpage-course-list-all'));
        $courses = coursecat::get(0)->get_courses($chelper->get_courses_display_options());
        $totalcount = coursecat::get(0)->get_courses_count($chelper->get_courses_display_options());
        $courseids = array_keys($courses);
        $newcourse = get_string('availablecourses');
        $header = '<div id="frontpage-course-list">
        <h2>'.$newcourse.'</h2>
        <div class="courses frontpage-course-list-all">
        <div class="row">';
        $footer = '</div>
        </div>
        </div>';
        $cocnt = 1;
        $content = '';
        if ($ccc = get_courses('all', 'c.sortorder ASC', 'c.id, c.shortname, c.visible')) {
            foreach ($courseids as $courseid) {
                $course = get_course($courseid);
                $noimgurl = $OUTPUT->image_url('no-image', 'theme');
                $courseurl = new moodle_url('/course/view.php', array('id' => $courseid ));
                if ($course instanceof stdClass) {
                    require_once($CFG->libdir. '/coursecatlib.php');
                    $course = new course_in_list($course);
                }
                $imgurl = '';
                $context = context_course::instance($course->id);
                foreach ($course->get_course_overviewfiles() as $file) {
                    $isimage = $file->is_valid_image();
                    $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                    if (!$isimage) {
                        $imgurl = $noimgurl;
                    }
                }
                if (empty($imgurl)) {
                    $imgurl = $noimgurl;
                }
                $icon = "fa-angle-double-right";
                if (right_to_left()) {
                    $icon = "fa-angle-double-left";
                }
                $content .= '<div class="col-md-3">
                <div class="fp-coursebox">
                <div class="fp-coursethumb">
                <a href="'.$courseurl.'">
                <img src="'.$imgurl.'" width="243" height="165" alt="'.$course->fullname.'">
                </a>
                </div>
                <div class="fp-courseinfo">
                <h5><a href="'.$courseurl.'">'.$course->fullname.'</a></h5>
                <div class="readmore"><a href="'.$courseurl.'">'.get_string("readmore",
                    "theme_klass").'&nbsp; <i class="fa '.$icon.'"></i></a></div>
                </div>
                </div>
                </div>';
                if (($cocnt % 4) == "0") {
                    $content .= '<div class="clearfix hidexs"></div>';
                }
                $cocnt++;
            }
        }
        $coursehtml = $header.$content.$footer;
        echo $coursehtml;
        if (!$totalcount && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            echo $this->add_new_course_button();
        }
    }

    /**
     * Renderer the course cat course box from the parent
     *
     * @param coursecat_helper $chelper
     * @param int $course
     * @param string $additionalclasses
     * @return $content
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
        $classes = trim('coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }
        // Coursebox.
        if (empty($course->get_course_overviewfiles())) {
            $coursecontent = "content-block";
        } else {
            $coursecontent = "";
        }
        $content .= html_writer::start_tag('div', array(
            'class' => $classes.' '.$coursecontent,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));
        $content .= html_writer::start_tag('div', array('class' => 'info'));
        // Course name.
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', array('class' => 'moreinfo'));
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $image = html_writer::empty_tag('img', array('src' => $this->output->image_url('i/info'),
                    'alt' => $this->strings->summary));
                $content .= html_writer::link($url, $image, array('title' => $this->strings->summary));
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('div'); // Moreinfo.
        // Print enrolmenticons.
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pixicon) {
                $content .= $this->render($pixicon);
            }
            $content .= html_writer::end_tag('div'); // Enrolmenticons.
        }
        $content .= html_writer::end_tag('div'); // Info.
        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div'); // Content.
        $content .= html_writer::end_tag('div'); // Coursebox.
        return $content;
    }
}