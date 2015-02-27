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

class userrep {
    // Find completion data. $courseid=0 means all courses
    // for that company.
    public static function get_completion( $userid, $courseid ) {
        global $DB;

        // Going to build an array for the data.
        $data = array();

        // Count the three statii for the graph.
        $notstarted = 0;
        $inprogress = 0;
        $completed = 0;

        // Get completion data for course.
        // Get course object.
        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            error( 'unable to find course record' );
        }
        $datum = new stdclass();
        $datum->coursename = $course->fullname;

        // Instantiate completion info thingy.
        $info = new completion_info( $course );

        // Get gradebook details.
        $gbsql = "select gg.finalgrade as result from {grade_grades} gg, {grade_items} gi
                  WHERE gi.courseid=$courseid AND gi.itemtype='course' AND gg.userid=$userid
                  AND gi.id=gg.itemid";
        if (!$gradeinfo = $DB->get_record_sql($gbsql)) {
            $gradeinfo = new object();
            $gradeinfo->result = null;
        }

        // If completion is not enabled on the course
        // there's no point carrying on.
        if (!$info->is_enabled()) {
            $datum->enabled = false;
            $data[ $courseid ] = $datum;
            return false;
        } else {
            $datum->enabled = true;
        }

        // Get criteria for coursed.
        // This is an array of tracked activities (only tracked ones).
        $criteria = $info->get_criteria();

        // Number of tracked activities to complete.
        $trackedcount = count( $criteria );
        $datum->trackedcount = $trackedcount;

        $u = new stdclass();

        // Iterate over users to get info.

        // Find user's completion info for this course.
        if ($completioninfo = $DB->get_record( 'course_completions',
                                                array('userid' => $userid,
                                                      'course' => $courseid))) {
            $u->timeenrolled = $completioninfo->timeenrolled;
            if (!empty($completioninfo->timestarted)) {
                $u->timestarted = $completioninfo->timestarted;
                if (!empty($completioninfo->timecompleted)) {
                    $u->timecompleted = $completioninfo->timecompleted;
                    $u->status = 'completed';
                    ++$completed;
                } else {
                    $u->timecompleted = 0;
                    $u->status = 'inprogress';
                    ++$inprogress;
                }

            } else {
                $u->timestarted = 0;
                $u->status = 'notstarted';
                ++$notstarted;
            }

        } else {
            $u->timeenrolled = 0;
            $u->timecompleted = 0;
            $u->timestarted = 0;
            $u->status = 'notstarted';
            ++$notstarted;
        }

        $u->result = round($gradeinfo->result, 0);
        $datum->completion = $u;
        $data[ $courseid ] = $datum;

        // Make return object.
        $returnobj = new stdclass();
        $returnobj->data = $data;
        $returnobj->criteria = $criteria;

        return $returnobj;
    }
}
