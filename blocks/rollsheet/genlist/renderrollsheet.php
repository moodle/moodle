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

/*
 *
 * Retrieve and print the logo for the top of the
 * sign in sheet.
 *
 * */
function printHeaderLogo() {
    global $DB;
    $imageurl = $DB->get_field('block_rollsheet', 'field_value', array('id' => 1 ), $strictness = IGNORE_MISSING);
    echo '<img src = "'.$imageurl.'"/><br><div class = "printHeaderLogo"></div>';
}

function renderRollsheet() {
    global $DB, $cid, $CFG, $OUTPUT;
    $pagecounter = 0;
    $userspertable = get_config('block_rollsheet', 'studentsPerPage' );
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
    $totalusers = count($result);
    $usernumber = 0;
    while (!empty($result)) {
        $pagecounter++;

        if ($groupname) {
                $title = html_writer::div(html_writer::tag('p', get_string('signaturesheet', 'block_rollsheet')
                                        . ' &mdash; ' . $coursename->fullname . ': Section '
                                        . substr($groupname->name, -3) . '&nbsp;&nbsp;&nbsp;&nbsp;Page: '
                                        . $pagecounter . '&nbsp;&nbsp;&nbsp;&nbsp;Room # _____')
                                        , null, array('class' => 'rolltitle center'));
        } else {
                $title = html_writer::div(html_writer::tag('p', get_string('signaturesheet', 'block_rollsheet')
                                        . ' &mdash; ' . $coursename->fullname . '&nbsp;&nbsp;&nbsp;&nbsp;Page: '
                                        . $pagecounter . '&nbsp;&nbsp;&nbsp;&nbsp;Room # _____')
                                        , null, array('class' => 'rolltitle center'));
        }

        $disclaimer = html_writer::tag('p', get_string('absences', 'block_rollsheet'), array('class' => 'absences'));
        $disclaimer .= html_writer::tag('p', get_string('disclaimer', 'block_rollsheet'), array('class' => 'center disclaimer'));

        $k = 1;
        $table = new html_table();
        $table->attributes['class'] = 'roll';

        $addtextfield = get_config('block_rollsheet', 'includecustomtextfield');
        $addidfield = get_config('block_rollsheet', 'includeidfield');
        $numextrafields = get_config('block_rollsheet', 'numExtraFields');
        $emptyfield = '';

        $userdata = array();

        $j = 0;

        $userdatas = array();

        foreach ($result as $face) {
            $usernumber++;
            $j++;
            $userdata = array($usernumber);
            $userdata[] = ($face->firstname . ' ' . $face->lastname);

            if ($addidfield) {
                $userdata[2] = $face->idnumber;
            }

            if ($addtextfield) {
                $userdata[3] = ' ';
            }

            for ($i = 0; $i < $numextrafields; $i++) {
                $userdata[] = $emptyfield;
            }

            array_shift($result);

            $userdatas[$j] = $userdata;

            if ($k++ == $userspertable) {
                break;
            }
        }

        $table->head = array(null);
        $table->head[1] = get_string('fullName', 'block_rollsheet');

        // Id number field.
        if ($addidfield) {
            $table->head[2] = get_string('idnumber', 'block_rollsheet');
        }

        // Additional custom text field.
        if ($addtextfield) {
            $table->head[3] = get_config('block_rollsheet', 'customtext');
        }

        for ($i = 0; $i < $numextrafields; $i++) {
            $table->head[] = get_string('date', 'block_rollsheet');
        }

        $table->data = $userdatas;

        echo $title;
        echo html_writer::table($table);
        echo $disclaimer;
    }
}