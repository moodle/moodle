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

defined('MOODLE_INTERNAL') || die();

class block_rollsheet extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_rollsheet');
    }

    public function applicable_formats() {
        return array('site' => false, 'my' => false, 'course-view' => true);
    }

    public function has_config() {
        return true;
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $PAGE, $COURSE, $OUTPUT, $CFG;
        $context = context_course::instance($COURSE->id);
        $permission = (
            has_capability('block/rollsheet:viewblock', $context)
        );

        $blockHidden = get_config('block_rollsheet', 'hidefromstudents');
        $content = new stdClass;
        $content->items = array();
        $content->icons = array();
        $content->footer = '';
        $this->content = $content;
        $iconclass = array('class' => 'icon');
        $cid = optional_param('id', '', PARAM_INT);
        $sheetstr = get_string('genlist', 'block_rollsheet');
        $picstr = get_string('genpics', 'block_rollsheet');

        $sheeturl = new moodle_url('/blocks/rollsheet/genlist/show.php', array('cid' => $COURSE->id));
        $picurl = new moodle_url('/blocks/rollsheet/genpics/show.php', array('cid' => $COURSE->id));

        $membergroups = groups_get_user_groups($COURSE->id);
        $membergroups = $membergroups[0];
        if (count($membergroups) == 1) {
            $selectgroupsec = implode("", $membergroups);
            $sheeturl .= '&rendertype=group&selectgroupsec=' . $selectgroupsec;
            $picurl .= '&rendertype=group&selectgroupsec=' . $selectgroupsec;
        }

        if ($permission) {
            $content->items[] = html_writer::link($sheeturl, $sheetstr);
            $content->items[] = html_writer::link($picurl, $picstr);
            $content->icons[] = $OUTPUT->pix_icon('i/users', $sheetstr, 'moodle', $iconclass);
            $content->icons[] = $OUTPUT->pix_icon('i/users', $picstr, 'moodle', $iconclass);
        }
        return $this->content;
    }
}