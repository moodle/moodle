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

        $course = get_site();
        require_once($CFG->dirroot.'/course/lib.php');
        $context = context_course::instance($course->id);
        $isediting = $this->page->user_is_editing() && has_capability('moodle/course:manageactivities', $context);
        $courserenderer = $this->page->get_renderer('core', 'course');

/// extra fast view mode
        if (!$isediting) {
            $modinfo = get_fast_modinfo($course);
            if (!empty($modinfo->sections[0])) {
                foreach($modinfo->sections[0] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    if (!$cm->uservisible) {
                        continue;
                    }

                    if ($cm->indent > 0) {
                        $indent = '<div class="mod-indent mod-indent-'.$cm->indent.'"></div>';
                    } else {
                        $indent = '';
                    }

                    if (!empty($cm->url)) {
                        $content = html_writer::div($courserenderer->course_section_cm_name($cm), 'activity');
                    } else {
                        $content = $cm->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
                    }

                    $this->content->items[] = $indent . html_writer::div($content, 'main-menu-content');
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
            $strmovehere = get_string('movehere');
            $strmovefull = strip_tags(get_string('movefull', '', "'$USER->activitycopyname'"));
            $strcancel= get_string('cancel');
        } else {
            $strmove = get_string('move');
        }

        if ($ismoving) {
            $this->content->icons[] = '<img src="'.$OUTPUT->pix_url('t/move') . '" class="iconsmall" alt="" />';
            $this->content->items[] = $USER->activitycopyname.'&nbsp;(<a href="'.$CFG->wwwroot.'/course/mod.php?cancelcopy=true&amp;sesskey='.sesskey().'">'.$strcancel.'</a>)';
        }

        if (!empty($modinfo->sections[0])) {
            foreach ($modinfo->sections[0] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];
                if (!$mod->uservisible) {
                    continue;
                }
                if (!$ismoving) {
                    $actions = course_get_cm_edit_actions($mod, $mod->indent);

                    // Prepend list of actions with the 'move' action.
                    $actions = array('move' => new action_menu_link_primary(
                        new moodle_url('/course/mod.php', array('sesskey' => sesskey(), 'copy' => $mod->id)),
                        new pix_icon('t/move', $strmove, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                        $strmove
                    )) + $actions;

                    $editbuttons = html_writer::tag('div',
                        $courserenderer->course_section_cm_edit_actions($actions, $mod, array('donotenhance' => true)),
                        array('class' => 'buttons')
                    );
                } else {
                    $editbuttons = '';
                }
                if ($mod->visible || has_capability('moodle/course:viewhiddenactivities', $mod->context)) {
                    if ($ismoving) {
                        if ($mod->id == $USER->activitycopy) {
                            continue;
                        }
                        $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'&amp;sesskey='.sesskey().'">'.
                            '<img style="height:16px; width:80px; border:0px" src="'.$OUTPUT->pix_url('movehere') . '" alt="'.$strmovehere.'" /></a>';
                        $this->content->icons[] = '';
                    }
                    if ($mod->indent > 0) {
                        $indent = '<div class="mod-indent mod-indent-'.$mod->indent.'"></div>';
                    } else {
                        $indent = '';
                    }
                    if (!$mod->url) {
                        $content = $mod->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
                    } else {
                        $content = html_writer::div($courserenderer->course_section_cm_name($mod), ' activity');
                    }
                    $this->content->items[] = $indent . html_writer::div($content . $editbuttons, 'main-menu-content');
                }
            }
        }

        if ($ismoving) {
            $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?movetosection='.$section->id.'&amp;sesskey='.sesskey().'">'.
                                      '<img style="height:16px; width:80px; border:0px" src="'.$OUTPUT->pix_url('movehere') . '" alt="'.$strmovehere.'" /></a>';
            $this->content->icons[] = '';
        }

        $this->content->footer = $courserenderer->course_section_add_cm_control($course,
                0, null, array('inblock' => true));

        return $this->content;
    }
}


