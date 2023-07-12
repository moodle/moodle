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
 * lw_courses block rendrer
 *
 * @package    block_lw_courses
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @copyright  2017 Mathew May <mathewm@hotmail.co.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/externallib.php');
/**
 * lw_courses block rendrer
 *
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_lw_courses_renderer extends plugin_renderer_base {

    /**
     * Construct contents of lw_courses block
     *
     * @param array $courses list of courses in sorted order
     * @return string html to be displayed in lw_courses block
     */
    public function lw_courses($courses) {
        global $CFG, $PAGE, $DB, $OUTPUT;
        $html = '';
        // LearningWorks.
        $PAGE->requires->js(new moodle_url($CFG->wwwroot.'/blocks/lw_courses/js/custom.js'));
        $config = get_config('block_lw_courses');
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $ismovingcourse = false;
        $courseordernumber = 0;
        $userediting = false;
        // Intialise string/icon etc if user is editing and courses > 1.
        if ($this->page->user_is_editing() && (count($courses) > 1)) {
            $userediting = true;
            $this->page->requires->js_init_call('M.block_lw_courses.add_handles');

            // Check if course is moving.
            $ismovingcourse = optional_param('movecourse', false, PARAM_BOOL);
            $movingcourseid = optional_param('courseid', 0, PARAM_INT);
        }

        // Render first movehere icon.
        if ($ismovingcourse) {
            // Remove movecourse param from url.
            $this->page->ensure_param_not_in_url('movecourse');

            // Show moving course notice, so user knows what is being moved.
            $html .= $this->output->box_start('notice');
            $a = new stdClass();
            $a->fullname = $courses[$movingcourseid]->fullname;
            $a->cancellink = html_writer::link($this->page->url, get_string('cancel'));
            $html .= get_string('movingcourse', 'block_lw_courses', $a);
            $html .= $this->output->box_end();

            $moveurl = new moodle_url('/blocks/lw_courses/move.php',
                array('sesskey' => sesskey(), 'moveto' => 0, 'courseid' => $movingcourseid));
            if (method_exists($this->output, 'image_url')) {
                // Use the new method.
                $moveicon = $this->output->image_url('movehere');
            } else {
                // Still a pre Moodle 3.3 release. Use pix_url because image_url doesn't exist yet.
                $moveicon = $this->output->pix_url('movehere');
            }
            // Create move icon, so it can be used.
            $movetofirsticon = html_writer::empty_tag('img',
                array('src' => $moveicon,
                    'alt' => get_string('movetofirst', 'block_lw_courses', $courses[$movingcourseid]->fullname),
                    'title' => get_string('movehere')));
            $moveurl = html_writer::link($moveurl, $movetofirsticon);
            $html .= html_writer::tag('div', $moveurl, array('class' => 'movehere'));
        }

        // LearningWorks.
        $gridsplit = intval(12 / count($courses)); // Added intval to avoid any float.

        // Set a minimum size for the course 'cards'.
        $colsize = intval($config->coursegridwidth) > 0 ? intval($config->coursegridwidth) : BLOCKS_LW_COURSES_DEFAULT_COL_SIZE;
        if ($gridsplit < $colsize) {
            $gridsplit = $colsize;
        }

        $courseclass = $config->startgrid == BLOCKS_LW_COURSES_STARTGRID_YES ? "grid" : "list";
        $startvalue = $courseclass == "list" ? "12" : $gridsplit;

        $listonly = false;
        if ($gridsplit == 12) {
            $listonly = true;
            $startvalue = 12;
            $courseclass = "list";
        } else {
            $html .= html_writer::tag('a', 'Change View', array('href' => '#', 'id' => 'box-or-lines',
            'styles' => '', 'class' => "$courseclass col-md-$startvalue span$startvalue $courseclass"));
        }
        $html .= html_writer::tag('div', '', array("class" => "hidden startgrid $courseclass", "grid-size" => $gridsplit));
        $html .= html_writer::div('', 'box flush');

        $allnames = get_all_user_name_fields(true, 'u');
        $fields = 'u.id, u.confirmed, u.username, '. $allnames . ', ' .
            'u.maildisplay, u.mailformat, u.maildigest, u.email, u.emailstop, u.city, '.
            'u.country, u.picture, u.idnumber, u.department, u.institution, '.
            'u.lang, u.timezone, u.lastaccess, u.mnethostid, u.imagealt, r.name AS rolename, r.sortorder, '.
            'r.shortname AS roleshortname, rn.name AS rolecoursealias';

        $html .= html_writer::start_div('lw_courses_list');
        foreach ($courses as $key => $course) {

            // If moving course, then don't show course which needs to be moved.
            if ($ismovingcourse && ($course->id == $movingcourseid)) {
                continue;
            }

            $html .= $this->output->box_start(
                "coursebox $courseclass span$startvalue col-md-$startvalue $courseclass col-xs-12",
                "course-{$course->id}");
            $html .= $this->course_image($course);

            $teacherimages = html_writer::start_div('teacher_image_wrap');
            $teachernames = '';
            if ($course->id > 0 && !empty($role) && $config->showteachers != BLOCKS_LW_COURSES_SHOWTEACHERS_NO) {
                $context = context_course::instance($course->id);
                $teachers = get_role_users($role->id, $context, false, $fields);
                foreach ($teachers as $key => $teacher) {
                    $teachername = get_string('defaultcourseteacher') . ': ' . fullname($teacher);
                    $teachernames .= html_writer::tag('p', $teachername, array('class' => 'teacher_name'));
                    $teacherimages .= html_writer::div($OUTPUT->user_picture($teacher, array('size' => 50, 'class' => '')), 'teacher_image');
                }
            }
            $teacherimages .= html_writer::end_div();
            $html .= $teacherimages;

            if (method_exists($this->output, 'image_url')) {
                // Use the new method.
                $moveicon = $this->image_url('t/move');
            } else {
                // Still a pre Moodle 3.3 release. Use pix_url because image_url doesn't exist yet.
                $moveicon = $this->pix_url('t/move');
            }
            $html .= html_writer::start_tag('div', array('class' => 'course_title'));
            // If user is editing, then add move icons.
            if ($userediting && !$ismovingcourse) {
                $moveicon = html_writer::empty_tag('img',
                    array('src' => $moveicon->out(false),
                        'alt' => get_string('movecourse', 'block_lw_courses', $course->fullname),
                        'title' => get_string('move')));
                $moveurl = new moodle_url($this->page->url, array('sesskey' => sesskey(), 'movecourse' => 1,
                    'courseid' => $course->id));
                $moveurl = html_writer::link($moveurl, $moveicon);
                $html .= html_writer::tag('div', $moveurl, array('class' => 'move'));
            }

            // No need to pass title through s() here as it will be done automatically by html_writer.
            $attributes = array('title' => $course->fullname);
            if ($course->id > 0) {
                if (empty($course->visible)) {
                    $attributes['class'] = 'dimmed';
                }
                $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
                $coursefullname = format_string(get_course_display_name_for_list($course), true, $course->id);
                $link = html_writer::link($courseurl, $coursefullname, $attributes);
                $html .= $this->output->heading($link, 2, 'title');
            } else {
                $html .= $this->output->heading(html_writer::link(
                       new moodle_url('/auth/mnet/jump.php', array(
                       'hostid' => $course->hostid,
                       'wantsurl' => '/course/view.php?id='.$course->remoteid)),
                       format_string($course->shortname, true), $attributes) .
                       ' (' . format_string($course->hostname) . ')', 2, 'title');
            }
            $html .= $this->output->box('', 'flush');
            $html .= html_writer::end_tag('div');

            if (!empty($config->showchildren) && ($course->id > 0)) {
                // List children here.
                if ($children = block_lw_courses_get_child_shortnames($course->id)) {
                    $html .= html_writer::tag('span', $children, array('class' => 'coursechildren'));
                }
            }

            if ($course->id > 0) {
                $html .= $this->course_description($course);

                $html .= block_lw_courses_build_progress($course);

                $html .= html_writer::div($teachernames, 'teacher_names');
            }

            if ($config->showcategories != BLOCKS_LW_COURSES_SHOWCATEGORIES_NONE) {
                // List category parent or categories path here.
                $currentcategory = core_course_category::get($course->category, IGNORE_MISSING);
                if ($currentcategory !== null) {
                    $html .= html_writer::start_tag('div', array('class' => 'categorypath'));
                    if ($config->showcategories == BLOCKS_LW_COURSES_SHOWCATEGORIES_FULL_PATH) {
                        foreach ($currentcategory->get_parents() as $categoryid) {
                            $category = core_course_category::get($categoryid, IGNORE_MISSING);
                            if ($category !== null) {
                                $html .= $category->get_formatted_name().' / ';
                            }
                        }
                    }
                    $html .= $currentcategory->get_formatted_name();
                    $html .= html_writer::end_tag('div');
                }
            }

            $html .= $this->output->box('', 'flush');
            $html .= $this->output->box_end();
            $courseordernumber++;
            if (method_exists($this->output, 'image_url')) {
                // Use the new method.
                $movehere = $this->output->image_url('movehere');
            } else {
                // Still a pre Moodle 3.3 release. Use pix_url because image_url doesn't exist yet.
                $movehere = $this->output->pix_url('movehere');
            }
            if ($ismovingcourse) {
                $moveurl = new moodle_url('/blocks/lw_courses/move.php',
                    array('sesskey' => sesskey(), 'moveto' => $courseordernumber, 'courseid' => $movingcourseid));
                $a = new stdClass();
                $a->movingcoursename = $courses[$movingcourseid]->fullname;
                $a->currentcoursename = $course->fullname;
                $movehereicon = html_writer::empty_tag('img',
                    array('src' => $movehere,
                        'alt' => get_string('moveafterhere', 'block_lw_courses', $a),
                        'title' => get_string('movehere')));
                $moveurl = html_writer::link($moveurl, $movehereicon);
                $html .= html_writer::tag('div', $moveurl, array('class' => 'movehere'));
            }

        }

        // Wrap course list in a div and return.
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Construct activities overview for a course
     *
     * @param int $cid course id
     * @param array $overview overview of activities in course
     * @return string html of activities overview
     */
    protected function activity_display($cid, $overview) {
        $output = html_writer::start_tag('div', array('class' => 'activity_info'));
        foreach (array_keys($overview) as $module) {
            $output .= html_writer::start_tag('div', array('class' => 'activity_overview'));
            $url = new moodle_url("/mod/$module/index.php", array('id' => $cid));
            $modulename = get_string('modulename', $module);
            $icontext = html_writer::link($url, $this->output->pix_icon(
                'icon', $modulename, 'mod_'.$module, array('class' => 'iconlarge')));
            if (get_string_manager()->string_exists("activityoverview", $module)) {
                $icontext .= get_string("activityoverview", $module);
            } else {
                $icontext .= get_string("activityoverview", 'block_lw_courses', $modulename);
            }

            // Add collapsible region with overview text in it.
            $output .= $this->collapsible_region($overview[$module], '', 'region_'.$cid.'_'.$module, $icontext, '', true);

            $output .= html_writer::end_tag('div');
        }
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Constructs header in editing mode
     *
     * @param int $max maximum number of courses
     * @return string html of header bar.
     */
    public function editing_bar_head($max = 0) {
        $output = $this->output->box_start('notice');

        $options = array('0' => get_string('alwaysshowall', 'block_lw_courses'));
        for ($i = 1; $i <= $max; $i++) {
            $options[$i] = $i;
        }
        $url = new moodle_url('/my/index.php');
        $select = new single_select($url, 'mynumber', $options, block_lw_courses_get_max_user_courses(), array());
        $select->set_label(get_string('numtodisplay', 'block_lw_courses'));
        $output .= $this->output->render($select);

        $output .= $this->output->box_end();
        return $output;
    }

    /**
     * Show hidden courses count
     *
     * @param int $total count of hidden courses
     * @return string html
     */
    public function hidden_courses($total) {
        if ($total <= 0) {
            return;
        }
        $output = $this->output->box_start('notice');
        $plural = $total > 1 ? 'plural' : '';
        $config = get_config('block_lw_courses');
        // Show view all course link to user if forcedefaultmaxcourses is not empty.
        if (!empty($config->forcedefaultmaxcourses)) {
            $output .= get_string('hiddencoursecount'.$plural, 'block_lw_courses', $total);
        } else {
            $a = new stdClass();
            $a->coursecount = $total;
            $a->showalllink = html_writer::link(new moodle_url('/my/index.php',
                array('mynumber' => block_lw_courses::SHOW_ALL_COURSES)),
                get_string('showallcourses'));
            $output .= get_string('hiddencoursecountwithshowall'.$plural, 'block_lw_courses', $a);
        }

        $output .= $this->output->box_end();
        return $output;
    }

    /**
     * Creates collapsable region
     *
     * @param string $contents existing contents
     * @param string $classes class names added to the div that is output.
     * @param string $id id added to the div that is output. Must not be blank.
     * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
     * @param string $userpref the name of the user preference that stores the user's preferred default state.
     *      (May be blank if you do not wish the state to be persisted.
     * @param bool $default Initial collapsed state to use if the user_preference it not set.
     * @return bool if true, return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region($contents, $classes, $id, $caption, $userpref = '', $default = false) {
        $output  = $this->collapsible_region_start($classes, $id, $caption, $userpref, $default);
        $output .= $contents;
        $output .= $this->collapsible_region_end();

        return $output;
    }

    /**
     * Print (or return) the start of a collapsible region, that has a caption that can
     * be clicked to expand or collapse the region. If JavaScript is off, then the region
     * will always be expanded.
     *
     * @param string $classes class names added to the div that is output.
     * @param string $id id added to the div that is output. Must not be blank.
     * @param string $caption text displayed at the top. Clicking on this will cause the region to expand or contract.
     * @param string $userpref the name of the user preference that stores the user's preferred default state.
     *      (May be blank if you do not wish the state to be persisted.
     * @param bool $default Initial collapsed state to use if the user_preference it not set.
     * @return bool if true, return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region_start($classes, $id, $caption, $userpref = '', $default = false) {
        // Work out the initial state.
        if (!empty($userpref) and is_string($userpref)) {
            user_preference_allow_ajax_update($userpref, PARAM_BOOL);
            $collapsed = get_user_preferences($userpref, $default);
        } else {
            $collapsed = $default;
            $userpref = false;
        }

        if ($collapsed) {
            $classes .= ' collapsed';
        }

        $output = '';
        $output .= '<div id="' . $id . '" class="collapsibleregion ' . $classes . '">';
        $output .= '<div id="' . $id . '_sizer">';
        $output .= '<div id="' . $id . '_caption" class="collapsibleregioncaption">';
        $output .= $caption . ' ';
        $output .= '</div><div id="' . $id . '_inner" class="collapsibleregioninner">';
        $this->page->requires->js_init_call('M.block_lw_courses.collapsible', array($id, $userpref, get_string('clicktohideshow')));

        return $output;
    }

    /**
     * Close a region started with print_collapsible_region_start.
     *
     * @return string return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region_end() {
        $output = '</div></div></div>';
        return $output;
    }

    /**
     * Creates html for welcome area
     *
     * @param int $msgcount number of messages
     * @return string html string for welcome area.
     */
    public function welcome_area($msgcount) {
        global $CFG, $USER;
        $output = $this->output->box_start('welcome_area');

        $picture = $this->output->user_picture($USER, array('size' => 75, 'class' => 'welcome_userpicture'));
        $output .= html_writer::tag('div', $picture, array('class' => 'profilepicture'));

        $output .= $this->output->box_start('welcome_message');
        $output .= $this->output->heading(get_string('welcome', 'block_lw_courses', $USER->firstname));

        if (!empty($CFG->messaging)) {
            $plural = 's';
            if ($msgcount > 0) {
                $output .= get_string('youhavemessages', 'block_lw_courses', $msgcount);
                if ($msgcount == 1) {
                    $plural = '';
                }
            } else {
                $output .= get_string('youhavenomessages', 'block_lw_courses');
            }
            $output .= html_writer::link(new moodle_url('/message/index.php'),
                get_string('message'.$plural, 'block_lw_courses'));
        }
        $output .= $this->output->box_end();
        $output .= $this->output->box('', 'flush');
        $output .= $this->output->box_end();

        return $output;
    }

    // Custom LearningWorks functions.

    /**
     * Get the image for a course if it exists
     *
     * @param object $course The course whose image we want
     * @return string|void
     */
    public function course_image($course) {
        global $CFG;

        $course = new core_course_list_element($course);
        // Check to see if a file has been set on the course level.
        if ($course->id > 0 && $course->get_course_overviewfiles()) {
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if ($isimage) {
                    $config = get_config('block_lw_courses');
                    if (is_null($config->lw_courses_bgimage) ||
                         $config->lw_courses_bgimage == BLOCKS_LW_COURSES_IMAGEASBACKGROUND_FALSE) {
                        // Embed the image url as a img tag sweet...
                        $image = html_writer::empty_tag('img', array('src' => $url, 'class' => 'course_image'));
                        return html_writer::div($image, 'image_wrap');
                    } else {
                        // We need a CSS soloution apparently lets give it to em.
                        return html_writer::div('', 'course_image_embed',
                            array("style" => 'background-image:url('.$url.'); background-size:cover'));
                    }
                } else {
                    return $this->course_image_defaults();
                }
            }
        } else {
            // Lets try to find some default images eh?.
            return $this->course_image_defaults();
        }
        // Where are the default at even?.
        return print_error('error');
    }

    /**
     * There was no image for a course give a default
     *
     * @return string|void
     */
    public function course_image_defaults() {

        $config = get_config('block_lw_courses');

        if (method_exists($this->output, 'image_url')) {
            // Use the new method.
            $default = $this->output->image_url('default', 'block_lw_courses');
        } else {
            // Still a pre Moodle 3.3 release. Use pix_url because image_url doesn't exist yet.
            $default = $this->output->pix_url('default', 'block_lw_courses');
        }
        if ($courseimagedefault = get_config('block_lw_courses', 'courseimagedefault')) {

            // Return an img element with the image in the block settings to use for the course.
            $imageurl = block_lw_courses_get_course_image_url($courseimagedefault);
        } else {
            // We check for a default image in the lw_courses pix folder named default aka our final hope.
            $imageurl = $default;
        }

        // Do we need a CSS soloution or is a img good enough?.
        if (is_null($config->lw_courses_bgimage) || $config->lw_courses_bgimage == BLOCKS_LW_COURSES_IMAGEASBACKGROUND_FALSE) {
            // Embed the image url as a img tag sweet...
            $image = html_writer::empty_tag('img', array( 'src' => $imageurl, 'class' => 'course_image' ));
            return html_writer::div($image, 'image_wrap');
        } else {
            // We need a CSS solution apparently lets give it to 'em.
            return html_writer::div('', 'course_image_embed',
                    array("style" => 'background-image:url('.$imageurl.'); background-size:cover'));
        }
        // Where are the default at even?.
        return print_error('filenotreadable');
    }

    /**
     * Get the Course description for a given course
     *
     * @param object $course The course whose description we want
     * @return string
     */
    public function course_description($course) {
        $course = new core_course_list_element($course);

        $context = \context_course::instance($course->id);
        $summary = external_format_string($course->summary, $context,
                1, array());
        return html_writer::div($summary, 'course_description');
    }

    /**
     * Cut off the course description at a certain point
     *
     * @param string $s Initial String passed in
     * @param int $l The length to cut it too
     * @param string $e I am unsure
     * @return string
     */
    public function truncate_html($s, $l, $e = '&hellip;') {
        $s = trim($s);
        $e = (strlen(strip_tags($s)) > $l) ? $e : '';
        $i = 0;
        $tags = array();

        preg_match_all('/<[^>]+>([^<]*)/', $s, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        foreach ($m as $o) {
            if ($o[0][1] - $i >= $l) {
                break;
            }
            $t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
            if ($t[0] != '/') {
                $tags[] = $t;
            } else if (end($tags) == substr($t, 1)) {
                array_pop($tags);
            }
            $i += $o[1][1] - $o[0][1];
        }

        $output = substr($s, 0, $l = min(strlen($s), $l + $i)) .
            $e . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');
        return $output;
    }
}
