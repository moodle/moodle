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
 * Social activities block.
 *
 * @package    block_social_activities
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_social_activities extends block_list {
    function init(){
        $this->title = get_string('pluginname', 'block_social_activities');
    }

    function applicable_formats() {
        return array('course-view-social' => true);
    }

    function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $course = $this->page->course;
        $format = course_get_format($course);
        $courserenderer = $format->get_renderer($this->page);

        require_once($CFG->dirroot.'/course/lib.php');

        $context = context_course::instance($course->id);
        $isediting = $this->page->user_is_editing() && has_capability('moodle/course:manageactivities', $context);
        $modinfo = get_fast_modinfo($course);

        // Output classes.
        $cmnameclass = $format->get_output_classname('content\\cm\\cmname');
        $controlmenuclass = $format->get_output_classname('content\\cm\\controlmenu');

        $badgeattributes = [
            'class' => 'badge rounded-pill bg-warning text-dark mt-2',
            'data-region' => 'visibility'
        ];

        // Extra fast view mode.
        if (!$isediting) {
            if (!empty($modinfo->sections[0])) {
                foreach($modinfo->sections[0] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    if (!$cm->uservisible || !$cm->is_visible_on_course_page()) {
                        continue;
                    }

                    $badges = '';
                    if (!$cm->visible) {
                        $badges = html_writer::tag(
                            'span',
                            get_string('hiddenfromstudents'),
                            $badgeattributes
                        );
                    }

                    if ($cm->is_stealth()) {
                        $badges = html_writer::tag(
                            'span',
                            get_string('hiddenoncoursepage'),
                            $badgeattributes
                        );
                    }

                    if (!$cm->url) {
                        $activitybasis = html_writer::div(
                            $cm->get_formatted_content(['overflowdiv' => true, 'noclean' => true]),
                            'activity-basis d-flex align-items-center'
                        );
                        $content = html_writer::div(
                            $activitybasis . $badges,
                            'contentwithoutlink activity-item activity',
                            ['data-activityname' => $cm->name]
                        );
                        $this->content->items[] = $content;
                        $this->content->icons[] = '';
                    } else {
                        $cmname = new $cmnameclass($format, $cm->get_section_info(), $cm);
                        $activitybasis = html_writer::div(
                            $courserenderer->render($cmname),
                            'activity-basis d-flex align-items-center');
                        $content = html_writer::div(
                            $activitybasis . $badges,
                            'activity-item activity',
                            ['data-activityname' => $cm->name]
                        );
                        $this->content->items[] = $content;
                    }
                }
            }
            return $this->content;
        }

        // Slow & hacky editing mode.
        $ismoving = ismoving($course->id);
        $section = $modinfo->get_section_info(0);

        if ($ismoving) {
            $strmovefull = strip_tags(get_string('movefull', '', "'$USER->activitycopyname'"));
            $strcancel= get_string('cancel');
        } else {
            $strmove = get_string('move');
        }

        if ($ismoving) {
            $this->content->icons[] = '&nbsp;' . $OUTPUT->pix_icon('t/move', get_string('move'));
            $cancelurl = new moodle_url('/course/mod.php', array('cancelcopy' => 'true', 'sesskey' => sesskey()));
            $this->content->items[] = $USER->activitycopyname . '&nbsp;(<a href="' . $cancelurl . '">' . $strcancel . '</a>)';
        }

        if (!empty($modinfo->sections[0])) {
            foreach ($modinfo->sections[0] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];
                if (!$mod->uservisible || !$mod->is_visible_on_course_page()) {
                    continue;
                }
                if (!$ismoving) {

                    $controlmenu = new $controlmenuclass(
                        $format,
                        $mod->get_section_info(),
                        $mod,
                        ['disableindentation' => true]
                    );

                    $menu = $controlmenu->get_action_menu($OUTPUT);

                    // Add a move primary action.
                    $moveaction = html_writer::link(
                        new moodle_url('/course/mod.php', ['sesskey' => sesskey(), 'copy' => $mod->id]),
                        $OUTPUT->pix_icon('i/dragdrop', $strmove),
                        ['class' => 'editing_move_activity']
                    );

                    $editbuttons = html_writer::tag('div',
                        $courserenderer->render($controlmenu),
                        ['class' => 'buttons activity-actions ml-auto']
                    );
                } else {
                    $editbuttons = '';
                    $moveaction = '';
                }
                if ($mod->visible || has_capability('moodle/course:viewhiddenactivities', $mod->context)) {
                    if ($ismoving) {
                        if ($mod->id == $USER->activitycopy) {
                            continue;
                        }
                        $movingurl = new moodle_url('/course/mod.php', array('moveto' => $mod->id, 'sesskey' => sesskey()));
                        $this->content->items[] = html_writer::link($movingurl, '', array('title' => $strmovefull,
                            'class' => 'movehere'));
                        $this->content->icons[] = '';
                    }

                    $badges = '';
                    if (!$mod->visible) {
                        $badges = html_writer::tag(
                            'span',
                            get_string('hiddenfromstudents'),
                            $badgeattributes
                        );
                    }

                    if ($mod->is_stealth()) {
                        $badges = html_writer::tag(
                            'span',
                            get_string('hiddenoncoursepage'),
                            $badgeattributes
                        );
                    }

                    if (!$mod->url) {
                        $activitybasis = html_writer::div(
                            $mod->get_formatted_content(['overflowdiv' => true, 'noclean' => true]) .
                            $editbuttons,
                            'activity-basis d-flex align-items-center');
                        $content = html_writer::div(
                            $moveaction .
                            $activitybasis .
                            $badges,
                            'contentwithoutlink activity-item activity',
                            ['data-activityname' => $mod->name]
                        );
                        $this->content->items[] = $content;
                        $this->content->icons[] = '';
                    } else {
                        $cmname = new $cmnameclass($format, $mod->get_section_info(), $mod);
                        $activitybasis = html_writer::div(
                            $courserenderer->render($cmname) .
                            $editbuttons,
                            'activity-basis d-flex align-items-center');
                        $content = html_writer::div(
                            $moveaction .
                            $activitybasis .
                            $badges,
                            'activity-item activity',
                            ['data-activityname' => $mod->name]
                        );
                        $this->content->items[] = $content;
                    }
                }
            }
        }

        if ($ismoving) {
            $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
            $this->content->items[] = html_writer::link($movingurl, '', array('title' => $strmovefull, 'class' => 'movehere'));
            $this->content->icons[] = '';
        }

        $this->content->footer = $courserenderer->course_section_add_cm_control($course,
                0, null, array('inblock' => true));

        return $this->content;
    }
}
