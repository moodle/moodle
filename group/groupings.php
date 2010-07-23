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
 * Allows a creator to edit groupings
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package groups
 */

require_once '../config.php';
require_once $CFG->dirroot.'/group/lib.php';

$courseid = required_param('id', PARAM_INT);

$PAGE->set_url('/group/groupings.php', array('id'=>$courseid));

if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/course:managegroups', $context);

$strgrouping     = get_string('grouping', 'group');
$strgroups       = get_string('groups');
$strname         = get_string('name');
$strdelete       = get_string('delete');
$stredit         = get_string('edit');
$srtnewgrouping  = get_string('creategrouping', 'group');
$strgroups       = get_string('groups');
$strgroupings    = get_string('groupings', 'group');
$struses         = get_string('activities');
$strparticipants = get_string('participants');
$strmanagegrping = get_string('showgroupsingrouping', 'group');

navigation_node::override_active_url(new moodle_url('/group/index.php', array('id'=>$courseid)));
$PAGE->navbar->add($strgroupings);

/// Print header
$PAGE->set_title($strgroupings);
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

// Add tabs
$currenttab = 'groupings';
require('tabs.php');

echo $OUTPUT->heading($strgroupings);

$data = array();
if ($groupings = $DB->get_records('groupings', array('courseid'=>$course->id), 'name')) {
    foreach($groupings as $grouping) {
        $line = array();
        $line[0] = format_string($grouping->name);

        if ($groups = groups_get_all_groups($courseid, 0, $grouping->id)) {
            $groupnames = array();
            foreach ($groups as $group) {
                $groupnames[] = format_string($group->name);
            }
            $line[1] = implode(', ', $groupnames);
        } else {
            $line[1] = get_string('none');
        }
        $line[2] = $DB->count_records('course_modules', array('course'=>$course->id, 'groupingid'=>$grouping->id));

        $buttons  = "<a title=\"$stredit\" href=\"grouping.php?id=$grouping->id\"><img".
                    " src=\"" . $OUTPUT->pix_url('t/edit') . "\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        $buttons .= "<a title=\"$strdelete\" href=\"grouping.php?id=$grouping->id&amp;delete=1\"><img".
                    " src=\"" . $OUTPUT->pix_url('t/delete') . "\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        $buttons .= "<a title=\"$strmanagegrping\" href=\"assign.php?id=$grouping->id\"><img".
                    " src=\"" . $OUTPUT->pix_url('i/group') . "\" class=\"icon\" alt=\"$strmanagegrping\" /></a> ";

        $line[3] = $buttons;
        $data[] = $line;
    }
}
$table = new html_table();
$table->head  = array($strgrouping, $strgroups, $struses, $stredit);
$table->size  = array('30%', '50%', '10%', '10%');
$table->align = array('left', 'left', 'center', 'center');
$table->width = '90%';
$table->data  = $data;
echo html_writer::table($table);

echo $OUTPUT->container_start('buttons');
echo $OUTPUT->single_button(new moodle_url('grouping.php', array('courseid'=>$courseid)), $srtnewgrouping);
echo $OUTPUT->container_end();

echo $OUTPUT->footer();
