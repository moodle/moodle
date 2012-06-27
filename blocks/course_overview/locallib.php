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

function block_course_overview_get_overviews($courses) {
    global $CFG, $USER, $DB, $OUTPUT;
    $htmlarray = array();
    if ($modules = $DB->get_records('modules')) {
        foreach ($modules as $mod) {
            if (file_exists(dirname(__FILE__).'/../../mod/'.$mod->name.'/lib.php')) {
                include_once(dirname(__FILE__).'/../../mod/'.$mod->name.'/lib.php');
                $fname = $mod->name.'_print_overview';
                if (function_exists($fname)) {
                    $fname($courses,$htmlarray);
                }
            }
        }
    }
    return $htmlarray;
}

function block_course_overview_update_mynumber($number) {
    global $DB, $USER;
    if ($field = $DB->get_record('user_info_field', array('shortname' => 'mynumber'))) {
        if ($data = $DB->get_record('user_info_data', array('fieldid' => $field->id, 'userid' => $USER->id))) {
            $data->data = $number;
            $DB->update_record('user_info_data', $data);
        } else {
            $data = new stdClass();
            $data->fieldid = $field->id;
            $data->userid = $USER->id;
            $data->data = $number;
            $DB->insert_record('user_info_data', $data);
        }
    }
}

function block_course_overview_update_myorder($sortorder) {
    global $DB, $USER;
    if ($field = $DB->get_record('user_info_field', array('shortname' => 'myorder'))) {
        if ($data = $DB->get_record('user_info_data', array('fieldid' => $field->id, 'userid' => $USER->id))) {

            $oldlist = explode(',', $data->data);
            $newlist = explode(',', $sortorder);
            foreach ($oldlist as $oldentry) {
                if (!in_array($oldentry, $newlist)) {
                    $newlist[] = $oldentry;
                }
            }
            $sortorder = implode(',', $newlist);

            $data->data = $sortorder;
            $DB->update_record('user_info_data', $data);
        } else {
            $data = new stdClass();
            $data->fieldid = $field->id;
            $data->userid = $USER->id;
            $data->data = $sortorder;
            $DB->insert_record('user_info_data', $data);
        }
    }
}

function block_course_overview_get_child_shortnames($courseid) {
    global $COURSE, $DB, $OUTPUT;

    $sql = "SELECT c.shortname
            FROM {enrol} AS e
            JOIN {course} AS c ON (c.id = e.customint1)
            WHERE e.courseid = :courseid AND e.enrol = :method ORDER BY e.sortorder";
    $params = array('method' => 'meta', 'courseid' => $courseid);
    if ($results = $DB->get_records_sql($sql, $params)) {
        $shortnames = array();
        foreach ($results as $res) {
            $shortnames[] = $res->shortname;
        }
        $total = count($shortnames);
        $suffix = '';
        if ($total > 10) {
            $shortnames = array_slice($shortnames, 0, 10);
            $diff = $total - count($shortnames);
            $plural = $diff > 1 ? 's' : '';
            $suffix = " (and $diff other$plural)";
        }
        $shortnames = 'includes '.implode('; ', $shortnames).$suffix;
    }

    return isset($shortnames) ? $shortnames : false;
}

function block_course_overview_get_sorted_courses() {
    global $USER;

    $limit = 71; //TODO: Make this a block setting
    if (isset($USER->profile['mynumber']) && intval($USER->profile['mynumber']) > 0) {
        $limit = intval($USER->profile['mynumber']);
    } else {
        $USER->profile['mynumber'] = 0;
    }

    $courses = enrol_get_my_courses('id, shortname, fullname, modinfo');
    $site = get_site();

    if (array_key_exists($site->id,$courses)) {
        unset($courses[$site->id]);
    }

    foreach ($courses as $c) {
        if (isset($USER->lastcourseaccess[$c->id])) {
            $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
        } else {
            $courses[$c->id]->lastaccess = 0;
        }
    }

    $order = array();
    if (isset($USER->profile['myorder'])) {
        $order = explode(',', $USER->profile['myorder']);
    }

    $courses_sorted = array();

    //unsorted courses top of the list
    foreach ($courses as $c) {
        if (count($courses_sorted) >= $limit) {
            break;
        }
        if (!in_array($c->id, $order)) {
            $courses_sorted[$c->id] = $c;
        }
    }

    //get courses in sort order into list
    foreach ($order as $o) {
        if (count($courses_sorted) >= $limit) {
            break;
        }
        if (isset($courses[intval($o)])) {
            $courses_sorted[$o] = $courses[$o];
        }
    }

    //append the remaining courses onto the end of the list
    foreach ($courses as $c) {
        if (count($courses_sorted) >= $limit) {
            break;
        }
        if (!isset($courses_sorted[$c->id])) {
            $courses_sorted[$c->id] = $c;
        }
    }

    return array($courses_sorted, count($courses));
}
