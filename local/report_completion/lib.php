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
 * @package   local_report_completion
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class comprep{

    // Get the jsmodule setup thingy.
    public static function getjsmodule() {
        $jsmodule = array(
            'name'     => 'local_report_completion',
            'fullpath' => '/course/report/iomad_completion/module.js',
            'requires' => array('base', 'node', 'charts', 'json'),
            'strings' => array(
                )
        );
        return $jsmodule;
    }

    // Create the select list of courses.
    public static function courseselectlist($companyid = 0) {
        global $DB;
        global $SITE;

        // Create "empty" array.
        $courseselect = array(0 => get_string('select', 'local_report_completion'));

        // If the companyid=0 then there's no courses.
        if ($companyid == 0) {
            return $courseselect;
        }

        // Get courses for given company.
        if (!$courses = $DB->get_records('company_course', array('companyid' => $companyid))) {
            return $courseselect;
        }
        // Get the course names and put them in the list.
        foreach ($courses as $course) {
            if ($course->courseid == $SITE->id) {
                continue;
            }
            $coursefull = $DB->get_record('course', array('id' => $course->courseid));
            $courseselect[$coursefull->id] = $coursefull->fullname;
        }
        return $courseselect;
    }

    // Create list of course users.
    public static function participantsselectlist($courseid, $companyid ) {
        global $DB;

        // Empty list.-
        $participantselect = array(0 => get_string('select', 'local_report_completion'));

        // If companyid = 0 then nothing to do.
        if ($companyid == 0) {
            return $participantselect;
        }

        // Get company.
        if (!$company = $DB->get_record( 'company', array('id' => $companyid))) {
            error( 'unable to find company record' );
        }

        // Get list of users.
        $users = self::getcompanyusers( $company->shortname );

        // Add to select list.
        foreach ($users as $user) {
            $participantselect[ $user->id ] = fullname( $user );
        }

        return $participantselect;
    }

    // Get the users that belong to company
    // with supplied short name.
    // TODO: Also need to restrict by course, but difficult
    // to see what capability or role assignment to check.
    public static function getcompanyusers( $companyid ) {
        global $DB;

        if (! $dataids = $DB->get_records('company_users', array('companyid' => $companyid))) {
            return array();
        }

        // Run through and get users.
        $users = array();
        foreach ($dataids as $dataid) {
            $userid = $dataid->userid;
            if (!$user = $DB->get_record( 'user', array('id' => $userid))) {
                print_error( 'userrecordnotfound', 'local_report_completion' );
            }
            $users[] = $user;
        }

        return $users;
    }

    // Find completion data. $courseid=0 means all courses
    // for that company.
    public static function get_completion( $companyid, $courseid = 0, $wantedusers = null, $compfrom = 0, $compto = 0 ) {
        global $DB, $CFG;

        // Get list of course ids.
        $courseids = array();
        if ($courseid == 0) {
            if (!$courses = $DB->get_records_sql("SELECT c.id AS courseid FROM {course} c
                                                  WHERE c.id in (
                                                    SELECT courseid FROM {companycourse}
                                                    WHERE companyid = $companyid )
                                                  OR c.id in (
                                                    SELECT pc.courseid FROM {iomad_courses} pc
                                                    INNER JOIN {company_shared_courses} csc
                                                    ON pc.courseid=csc.courseid
                                                    WHERE pc.shared=2
                                                    AND csc.companyid = $companyid )
                                                  OR c.id in (
                                                    SELECT pc.courseid FROM {iomad_courses} pc
                                                    WHERE pc.shared=1)")) {
                // No courses for company, so exit.
                return false;
            }
            foreach ($courses as $course) {
                $courseids[] = $course->courseid;
            }
        } else {
            $courseids[] = $courseid;
        }

        // Going to build an array for the data.
        $data = array();

        // Count the three statii for the graph.
        $notstarted = 0;
        $inprogress = 0;
        $completed = 0;

        // Get completion data for each course.
        foreach ($courseids as $courseid) {

            // Get course object.
            if (!$course = $DB->get_record('course', array('id' => $courseid))) {
                error( 'unable to find course record' );
            }
            $datum = null;
            $datum->coursename = $course->fullname;

            // Instantiate completion info thingy.
            $info = new completion_info( $course );

            // If completion is not enabled on the course
            // there's no point carrying on.
            if (!$info->is_enabled()) {
                $datum->enabled = false;
                $data[ $courseid ] = $datum;
                continue;
            } else {
                $datum->enabled = true;
            }

            // Get criteria for coursed.
            // This is an array of tracked activities (only tracked ones).
            $criteria = $info->get_criteria();

            // Number of tracked activities to complete.
            $trackedcount = count( $criteria );
            $datum->trackedcount = $trackedcount;

            // Get data for all users in course.
            // This is an array of users in the course. It contains a 'progress'
            // array showing completed *tracked* activities.
            $progress = $info->get_progress_all();

            // Iterate over users to get info.
            $users = array();
            $numusers = 0;
            $numprogress = 0;
            $numcomplete = 0;
            $numnotstarted = 0;
            foreach ($wantedusers as $wanteduser) {
                if (empty($progress[$wanteduser])) {
                    continue;
                }
                $user = $progress[$wanteduser];

                ++$numusers;
                $u = null;
                $u->fullname = fullname( $user );

                // Count of progress is the number they have completed.
                $u->completed_count = count( $user->progress );
                if ($trackedcount > 0) {
                    $u->completed_percent = round(100 * $u->completed_count / $trackedcount, 2);
                } else {
                    $u->completed_percent = '0';
                }
                // Find user's completion info for this course.
                if ($completioninfo = $DB->get_record( 'course_completions', array('userid' => $user->id, 'course' => $courseid))) {
                    if ((!empty($compfrom) || !empty($compto)) && empty($completioninfo->timecompleted)) {
                        continue;
                    } else if (!empty($compfrom) && ($completioninfo->timecompleted < $compfrom)) {
                        continue;
                    } else if (!empty($compto) && ($completioninfo->timecompleted > $compto)) {
                        continue;
                    } else {
                        $u->timeenrolled = $completioninfo->timeenrolled;
                        if (!empty($completioninfo->timestarted)) {
                            $u->timestarted = $completioninfo->timestarted;
                            if (!empty($completioninfo->timecompleted)) {
                                $u->timecompleted = $completioninfo->timecompleted;
                                $u->status = 'completed';
                                ++$numcomplete;
                            } else {
                                $u->timecompleted = 0;
                                $u->status = 'inprogress';
                                ++$numprogress;
                            }

                        } else {
                            $u->timestarted = 0;
                            $u->status = 'notstarted';
                            ++$numnotstarted;
                        }
                    }

                } else {
                    $u->timeenrolled = 0;
                    $u->timecompleted = 0;
                    $u->timestarted = 0;
                    $u->status = 'notstarted';
                    ++$numnotstarted;
                }

                // Get the users score.
                $gbsql = "select gg.finalgrade as result from {grade_grades} gg, {grade_items} gi
                          WHERE gi.courseid=$courseid AND gi.itemtype='course' AND gg.userid=".$user->id."
                          AND gi.id=gg.itemid";
                if (!$gradeinfo = $DB->get_record_sql($gbsql)) {
                    $gradeinfo = new stdclass();
                    $gradeinfo->result = null;
                }
                $u->result = round($gradeinfo->result, 0);
                $userinfo = $DB->get_record('user', array('id' => $user->id));
                $u->email = $userinfo->email;
                $u->id = $user->id;

                $u->department = company_user::get_department_name($user->id);

                // Add to revised user array.
                $users[$user->id] = $u;
            }
            $datum->users = $users;
            $datum->completed = $numcomplete;
            $datum->numusers = $numusers;
            $datum->started = $numnotstarted;
            $datum->inprogress = $numprogress;

            $data[ $courseid ] = $datum;
        }

        // Make the data for the graph.
        $graphdata = array('notstarted' => $notstarted, 'inprogress' => $inprogress, 'completed' => $completed);

        // Make return object.
        $returnobj = null;
        $returnobj->data = $data;
        $returnobj->graphdata = $graphdata;

        return $returnobj;
    }

    /**
     * Sort array of objects by field.
     *
     * @param array $objects Array of objects to sort.
     * @param string $on Name of field.
     * @param string $order (ASC|DESC)
     */
    public static function sort_on_field(&$objects, $on, $order ='ASC') {
        $comparer = ($order === 'DESC')
            ? "return -strcmp(\$a->{$on},\$b->{$on});"
            : "return strcmp(\$a->{$on},\$b->{$on});";
        usort($objects, create_function('$a,$b', $comparer));
    }
}

