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

namespace theme_adaptable\traits;

defined('MOODLE_INTERNAL') || die();

use html_writer;
use context_course;
use completion_info;
use core_text;
use url_select;

// Load libraries.
require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->libdir.'/coursecatlib.php');
require_once($CFG->dirroot.'/message/lib.php');
require_once($CFG->dirroot.'/course/format/topics/renderer.php');
require_once($CFG->dirroot.'/course/format/weeks/renderer.php');

trait single_section_page {
    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course entry from DB.
     * @param array $sections (argument not used).
     * @param array $mods (argument not used).
     * @param array $modnames (argument not used).
     * @param array $modnamesused (argument not used).
     * @param int $displaysection The section number in the course which is being displayed.
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        $this->print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
    }

    /**
     * Return the number of sections for a given course.
     *
     * The parameter numsections is removed from course formats as of Moodle version
     * 3.3.  This function assists with retrieving this information. See Moodle Tracker issue
     * MDL-57769 for further information. In this theme, references to $course->numsections
     * have been replaced by calls calls to this function to get the information.
     * Also see https://bitbucket.org/covuni/moodle-theme_adaptable/pull-requests/43/fixes-renderersphp-for-the-missing/diff.
     *
     * @return int Number of sections.
     */
    public function get_num_sections($course) {

        global $DB;
        $numsections = $DB->count_records('course_sections', array('course' => $course->id)) - 1;
        return $numsections;
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course entry from DB.
     * @param array    $sections (argument not used).
     * @param array    $mods (argument not used).
     * @param array    $modnames (argument not used).
     * @param array    $modnamesused (argument not used).
     * @param int      $displaysection The section number in the course which is being displayed.
     * @param boolean  $showsectionzero states if section zero is to be shown at the top of the section.
     */
    protected function print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection,
            $showsectionzero = 1) {
        global $PAGE;

        // Build, on the fly, 'numsections' property (see Moodle's Tracker issue MDL-57769 for details).
        global $DB;
        $course->numsections = $DB->count_records('course_sections', array('course' => $course->id)) - 1;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection, $course->id);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        if ($showsectionzero) {
            $thissection = $modinfo->get_section_info(0);
            if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));

        // Title attributes.
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections.
        echo $this->start_section_list();

        if (!$showsectionzero) {
            echo $this->section_header_onsectionpage_topic0notattop($thissection, $course);
        } else {
            echo $this->section_header($thissection, $course, true, $displaysection);
        }
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('nav', array('class' => 'section_footer'));
        $sectionbottomnav .= $sectionnavlinks['previous'];
        $sectionbottomnav .= $sectionnavlinks['next'];
        $sectionbottomnav .= html_writer::tag('div', '', array('class' => 'clearfix'));
        $sectionbottomnav .= html_writer::end_tag('nav');
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
                array('class' => 'jumpnav'));
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        return $this->section_nav_selection_content($course, $sections, $displaysection);
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param $displaysection the current displayed section number.
     * @param int $section Section number to start on.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection_content($course, $sections, $displaysection, $section = 1) {
        global $CFG;
        $o = '';
        $sectionmenu = array();
        $sectionmenu[course_get_url($course)->out(false)] = get_string('maincoursepage', 'theme_adaptable');
        $modinfo = get_fast_modinfo($course);

        // Get 'numsections' property (see Moodle's Tracker issue MDL-57769 for details).
        // Also see https://bitbucket.org/covuni/moodle-theme_adaptable/issues/728/fixes-renderersphp-for-the-missing.
        $numsections = $this->get_num_sections($course);
        while ($section <= $numsections) {
            $thissection = $modinfo->get_section_info($section);
            $showsection = $thissection->uservisible or !$course->hiddensections;
            if (($showsection) && ($section != $displaysection) && ($url = course_get_url($course, $section))) {
                $sectionmenu[$url->out(false)] = $this->shorten_string(get_section_name($course, $section));
            }
            $section++;
        }

        $select = new url_select($sectionmenu, '', array('' => get_string('jumpto', 'theme_adaptable')));
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    private function shorten_string($string, $ellipsis = '..') {
        $maxlen = 50;
        $string = strip_tags($string);
        $boundary = $maxlen - strlen($ellipsis);
        if ((core_text::strlen($string) > $maxlen)) {
            $shortstring = core_text::substr($string, 0, $boundary) . $ellipsis;
        } else {
            $shortstring = $string;
        }
        return $shortstring;
    }

    /**
     * Generate next/previous section links for naviation.
     *
     * @param stdClass $course The course entry from DB.
     * @param array $sections The course_sections entries from the DB.
     * @param int $sectionno The section number in the coruse which is being displayed.
     * @return array associative array with previous and next section link.
     */
    protected function get_nav_links($course, $sections, $sectionno) {
        return $this->get_nav_links_content($course, $sections, $sectionno);
    }

    /**
     * Generate next/previous section links for naviation.
     *
     * @param stdClass $course The course entry from DB.
     * @param array $sections The course_sections entries from the DB.
     * @param int $sectionno The section number in the coruse which is being displayed.
     * @param int $buffer Control the navigation items for when section 0 is in the grid in the Grid format.
     * @return array associative array with previous and next section link.
     */
    protected function get_nav_links_content($course, $sections, $sectionno, $buffer = 0) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => html_writer::tag('div', '', array('class' => 'previous_section prevnext')),
                'next' => html_writer::tag('div', '', array('class' => 'next_section prevnext')));
        $back = $sectionno - 1;

        $hasprev = $hasnext = false;
        while ($back > $buffer && !$hasprev) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array();
                $params['class'] = 'previous_section prevnext';
                if (!$sections[$back]->visible) {
                    $params['class'] = 'previous_section prevnext dimmed_text';
                }
                $previouslink = html_writer::tag('span', '<i class="fa fa-angle-double-left"></i>', array('class' => 'nav_icon'));
                $sectionname = html_writer::tag('span', get_string('previoussection', 'theme_adaptable'),
                        array('class' => 'nav_guide')) . '<br>';
                        $sectionname .= get_section_name($course, $sections[$back]);
                        $previouslink .= html_writer::tag('span', $sectionname, array('class' => 'text'));
                        $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
                        $hasprev = true;
            }
            $back--;
        }

        $forward = $sectionno + 1;

        // Get 'numsections' property (see Moodle's Tracker issue MDL-57769 for details).
        // Also see https://bitbucket.org/covuni/moodle-theme_adaptable/issues/728/fixes-renderersphp-for-the-missing.
        $numsections = $this->get_num_sections($course);
        while ($forward <= $numsections && !$hasnext) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array();
                $params['class'] = 'next_section prevnext';
                if (!$sections[$forward]->visible) {
                    $params['class'] = 'next_section prevnext dimmed_text';
                }

                $sectionname = html_writer::tag('span', get_string('nextsection', 'theme_adaptable'),
                        array('class' => 'nav_guide')) . '<br>';
                $sectionname .= get_section_name($course, $sections[$forward]);
                $nextlink = html_writer::tag('span', $sectionname, array('class' => 'text'));
                $nextlink .= html_writer::tag('span', '<i class="fa fa-angle-double-right"></i>', array('class' => 'nav_icon'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
                $hasnext = true;
            }
            $forward++;
        }

        return $links;
    }
}

