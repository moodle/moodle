<?php

namespace theme_lms\output\core;

use context_course;
use context_system;
use core_course_category;
use core_course_list_element;
use core_text;
use coursecat_helper;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die;

class course_renderer extends \core_course_renderer
{


    protected function course_name(coursecat_helper $chelper, core_course_list_element $course): string
    {
        $content = '';
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $nametag = 'div';
        }
        $coursename = $chelper->get_course_formatted_name($course);
//        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
//            $coursename, ['class' => $course->visible ? 'aalink' : 'aalink dimmed']);
        $coursenamelink = html_writer::tag("div", $coursename, ['class' => "title-course"]);
        $content .= html_writer::tag($nametag, $coursenamelink, ['class' => 'coursename']);
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', ['class' => 'moreinfo']);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()
                || $course->has_custom_fields()) {
                $url = new moodle_url('/course/info.php', ['id' => $course->id]);
                $image = $this->output->pix_icon('i/info', $this->strings->summary);
                $content .= html_writer::link($url, $image, ['title' => $this->strings->summary]);
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('div');
        return $content;
    }

    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '')
    {
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = '';
        $classes = trim('coursebox customize-coursebox clearfix ' . $additionalclasses);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'info'));
        $content .= $this->course_name($chelper, $course);
        $content .= $this->course_enrolment_icons($course);
        $content .= html_writer::end_tag('div');

        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div');

        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }
    protected function course_summary(coursecat_helper $chelper, core_course_list_element $course): string
    {
        $content = html_writer::start_tag('div', ['class' => 'summary']);
        if ($course->has_summary()) {
            $content .= $this->get_course_formatted_summary($course,
                array('overflowdiv' => true, 'noclean' => true, 'para' => false));
        }
        $content .= html_writer::end_tag('div');

        return $content;
    }

    public function get_course_formatted_summary($course, $options = array())
    {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        if (!$course->has_summary()) {
            return '';
        }
        $options = (array)$options;
        $context = context_course::instance($course->id);
        if (!isset($options['context'])) {
            // TODO see MDL-38521
            // option 1 (current), page context - no code required
            // option 2, system context
            // $options['context'] = context_system::instance();
            // option 3, course context:
            // $options['context'] = $context;
            // option 4, course category context:
            // $options['context'] = $context->get_parent_context();
        }
        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);
        $summary = format_text($summary, $course->summaryformat, $options, $course->id);
        if (!empty($this->searchcriteria['search'])) {
            $summary = highlight($this->searchcriteria['search'], $summary);
        }
        $summary = mb_substr(strip_tags($summary), 0, 150, 'UTF-8');
        return $summary . '...';
    }

    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course)
    {
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = \html_writer::start_tag('div', ['class' => 'd-flex']);
        $content .= $this->course_overview_files($course);
        $content .= \html_writer::start_tag('div', ['class' => 'flex-grow-1']);
        $content .= $this->course_summary($chelper, $course);
//        $content .= $this->course_contacts($course);

        $content .= $this->course_category_name($chelper, $course);
        $content .= $this->course_custom_fields($course);
        $content .= $this->course_button_footer($course);
        $content .= \html_writer::end_tag('div');
        $content .= \html_writer::end_tag('div');
        return $content;
    }

//
    protected function course_button_footer($course)
    {
        $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
        return \html_writer::tag('a', get_string('continuepractice'), [
            'href' => $courseurl,
            'class' => 'btn btn-outline-primary mt-2',
            'role' => 'button'
        ]);
    }

    public function frontpage_available_courses()
    {
        global $CFG;

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
        set_courses_display_options(array(
            'recursive' => true,
            'limit' => $CFG->frontpagecourselimit,
            'viewmoreurl' => new moodle_url('/course/index.php'),
            'viewmoretext' => new lang_string('fulllistofcourses')));

        $chelper->set_attributes(array('class' => 'frontpage-course-list-all customize-course-home'));
        $courses = core_course_category::top()->get_courses($chelper->get_courses_display_options());
        $totalcount = core_course_category::top()->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }
        return $this->coursecat_courses($chelper, $courses, $totalcount);
    }
    protected function frontpage_part($skipdivid, $contentsdivid, $header, $contents)
    {
        if (strval($contents) === '') {
            return '';
        }
        $output = html_writer::link('#' . $skipdivid,
            get_string('skipa', 'access', core_text::strtolower(strip_tags($header))),
            array('class' => 'skip-block skip aabtn'));

        // Wrap frontpage part in div container.
        $output .= html_writer::start_tag('div', array('id' => $contentsdivid));
        if ($contentsdivid === 'frontpage-available-course-list') {
            $output .= html_writer::tag("div", $header, array('class' => 'customize-available-course mb-3'));
        } else {
            $output .= $this->heading($header);
        }

        $output .= $contents;

        // End frontpage part div container.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::tag('span', '', array('class' => 'skip-block-to', 'id' => $skipdivid));
        return $output;
    }
    public function frontpage()
    {
        global $CFG, $SITE;

        $output = '';

        if (isloggedin() and !isguestuser() and isset($CFG->frontpageloggedin)) {
            $frontpagelayout = $CFG->frontpageloggedin;
        } else {
            $frontpagelayout = $CFG->frontpage;
        }

        foreach (explode(',', $frontpagelayout) as $v) {
            switch ($v) {
                // Display the main part of the front page.
                case FRONTPAGENEWS:
                    if ($SITE->newsitems) {
                        // Print forums only when needed.
                        require_once($CFG->dirroot . '/mod/forum/lib.php');
                        if (($newsforum = forum_get_course_forum($SITE->id, 'news')) &&
                            ($forumcontents = $this->frontpage_news($newsforum))) {
                            $newsforumcm = get_fast_modinfo($SITE)->instances['forum'][$newsforum->id];
                            $output .= $this->frontpage_part('skipsitenews', 'site-news-forum',
                                $newsforumcm->get_formatted_name(), $forumcontents);
                        }
                    }
                    break;

                case FRONTPAGEENROLLEDCOURSELIST:
                    $mycourseshtml = $this->frontpage_my_courses();
                    if (!empty($mycourseshtml)) {
                        $output .= $this->frontpage_part('skipmycourses', 'frontpage-course-list',
                            get_string('mycourses'), $mycourseshtml);
                    }
                    break;

                case FRONTPAGEALLCOURSELIST:
                    $availablecourseshtml = $this->frontpage_available_courses();
                    $output .= $this->frontpage_part('skipavailablecourses', 'frontpage-available-course-list',
                        get_string('availablecourses'), $availablecourseshtml);
                    break;

                case FRONTPAGECATEGORYNAMES:
                    $output .= $this->frontpage_part('skipcategories', 'frontpage-category-names',
                        get_string('categories'), $this->frontpage_categories_list());
                    break;

                case FRONTPAGECATEGORYCOMBO:
                    $output .= $this->frontpage_part('skipcourses', 'frontpage-category-combo',
                        get_string('courses'), $this->frontpage_combo_list());
                    break;

                case FRONTPAGECOURSESEARCH:
                    $output .= $this->box($this->course_search_form(''), 'd-flex justify-content-center');
                    break;

            }
            $output .= '<br />';
        }

        return $output;
    }
}

