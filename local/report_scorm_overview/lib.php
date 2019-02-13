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

class scormcomprep{

    // Get the jsmodule setup thingy.
    public function getjsmodule() {
        $jsmodule = array(
            'name'     => 'local_report_completion',
            'fullpath' => '/course/report/iomad_completion/module.js',
            'requires' => array('base', 'node', 'charts', 'json'),
            'strings' => array(
               )
        );
        return $jsmodule;
    }

    // Check the user and the companyid are allowed.
    public function confirm_user_company($user, $companyid) {
        global $DB;

        // Companyid is defined?
        if ($companyid == 0) {
            return true;
        }

        // User must either be in the companymanager table for THIS company
        // or not at all.
        if ($companies = $DB->get_records('companymanager', array('userid' => $user->id))) {
            foreach ($companies as $company) {
                if ($company->companyid == $companyid) {
                    return true;
                }
            }

            // If we get this far then there's a problem.
            return false;
        }

        // Not in table, so that's fine.
        return true;
    }

    // Create the select list of companies.
    // If the user is in the company managers table then the list is restricted.
    public function companyselectlist($user) {
        global $DB;

        // Create "empty" array.
        $companyselect = array(0 => get_string('select', 'local_report_completion'));

        // Get the companies they manage.
        $managedcompanies = array();
        if ($managers = $DB->get_records('companymanager', array('userid' => $user->id))) {
            foreach ($managers as $manager) {
                $managedcompanies[] = $manager->companyid;
            }
        }

        // Get companies information.
        if (!$companies = $DB->get_records('company')) {
            return $companyselect;
        }

        // Make suitable for a select list.
        foreach ($companies as $company) {

            // If managers found then only allow selected companies.
            if (!empty($managedcompanies)) {
                if (!in_array($company->id, $managedcompanies)) {
                    continue;
                }
            }
            $companyselect[$company->id] = $company->name;
        }

        return $companyselect;
    }

    // Create the select list of courses.
    public function courseselectlist($companyid=0) {
        global $DB;
        global $SITE;

        // Create "empty" array.
        $courseselect = array(0 => get_string('select', 'local_report_completion'));

        // If the companyid=0 then there's no courses.
        if ($companyid == 0) {
            return $courseselect;
        }

        // Get courses for given company.
        if (!$courses = $DB->get_records('companycourse', array('companyid' => $companyid))) {
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
    public function participantsselectlist($courseid, $companyid) {
        global $DB;

        // Empty list.
        $participantselect = array(0 => get_string('select', 'local_report_completion'));

        // If companyid = 0 then nothing to do.
        if ($companyid == 0) {
            return $participantselect;
        }

        // Get company.
        if (!$company = $DB->get_record('company', array('id' => $companyid))) {
            error('unable to find company record');
        }

        // Get list of users.
        $users = self::getcompanyusers($company->shortname);

        // Add to select list.
        foreach ($users as $user) {
            $participantselect[ $user->id ] = fullname($user);
        }

        return $participantselect;
    }

    // Get the users that belong to company
    // with supplied short name.
    // TODO: Also need to restrict by course, but difficult
    // to see what capability or role assignment to check.
    public function getcompanyusers($shortname) {
        global $DB;

        // Find the info field for Company.
        if (!$infofieldid = $DB->get_record('user_info_field', array('shortname' => 'company'))) {
            print_error('errornoinfofield', 'local_report_completion');
        }

        // Get the user ids for that company.
        $sql = "SELECT * FROM {user_info_data}
                WHERE fieldid = ?
                AND " . $DB->sql_compare_text('data') . " = ? ";
        if (!$dataids = $DB->get_records_sql($sql, array($infofieldid->id, $shortname))) {
            return array();
        }

        // Run through and get users.
        $users = array();
        foreach ($dataids as $dataid) {
            $userid = $dataid->userid;
            if (!$user = $DB->get_record('user', array('id' => $userid))) {
                print_error('userrecordnotfound', 'local_report_completion');
            }
            $users[] = $user;
        }

        return $users;
    }

    // Find completion data. $courseid=0 means all courses
    // for that company.
    public function get_completion($companyid, $courseid=0) {
        global $DB;

        // Get list of course ids.
        $courseids = array();
        if ($courseid == 0) {
            if (!$courses = $DB->get_records('companycourse', array('companyid' => $companyid))) {
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
                error('unable to find course record');
            }
            $datum = null;
            $datum->coursename = $course->fullname;

            // Instantiate completion info thingy.
            $info = new completion_info($course);

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
            $trackedcount = count($criteria);
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
            foreach ($progress as $user) {
                ++$numusers;
                $u = null;
                $u->fullname = fullname($user);

                // Count of progress is the number they have completed.
                $u->completed_count = count($user->progress);
                if ($trackedcount > 0) {
                    $u->completed_percent = round(100 * $u->completed_count / $trackedcount, 2);
                } else {
                    $u->completed_percent = '0';
                }
                // Find user's last access to this course.
                if ($lastaccess = $DB->get_record('user_lastaccess',
                                                   array('userid' => $user->id,
                                                         'courseid' => $courseid))) {
                    $u->lastaccess = $lastaccess->timeaccess;
                } else {
                    $u->lastaccess = 0;
                }

                // Work out status.
                if ($u->completed_percent == 0) {
                    $u->status = 'notstarted';
                    ++$notstarted;
                    ++$numnotstarted;
                } else if ($u->completed_percent < 100) {
                    $u->status = 'inprogress';
                    ++$inprogress;
                    ++$numprogress;
                } else {
                    $u->status = 'completed';
                    ++$completed;
                    ++$numcomplete;
                }
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
        $graphdata = array('notstarted' => $notstarted,
                           'inprogress' => $inprogress,
                           'completed' => $completed);

        // Make return object.
        $returnobj = null;
        $returnobj->data = $data;
        $returnobj->graphdata = $graphdata;

        return $returnobj;
    }

    // Draw the pie chart.
    // Not config.php has NOT been included so
    // act accordingly!!
    public static function drawchart($data) {

        // Include the chart libraries.
        $plib = '../iomad/pchart';
        require_once("$plib/class/pDraw.class.php");
        require_once("$plib/class/pPie.class.php");
        require_once("$plib/class/pImage.class.php");
        require_once("$plib/class/pData.class.php");

        // Chart data.
        $chartdata = new pData();
        $chartdata->addPoints(array($data->notstarted,
                                    $data->inprogress,
                                    $data->completed), "Value");

        // Labels.
        $chartdata->addPoints(array('Not started', 'In progress', 'Completed'), "Legend");
        $chartdata->setAbscissa("Legend");

        // Chart object.
        $chart = new pImage(350, 180, $chartdata);

        // Pie chart object.
        $pie = new pPie($chart, $chartdata);
        $chart->setShadow(false);
        $chart->setFontProperties(array("FontName" => "$plib/fonts/GeosansLight.ttf",
                                        "FontSize" => 11));
        $pie->setSliceColor(0, array("R" => 200, "G" => 0, "B" => 0));
        $pie->setSliceColor(1, array("R" => 200, "G" => 200, "B" => 0));
        $pie->setSliceColor(2, array("R" => 0, "G" => 200, "B" => 0));
        $pie->draw3Dpie(175, 100,
            array(
                "Radius" => 80,
                "DrawLabels" => true,
                "DataGapAngle" => 10,
                "DataGapRadius" => 6,
                "Border" => true
        ));

        // Display the chart.
        $chart->stroke();
    }

}

