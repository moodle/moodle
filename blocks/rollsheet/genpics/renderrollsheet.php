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

global $CFG, $DB;
require_login();

function renderPicSheet() {
    global $DB, $cid, $CFG, $OUTPUT;
    $pagecounter = 1;
    $usersperpage = get_config('block_rollsheet', 'usersPerPage');
    $cid = required_param('cid', PARAM_INT);
    $selectedgroupid = optional_param('selectgroupsec', '', PARAM_INT);
    $appendorder = '';
    $orderby = optional_param('orderby', '', PARAM_TEXT);
    if ($orderby == 'byid') {
        $appendorder = ' order by u.id';
    } else if ($orderby == 'firstname') {
        $appendorder = ' order by u.firstname, u.lastname';
    } else if ($orderby == 'lastname') {
        $appendorder = ' order by u.lastname, u.firstname';
    } else {
        $appendorder = ' order by u.lastname, u.firstname, u.idnumber';
    }

    // Check if we need to include a custom field.
    $groupname = $DB->get_record('groups', array('id' => $selectedgroupid), $fields = '*', $strictness = IGNORE_MISSING);
    $groupids = groups_get_user_groups($cid);
    $groupids = $groupids[0]; // Ignore groupings.
    $groupids = implode(",", $groupids);
    $context = context_course::instance($cid);
    $mainuserfields = user_picture::fields('u', array('id'), 'userid');
    $student = "'student'";
    $ctxlevel = $context->contextlevel;

    if ($groupname) {
       $query = "SELECT u.id, u.idnumber, $mainuserfields
                 FROM {course} c
                    INNER JOIN {context} cx ON c.id = cx.instanceid AND cx.contextlevel = $ctxlevel
                    INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
                    INNER JOIN {role} r ON ra.roleid = r.id
                    INNER JOIN {user} u ON ra.userid = u.id
                    INNER JOIN {groups_members} gm ON u.id = gm.userid
                    INNER JOIN {groups} g ON gm.groupid = g.id AND c.id = g.courseid
                 WHERE r.shortname = $student AND gm.groupid = ?" . $appendorder;
        $result = $DB->get_records_sql($query, array($selectedgroupid));
    } else if (!has_capability('moodle/site:accessallgroups', $context)) {
        $query = "SELECT CONCAT(u.id, g.id) AS groupuserid, u.id, u.idnumber, $mainuserfields
                  FROM {course} c
                    INNER JOIN {context} cx ON c.id = cx.instanceid AND cx.contextlevel = $ctxlevel
                    INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
                    INNER JOIN {role} r ON ra.roleid = r.id
                    INNER JOIN {user} u ON ra.userid = u.id
                    INNER JOIN {groups_members} gm ON u.id = gm.userid
                    INNER JOIN {groups} g ON gm.groupid = g.id AND c.id = g.courseid
                  WHERE r.shortname = $student AND gm.groupid IN ($groupids) " . $appendorder;
        $result = $DB->get_records_sql($query, array($cid));
    } else {
        $query = "SELECT u.id, u.idnumber, $mainuserfields 
                  FROM {course} c
                    INNER JOIN {context} cx ON c.id = cx.instanceid AND cx.contextlevel = $ctxlevel
                    INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
                    INNER JOIN {role} r ON ra.roleid = r.id
                    INNER JOIN {user} u ON ra.userid = u.id
                  WHERE r.shortname = $student AND c.id = ?" . $appendorder;
        $result = $DB->get_records_sql($query, array($cid));
    }
    $coursename = $DB->get_record('course', array('id' => $cid), 'fullname', $strictness = IGNORE_MISSING);

    $parentDivOpen = html_writer::start_tag('div', array('class' => 'placeholder'));
    $parentDivClose = html_writer::end_tag('div');
    $rowDivOpen = html_writer::start_tag('div', array('class' => 'ROWplaceholder'));

    $disclaimer = html_writer::tag('p', get_string('pdisclaimer', 'block_rollsheet'), array('class' => 'center disclaimer'));

    while (!empty($result)) {

        if ($groupname) {
            $title = html_writer::div(html_writer::tag('p', $coursename->fullname
                    . ' ' . substr($groupname->name, -3) . ' &mdash; ' . get_string('picturesheet', 'block_rollsheet')
                    . ': page ' . $pagecounter), null, array('class' => 'rolltitle center'));
        } else {
            $title = html_writer::div(html_writer::tag('p', $coursename->fullname
                    . ' &mdash; ' . get_string('picturesheet', 'block_rollsheet')
                    . ': page ' . $pagecounter), null, array('class' => 'rolltitle center'));
        }

        $pagecounter++;
        $userpicture = '';
        $j = 0;

        foreach ($result as $face) {
            $j++;
            $userpicture .= html_writer::div($OUTPUT->user_picture($face, array('courseid' => $cid
                                            , 'size' => 100, 'class' => 'welcome_userpicture'))
                         . html_writer::tag('p', $face->firstname . ' '
                         . $face->lastname, array('class' => 'center')), null, array('class' => 'floatleft'));
            array_shift($result);
            if ($j == $usersperpage) {
                break;
            }
        }

        echo $title;
        echo $userpicture;
        echo $disclaimer;
    }
}
