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
 * Site main menu block.
 *
 * @package    block_site_main_menu
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_site_main_menu extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_site_main_menu');
    }

    function applicable_formats() {
        return array('site' => true);
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

        require_once($CFG->dirroot . '/course/lib.php');

        $course = get_site();
        $format = course_get_format($course);
        $courserenderer = $format->get_renderer($this->page);

        $context = context_course::instance($course->id);
        $isediting = $this->page->user_is_editing() && has_capability('moodle/course:manageactivities', $context);

        // Output classes.
        $cmnameclass = $format->get_output_classname('content\\cm\\cmname');
        $controlmenuclass = $format->get_output_classname('content\\cm\\controlmenu');

        $badgeattributes = [
            'class' => 'badge badge-pill badge-warning mt-2',
            'data-region' => 'visibility'
        ];

        // Extra fast view mode.
        if (!$isediting) {
            $modinfo = get_fast_modinfo($course);
            if (!empty($modinfo->sections[0])) {
                foreach($modinfo->sections[0] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    if (!$cm->uservisible || !$cm->is_visible_on_course_page()) {
                        continue;
                    }

                    if ($cm->indent > 0) {
                        $indent = '<div class="mod-indent mod-indent-'.$cm->indent.'"></div>';
                    } else {
                        $indent = '';
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
                            $indent . $cm->get_formatted_content(['overflowdiv' => true, 'noclean' => true]),
                            'activity-basis d-flex align-items-center');
                        $content = html_writer::div(
                            $activitybasis . $badges,
                            'contentwithoutlink activity-item activity',
                            ['data-activityname' => $cm->name]
                        );
                    } else {
                        $cmname = new $cmnameclass($format, $cm->get_section_info(), $cm);
                        $activitybasis = html_writer::div(
                            $indent . $courserenderer->render($cmname),
                            'activity-basis d-flex align-items-center');
                        $content = html_writer::div(
                            $activitybasis . $badges,
                            'activity-item activity',
                            ['data-activityname' => $cm->name]
                        );
                    }

                    $this->content->items[] = html_writer::div($content, 'main-menu-content section');
                }
            }
            return $this->content;
        }

        // Slow & hacky editing mode.
        $ismoving = ismoving($course->id);
        course_create_sections_if_missing($course, 0);
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(0);

        if ($ismoving) {
            $strmovefull = strip_tags(get_string('movefull', '', "'$USER->activitycopyname'"));
            $strcancel= get_string('cancel');
        } else {
            $strmove = get_string('move');
        }

        if ($ismoving) {
            $this->content->icons[] = $OUTPUT->pix_icon('t/move', get_string('move'));
            $this->content->items[] = $USER->activitycopyname.'&nbsp;(<a href="'.$CFG->wwwroot.'/course/mod.php?cancelcopy=true&amp;sesskey='.sesskey().'">'.$strcancel.'</a>)';
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
                        $mod
                    );

                    $menu = $controlmenu->get_action_menu($OUTPUT);

                    $moveaction = html_writer::link(
                        new moodle_url('/course/mod.php', ['sesskey' => sesskey(), 'copy' => $mod->id]),
                        $OUTPUT->pix_icon('i/dragdrop', $strmove),
                        ['class' => 'editing_move_activity']
                    );

                    $editbuttons = html_writer::tag(
                        'div',
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

                    if ($mod->indent > 0) {
                        $indent = '<div class="mod-indent mod-indent-'.$mod->indent.'"></div>';
                    } else {
                        $indent = '';
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
                            $moveaction .
                            $indent .
                            $mod->get_formatted_content(['overflowdiv' => true, 'noclean' => true]) .
                            $editbuttons,
                            'activity-basis d-flex align-items-center');
                        $content = html_writer::div(
                            $activitybasis . $badges,
                            'contentwithoutlink activity-item activity',
                            ['data-activityname' => $mod->name]
                        );
                    } else {
                        $cmname = new $cmnameclass($format, $mod->get_section_info(), $mod);
                        $activitybasis = html_writer::div(
                            $moveaction .
                            $indent .
                            $courserenderer->render($cmname) .
                            $editbuttons,
                            'activity-basis d-flex align-items-center');
                        $content = html_writer::div(
                            $activitybasis . $badges,
                            'activity-item activity',
                            ['data-activityname' => $mod->name]
                        );
                    }
                    $this->content->items[] = html_writer::div($content, 'main-menu-content');
                }
            }
        }

        if ($ismoving) {
            $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
            $this->content->items[] = html_writer::link($movingurl, '', array('title' => $strmovefull, 'class' => 'movehere'));
            $this->content->icons[] = '';
        }

        if ($this->page->course->id === SITEID) {
            $this->content->footer = $courserenderer->course_section_add_cm_control($course,
                0, null, array('inblock' => true));
        }
        return $this->content;
    }
}
