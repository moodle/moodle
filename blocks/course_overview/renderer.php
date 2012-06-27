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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/message/lib.php');

class block_course_overview_renderer extends plugin_renderer_base {

    public function course_overview($courses, $overviews, $moving = 0) {
        global $OUTPUT;
        $html = '';
        $config = get_config('block_course_overview');

        $html .= html_writer::start_tag('div', array('id' => 'course_list'));
        $weight = 0;
        $keylist = array_keys($courses);
        if ($this->page->user_is_editing() && $moving && $keylist[0] != $moving) {
            $html .= $this->course_move_target($moving, $weight);
            $weight++;
        }
        foreach ($courses as $course) {
            $cursor = ajaxenabled() && !$moving ? ' cursor' : '';
            $html .= $OUTPUT->box_start('coursebox', "course-{$course->id}");
            $html .= html_writer::start_tag('div', array('class' => 'course_title'));
            if ($this->page->user_is_editing()) {
                // Move icon.
                $icon = ajaxenabled() ? 'i/move_2d' : 't/move';
                $control = array('url' => new moodle_url('/my/index.php', array('course_moveid' => $course->id)),
                    'icon' => $icon, 'caption' => get_string('move'));
                if (ajaxenabled()) {
                    $html .= html_writer::tag('div',
                        html_writer::empty_tag('img',
                            array('src' => $this->pix_url($control['icon'])->out(false),
                                'alt' => $control['caption'], 'class' => 'icon cursor',
                                'title' => $control['caption'])
                        ), array('class' => 'move')
                    );
                } else {
                    $html .= html_writer::tag('a',
                             html_writer::empty_tag('img',  array('src' => $this->pix_url($control['icon'])->out(false), 'alt' => $control['caption'])),
                                    array('class' => 'icon move','title' => $control['caption'], 'href' => $control['url']));
                }
            }
            $link = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), $course->fullname);
            $html .= $OUTPUT->heading($link, 2, 'title');
            $html .= $OUTPUT->box('', 'flush');
            $html .= html_writer::end_tag('div');

            if (isset($config->showchildren) && $config->showchildren) {
                //list children here
                if ($children = block_course_overview_get_child_shortnames($course->id)) {
                    $html .= html_writer::tag('span', $children, array('class' => 'coursechildren'));
                }
            }

            if (isset($overviews[$course->id])) {
                $html .= $this->activity_display($course->id, $overviews[$course->id]);
            }

            $html .= $OUTPUT->box('', 'flush');
            $html .= $OUTPUT->box_end();

            if ($this->page->user_is_editing() && $moving && $course->id != $moving) {
                //check if next is the course we're moving
                $okay = true;
                if (isset($keylist[$weight])) {
                    if ($courses[$keylist[$weight]]->id == $moving) {
                        $okay = false;
                    }
                }
                if ($okay) {
                    $html .= $this->course_move_target($moving, $weight);
                }
            }
            $weight++;
        }
        $html .= html_writer::end_tag('div');

        return $html;
    }

    protected function course_move_target($cid, $weight) {
        $url = new moodle_url('/blocks/course_overview/move.php', array('source' => $cid, 'target' => $weight));
        return html_writer::tag('a', html_writer::tag('span', get_string('movecoursehere', 'block_course_overview'),
                                array('class' => 'accesshide')), array('href' => $url, 'class' => 'coursemovetarget'));
    }

    protected function activity_display($cid, $overview) {
        global $OUTPUT;
        $output = html_writer::start_tag('div', array('class' => 'activity_info'));
        foreach (array_keys($overview) as $module) {
            $output .= html_writer::start_tag('div', array('class' => 'activity_overview'));
            $url = new moodle_url("/mod/$module/index.php", array('id' => $cid));
            $icontext = html_writer::link($url, $OUTPUT->pix_icon('icon', get_string('modulename', $module), 'mod_'.$module, array('class'=>'icon')).' ');
            if (get_string_manager()->string_exists("activity_$module", 'block_course_overview')) {
                $icontext .= get_string("activity_$module", 'block_course_overview');
            } else {
                $icontext .= get_string("activityoverview", 'block_course_overview', $module);
            }

            //add collapsible region with overview text in it
            $output .= $this->collapsible_region($overview[$module], '', 'region_'.$cid.'_'.$module, $icontext, '', true);

            $output .= html_writer::end_tag('div');
        }
        $output .= html_writer::end_tag('div');
        return $output;
    }

    public function editing_bar_head($max = 0) {
        global $OUTPUT, $USER;
        $output = $OUTPUT->box_start('notice');

        $options = array('0' => get_string('alwaysshowall', 'block_course_overview'));
        for ($i = 1; $i <= $max; $i++) {
            $options[$i] = $i;
        }
        $url = new moodle_url('/my/index.php');
        $select = new single_select($url, 'mynumber', $options, $USER->profile['mynumber'], array());
        $select->set_label(get_string('numtodisplay', 'block_course_overview'));
        $output .= $OUTPUT->render($select);

        $output .= $OUTPUT->box_end();
        return $output;
    }

    public function hidden_courses($total) {
        global $OUTPUT;
        if ($total <= 0) {
            return;
        }
        $output = $OUTPUT->box_start('notice');
        $plural = $total > 1 ? 'plural' : '';
        $output .= get_string('hiddencoursecount'.$plural, 'block_course_overview', $total);
        $output .= $OUTPUT->box_end();
        return $output;
    }

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
     * @param boolean $default Initial collapsed state to use if the user_preference it not set.
     * @param boolean $return if true, return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region_start($classes, $id, $caption, $userpref = '', $default = false) {
        global $CFG, $PAGE, $OUTPUT;

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
        $this->page->requires->js_init_call('M.block_course_overview.collapsible', array($id, $userpref, get_string('clicktohideshow')));

        return $output;
    }

    /**
     * Close a region started with print_collapsible_region_start.
     *
     * @param boolean $return if true, return the HTML as a string, rather than printing it.
     */
    protected function collapsible_region_end() {
        $output = '</div></div></div>';
        return $output;
    }

    public function welcome_area() {
        global $OUTPUT, $USER;
        $output = $OUTPUT->box_start('welcome_area');

        $picture = $OUTPUT->user_picture($USER, array('size' => 75, 'class' => 'welcome_userpicture'));
        $output .= html_writer::tag('div', $picture, array('class' => 'profilepicture'));

        $output .= $OUTPUT->box_start('welcome_message');
        $output .= $OUTPUT->heading(get_string('welcome', 'block_course_overview', $USER->firstname));

        //messages
        $count = message_count_unread_messages($USER);
        $plural = 's';
        if ($count > 0) {
            $output .= get_string('you_have_messages', 'block_course_overview', $count);
        } else {
            $output .= get_string('you_have_no_messages', 'block_course_overview');
            if ($count == 1) {
                $plural = '';
            }
        }
        $output .= html_writer::link(new moodle_url('/message/index.php'), get_string('message'.$plural, 'block_course_overview'));
        $output .= $OUTPUT->box_end();
        $output .= $OUTPUT->box('', 'flush');
        $output .= $OUTPUT->box_end();

        return $output;
    }
}
