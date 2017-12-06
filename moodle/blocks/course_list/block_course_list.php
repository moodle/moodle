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
 * Course list block.
 *
 * @package    block_course_list
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once($CFG->dirroot . '/course/lib.php');
include_once($CFG->libdir . '/coursecatlib.php');

class block_course_list extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_course_list');
    }

    function has_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $icon = $OUTPUT->pix_icon('i/course', get_string('course'));

        $adminseesall = true;
        if (isset($CFG->block_course_list_adminview)) {
           if ( $CFG->block_course_list_adminview == 'own'){
               $adminseesall = false;
           }
        }

        if (empty($CFG->disablemycourses) and isloggedin() and !isguestuser() and
          !(has_capability('moodle/course:update', context_system::instance()) and $adminseesall)) {    // Just print My Courses
            // As this is producing navigation sort order should default to $CFG->navsortmycoursessort instead
            // of using the default.
            if (!empty($CFG->navsortmycoursessort)) {
                $sortorder = 'visible DESC, ' . $CFG->navsortmycoursessort . ' ASC';
            } else {
                $sortorder = 'visible DESC, sortorder ASC';
            }
            if ($courses = enrol_get_my_courses(NULL, $sortorder)) {
                foreach ($courses as $course) {
                    $coursecontext = context_course::instance($course->id);
                    $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
                    $this->content->items[]="<a $linkcss title=\"" . format_string($course->shortname, true, array('context' => $coursecontext)) . "\" ".
                               "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".$icon.format_string(get_course_display_name_for_list($course)). "</a>";
                }
                $this->title = get_string('mycourses');
            /// If we can update any course of the view all isn't hidden, show the view all courses link
                if (has_capability('moodle/course:update', context_system::instance()) || empty($CFG->block_course_list_hideallcourseslink)) {
                    $this->content->footer = "<a href=\"$CFG->wwwroot/course/index.php\">".get_string("fulllistofcourses")."</a> ...";
                }
            }
            $this->get_remote_courses();
            if ($this->content->items) { // make sure we don't return an empty list
                return $this->content;
            }
        }

        $categories = coursecat::get(0)->get_children();  // Parent = 0   ie top-level categories only
        if ($categories) {   //Check we have categories
            if (count($categories) > 1 || (count($categories) == 1 && $DB->count_records('course') > 200)) {     // Just print top level category links
                foreach ($categories as $category) {
                    $categoryname = $category->get_formatted_name();
                    $linkcss = $category->visible ? "" : " class=\"dimmed\" ";
                    $this->content->items[]="<a $linkcss href=\"$CFG->wwwroot/course/index.php?categoryid=$category->id\">".$icon . $categoryname . "</a>";
                }
            /// If we can update any course of the view all isn't hidden, show the view all courses link
                if (has_capability('moodle/course:update', context_system::instance()) || empty($CFG->block_course_list_hideallcourseslink)) {
                    $this->content->footer .= "<a href=\"$CFG->wwwroot/course/index.php\">".get_string('fulllistofcourses').'</a> ...';
                }
                $this->title = get_string('categories');
            } else {                          // Just print course names of single category
                $category = array_shift($categories);
                $courses = get_courses($category->id);

                if ($courses) {
                    foreach ($courses as $course) {
                        $coursecontext = context_course::instance($course->id);
                        $linkcss = $course->visible ? "" : " class=\"dimmed\" ";

                        $this->content->items[]="<a $linkcss title=\""
                                   . format_string($course->shortname, true, array('context' => $coursecontext))."\" ".
                                   "href=\"$CFG->wwwroot/course/view.php?id=$course->id\">"
                                   .$icon. format_string(get_course_display_name_for_list($course), true, array('context' => context_course::instance($course->id))) . "</a>";
                    }
                /// If we can update any course of the view all isn't hidden, show the view all courses link
                    if (has_capability('moodle/course:update', context_system::instance()) || empty($CFG->block_course_list_hideallcourseslink)) {
                        $this->content->footer .= "<a href=\"$CFG->wwwroot/course/index.php\">".get_string('fulllistofcourses').'</a> ...';
                    }
                    $this->get_remote_courses();
                } else {

                    $this->content->icons[] = '';
                    $this->content->items[] = get_string('nocoursesyet');
                    if (has_capability('moodle/course:create', context_coursecat::instance($category->id))) {
                        $this->content->footer = '<a href="'.$CFG->wwwroot.'/course/edit.php?category='.$category->id.'">'.get_string("addnewcourse").'</a> ...';
                    }
                    $this->get_remote_courses();
                }
                $this->title = get_string('courses');
            }
        }

        return $this->content;
    }

    function get_remote_courses() {
        global $CFG, $USER, $OUTPUT;

        if (!is_enabled_auth('mnet')) {
            // no need to query anything remote related
            return;
        }

        $icon = $OUTPUT->pix_icon('i/mnethost', get_string('host', 'mnet'));

        // shortcut - the rest is only for logged in users!
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if ($courses = get_my_remotecourses()) {
            $this->content->items[] = get_string('remotecourses','mnet');
            $this->content->icons[] = '';
            foreach ($courses as $course) {
                $this->content->items[]="<a title=\"" . format_string($course->shortname, true) . "\" ".
                    "href=\"{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}\">"
                    .$icon. format_string(get_course_display_name_for_list($course)) . "</a>";
            }
            // if we listed courses, we are done
            return true;
        }

        if ($hosts = get_my_remotehosts()) {
            $this->content->items[] = get_string('remotehosts', 'mnet');
            $this->content->icons[] = '';
            foreach($USER->mnet_foreign_host_array as $somehost) {
                $this->content->items[] = $somehost['count'].get_string('courseson','mnet').'<a title="'.$somehost['name'].'" href="'.$somehost['url'].'">'.$icon.$somehost['name'].'</a>';
            }
            // if we listed hosts, done
            return true;
        }

        return false;
    }

    /**
     * Returns the role that best describes the course list block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'navigation';
    }
}


