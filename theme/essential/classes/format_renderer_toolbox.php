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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_essential;

defined('MOODLE_INTERNAL') || die;

trait format_renderer_toolbox {
    public function get_nav_links($course, $sections, $sectionno) {
        return $this->get_nav_links_content($course, $sections, $sectionno);
    }

    // Implemented this way to avoid duplication as reported by 'PHP Copy/Paste Detector - phpcpd'.
    protected function get_nav_links_content($course, $sections, $sectionno, $buffer = 0) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = \course_get_format($course)->get_course();
        $left = 'left';
        $right = 'right';
        if (\right_to_left()) {
            $temp = $left;
            $left = $right;
            $right = $temp;
        }
        $previousarrow = '<span aria-hidden="true" class="fa fa-chevron-circle-' . $left . '"></span>';
        $nextarrow = '<span aria-hidden="true" class="fa fa-chevron-circle-' . $right . '"></span>';
        $canviewhidden = \has_capability('moodle/course:viewhiddensections', \context_course::instance($course->id))
                or ! $course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > $buffer and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array('id' => 'previous_section');
                if (!$sections[$back]->visible) {
                    $params['class'] = 'dimmed_text';
                }
                $previouslink = \html_writer::start_tag('div', array('class' => 'nav_icon'));
                $previouslink .= $previousarrow;
                $previouslink .= \html_writer::end_tag('div');
                $previouslink .= \html_writer::start_tag('span', array('class' => 'text'));
                $previouslink .= \html_writer::start_tag('span', array('class' => 'nav_guide'));
                $previouslink .= \get_string('previoussection', 'theme_essential');
                $previouslink .= \html_writer::end_tag('span');
                $previouslink .= \html_writer::empty_tag('br');
                $previouslink .= \get_section_name($course, $sections[$back]);
                $previouslink .= \html_writer::end_tag('span');
                $links['previous'] = \html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        $numsections = course_get_format($course)->get_last_section_number();
        while ($forward <= $numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array('id' => 'next_section');
                if (!$sections[$forward]->visible) {
                    $params['class'] = 'dimmed_text';
                }
                $nextlink = \html_writer::start_tag('div', array('class' => 'nav_icon'));
                $nextlink .= $nextarrow;
                $nextlink .= \html_writer::end_tag('div');
                $nextlink .= \html_writer::start_tag('span', array('class' => 'text'));
                $nextlink .= \html_writer::start_tag('span', array('class' => 'nav_guide'));
                $nextlink .= \get_string('nextsection', 'theme_essential');
                $nextlink .= \html_writer::end_tag('span');
                $nextlink .= \html_writer::empty_tag('br');
                $nextlink .= \get_section_name($course, $sections[$forward]);
                $nextlink .= \html_writer::end_tag('span');
                $links['next'] = \html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        $this->print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
    }

    protected function print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection,
        $showsectionzero = 1) {
        global $PAGE;

        $modinfo = \get_fast_modinfo($course);
        $course = \course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return false;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return false;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        if ($showsectionzero) {
            $thissection = $modinfo->get_section_info(0);
            if ($thissection->summary or ! empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div.
        echo \html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);

        // Construct navigation links.
        $sectionnav = \html_writer::start_tag('nav', array('class' => 'section-navigation'));
        $sectionnav .= $sectionnavlinks['previous'];
        $sectionnav .= $sectionnavlinks['next'];
        $sectionnav .= \html_writer::empty_tag('br', array('style' => 'clear:both'));
        $sectionnav .= \html_writer::end_tag('nav');
        $sectionnav .= \html_writer::tag('div', '', array('class' => 'bor'));

        // Output Section Navigation.
        echo $sectionnav;

        // Define the Section Title.
        $sectiontitle = '';
        $sectiontitle .= \html_writer::start_tag('div', array('class' => 'section-title'));
        // Title attributes.
        $titleattr = 'title';
        if (!$thissection->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= \html_writer::start_tag('h3', array('class' => $titleattr));
        $sectiontitle .= \get_section_name($course, $displaysection);
        $sectiontitle .= \html_writer::end_tag('h3');
        $sectiontitle .= \html_writer::end_tag('div');

        // Output the Section Title.
        echo $sectiontitle;

        // Now the list of sections.
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);

        // Show completion help icon.
        $completioninfo = new \completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Close single-section div.
        echo \html_writer::end_tag('div');
    }
}