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
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @package   local_report_user_license_allocations
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

/**
 * Base class for the table used by local_report_user_license_allocations
 *
 * @copyright 2019 E-Learn Design Ltd. (https://www.e-learndesign.co.uk)
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_report_course_completion_course_table extends table_sql {

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_coursename($row) {
        global $output, $params;

        if (!$this->is_downloading()) {
            $courseuserslink = new moodle_url('/local/report_completion/index.php',
                                              array('courseid' => $row->id,
                                                    'validonly' => $params['validonly'],
                                                    'departmentid' => $row->departmentid));
            $coursemonthlylink = new moodle_url('/local/report_completion_monthly/index.php',
                                                array('courseid' => $row->id,
                                                      'departmentid' => $row->departmentid));
            $courselicenselink = new moodle_url('/local/report_user_license_allocations/index.php',
                                                array('courseid' => $row->id,
                                                      'departmentid' => $row->departmentid));
            $cell = html_writer::tag('h2', format_string($row->coursename, true, 1));
            $systemcontext = context_system::instance();
            if (iomad::has_capability('local/report_users:view', $systemcontext)) {
                $cell .= $output->single_button($courseuserslink, get_string('usersummary', 'local_report_completion'));
            }
            if (iomad::has_capability('local/report_completion_monthly:view', $systemcontext)) {
                if (!empty($cell)) {
                    $cell .= "</br>";
                }
                $cell .= $output->single_button($coursemonthlylink, get_string('pluginname', 'local_report_completion_monthly'));
            }
            if (iomad::has_capability('local/report_user_license_allocations:view', $systemcontext)) {
                if (!empty($cell)) {
                    $cell .= "</br>";
                }
                $cell .= $output->single_button($courselicenselink, get_string('pluginname', 'local_report_user_license_allocations'));
            }
            return $cell;

        } else {
            return format_string($row->coursename, true, 1);
        }
    }

    /**
     * Generate the display of the user's lastname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_licenseallocated($row) {
        global $output, $CFG, $DB, $params;

        // Get the company details.
        $company = new company($row->companyid);
        $parentcompanies = $company->get_parent_companies_recursive();

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND userid NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // Deal with department tree.
        $alldepartments = company::get_all_subdepartments($row->departmentid);
        $departmentsql = " AND cu.departmentid IN (" . join(",", array_keys($alldepartments)) . ") ";

        // Deal with suspended or not.
        if (empty($row->showsuspended)) {
            $suspendedsql = " AND u.suspended = 0 AND u.deleted = 0 ";
        } else {
            $suspendedsql = "AND u.deleted = 0 ";
        }

        // Are we showing as a % of all users?
        if (!empty($params['showpercentage'])) {
            $totalusers = $DB->count_records_sql("SELECT count(cu.id)
                                                  FROM {company_users} cu
                                                  JOIN {user} u ON (cu.userid = u.id)
                                                  WHERE cu.companyid = :companyid
                                                  $departmentsql
                                                  $userfilter
                                                  $suspendedsql",
                                                  array('companyid' => $company->id));
        }

        // Deal with any search dates.
        $datesql = "";
        $sqlparams = array('companyid' => $company->id, 'courseid' => $row->id);
        if (!empty($params['from'])) {
            $datesql = " AND (lit.licenseallocated > :enrolledfrom) ";
            $sqlparams['enrolledfrom'] = $params['from'];
            $sqlparams['completedfrom'] = $params['from'];
        }
        if (!empty($params['to'])) {
            $datesql .= " AND (lit.licenseallocated < :enrolledto) ";
            $sqlparams['enrolledto'] = $params['to'];
            $sqlparams['completedto'] = $params['to'];
        }

        // Count the unused licenses.
        $licensesunused = $DB->count_records_sql("SELECT COUNT(lit.id)
                                                  FROM {local_iomad_track} lit
                                                  JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                                  JOIN {user} u ON (lit.userid = u.id)
                                                  WHERE lit.companyid = :companyid
                                                  AND lit.courseid = :courseid
                                                  AND lit.licenseallocated IS NOT NULL
                                                  AND lit.timeenrolled IS NULL
                                                  $datesql
                                                  $suspendedsql
                                                  $departmentsql",
                                                  $sqlparams);

        // Count the used licenses.
        $licensesallocated = $DB->count_records_sql("SELECT count(lit.id) FROM {local_iomad_track} lit
                                                     JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                                     JOIN {user} u ON (lit.userid = u.id)
                                                     WHERE lit.courseid = :courseid
                                                     AND lit.companyid = :companyid
                                                     AND lit.licenseallocated IS NOT NULL
                                                     AND lit.timeenrolled IS NOT NULL
                                                     $datesql
                                                     $suspendedsql
                                                     $departmentsql",
                                                     $sqlparams);

        if (!$this->is_downloading()) {
            if (!empty($licenseallocated) || $DB->get_record('iomad_courses', array('courseid' => $row->id, 'licensed' => 1))) {
                $CFG->chart_colorset= ['green', '#d9534f'];
                $licensechart = new \core\chart_pie();
                $licensechart->set_doughnut(true); // Calling set_doughnut(true) we display the chart as a doughnut.
                if (empty($params['showpercentage'])) {
                    $series = new \core\chart_series('', array($licensesallocated, $licensesunused));
                    $licensechart->add_series($series);
                    $licensechart->set_labels(array(get_string('used', 'local_report_completion') . " (" . $licensesallocated . ")",
                                                    get_string('unused', 'local_report_completion') . " (" . $licensesunused . ")"));
                } else {
                    $licenseallocated = round($licenseallocated / $totalusers * 100, 2);
                    $licenseunused = round($licenseunused / $totalusers * 100, 2);
                    $series = new \core\chart_series('', array($licensesallocated, $licensesunused));
                    $licensechart->add_series($series);
                    $licensechart->set_labels(array(get_string('used', 'local_report_completion') . " (" . $licensesallocated . "%)",
                                                    get_string('unused', 'local_report_completion') . " (" . $licensesunused . "%)"));
                }
                return $output->render($licensechart, false);
            } else {
                return;
            }
        } else {
            if (!empty($licenseallocated) || $DB->get_record('iomad_courses', array('courseid' => $row->id, 'licensed' => 1))) {
                return get_string('used', 'local_report_completion') . " = " . ($licensesallocated - $licensesunused) . "\n" .
                      get_string('unused', 'local_report_completion') . " = $licensesunused\n";
            } else {
                return;
            }
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_usersummary($row) {
        global $output, $CFG, $USER, $DB, $params;

        // Get the company details.
        $company = new company($row->companyid);
        $parentcompanies = $company->get_parent_companies_recursive();

        // Deal with parent company managers
        if (!empty($parentcompanies)) {
            $userfilter = " AND userid NOT IN (
                             SELECT userid FROM {company_users}
                             WHERE companyid IN (" . implode(',', array_keys($parentcompanies)) . "))";
        } else {
            $userfilter = "";
        }

        // Deal with department tree.
        $alldepartments = company::get_all_subdepartments($row->departmentid);
        $departmentsql = " AND cu.departmentid IN (" . join(",", array_keys($alldepartments)) . ") ";

        // Deal with suspended or not.
        if (empty($row->showsuspended)) {
            $suspendedsql = " AND u.suspended = 0 AND u.deleted = 0";
        } else {
            $suspendedsql = " AND u.deleted = 0";
        }

        // Are we showing as a % of all users?
        if (!empty($params['showpercentage'])) {
            $totalusers = $DB->count_records_sql("SELECT count(cu.id)
                                                  FROM {company_users} cu
                                                  JOIN {user} u ON (cu.userid = u.id)
                                                  WHERE cu.companyid = :companyid
                                                  $departmentsql
                                                  $userfilter
                                                  $suspendedsql",
                                                  array('companyid' => $company->id));
        }

        // Deal with any search dates.
        $datesql = "";
        $sqlparams = array('companyid' => $company->id, 'courseid' => $row->id);
        if (!empty($params['from'])) {
            $datesql = " AND (lit.timeenrolled > :enrolledfrom OR lit.timecompleted > :completedfrom ) ";
            $sqlparams['enrolledfrom'] = $params['from'];
            $sqlparams['completedfrom'] = $params['from'];
        }
        if (!empty($params['to'])) {
            $datesql .= " AND (lit.timeenrolled < :enrolledto OR lit.timecompleted < :completedto) ";
            $sqlparams['enrolledto'] = $params['to'];
            $sqlparams['completedto'] = $params['to'];
        }

    // Just valid courses?
    if ($params['validonly']) {
        $validcompletedsql = " AND (lit.timeexpires > :runtime or (lit.timecompleted > 0 AND lit.timeexpires IS NULL))";
        $sqlparams['runtime'] = time();
    } else {
        $validcompletedsql = "";
    }

        // Count the completed users.
        $completed = $DB->count_records_sql("SELECT COUNT(lit.id)
                                             FROM {local_iomad_track} lit
                                             JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                             JOIN {user} u ON (lit.userid = u.id)
                                             WHERE lit.companyid = :companyid
                                             AND lit.courseid = :courseid
                                             AND lit.timecompleted IS NOT NULL
                                             $datesql
                                             $suspendedsql
                                             $validcompletedsql
                                             $departmentsql",
                                             $sqlparams);

        // Count the enrolled users
        $started = $DB->count_records_sql("SELECT COUNT(lit.id)
                                           FROM {local_iomad_track} lit
                                           JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                           JOIN {user} u ON (lit.userid = u.id)
                                           WHERE lit.companyid = :companyid
                                           AND lit.courseid = :courseid
                                           AND lit.timeenrolled IS NOT NULL
                                           AND lit.timecompleted IS NULL
                                           $datesql
                                           $suspendedsql
                                           $departmentsql",
                                           $sqlparams);

        // Count the non started users.
        $notstarted = $DB->count_records_sql("SELECT count(lit.id) FROM {local_iomad_track} lit
                                              JOIN {company_users} cu ON (lit.userid = cu.userid AND lit.companyid = cu.companyid)
                                              JOIN {user} u ON (lit.userid = u.id)
                                              WHERE lit.courseid = :courseid
                                              AND lit.companyid = :companyid
                                              AND lit.timeenrolled IS NULL
                                              $datesql
                                              $suspendedsql
                                              $departmentsql",
                                              $sqlparams);

        if (!$this->is_downloading()) {
            $enrolledchart = new \core\chart_pie();
            $enrolledchart->set_doughnut(true); // Calling set_doughnut(true) we display the chart as a doughnut.
            if (empty($params['showpercentage'])) {
                $enrolledseries = new \core\chart_series('', array($completed, $started, $notstarted));
                $enrolledchart->add_series($enrolledseries);
                $enrolledchart->set_labels(array(get_string('completedusers', 'local_report_completion') . " (" .$completed . ")",
                                             get_string('inprogressusers', 'local_report_completion') . " (" . $started . ")",
                                             get_string('notstartedusers', 'local_report_completion') . " (" . $notstarted . ")"));
            } else {
                $completed = round($completed / $totalusers * 100, 2);
                $started = round($started / $totalusers * 100, 2);
                $notstarted = round($notstarted / $totalusers * 100, 2);
                $enrolledseries = new \core\chart_series('', array($completed, $started, $notstarted));
                $enrolledchart->add_series($enrolledseries);
                $enrolledchart->set_labels(array(get_string('completedusers', 'local_report_completion') . " (" .$completed . "%)",
                                             get_string('inprogressusers', 'local_report_completion') . " (" . $started . "%)",
                                             get_string('notstartedusers', 'local_report_completion') . " (" . $notstarted . "%)"));
            }
            $CFG->chart_colorset= ['green', '#1177d1', '#d9534f'];
            return $output->render($enrolledchart, false);
        } else {
            return get_string('completedusers', 'local_report_completion') . " = $completed\n" .
                   get_string('inprogressusers', 'local_report_completion') . " = $started\n" .
                   get_string('notstartedusers', 'local_report_completion') . " = $notstarted\n";
        }
    }

    /**
     * Query the db. Store results in the table object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar. Bar
     * will only be used if there is a fullname column defined for the table.
     */
    function query_db($pagesize, $useinitialsbar=true) {
        global $DB;
        if (!$this->is_downloading()) {
            if ($this->countsql === NULL) {
                $this->countsql = 'SELECT courseid FROM '.$this->sql->from.' WHERE '.$this->sql->where;
                $this->countparams = $this->sql->params;
            }
            $subgrandtotal = $DB->get_records_sql($this->countsql, $this->countparams);
            $grandtotal = count($subgrandtotal);
            if ($useinitialsbar && !$this->is_downloading()) {
                $this->initialbars($grandtotal > $pagesize);
            }

            list($wsql, $wparams) = $this->get_sql_where();
            if ($wsql) {
                $this->countsql .= ' AND '.$wsql;
                $this->countparams = array_merge($this->countparams, $wparams);

                $this->sql->where .= ' AND '.$wsql;
                $this->sql->params = array_merge($this->sql->params, $wparams);

                $subtotal  = $DB->get_records_sql($this->countsql, $this->countparams);
                $total = count($subtotal);
            } else {
                $total = $grandtotal;
            }

            $this->pagesize($pagesize, $total);
        }

        // Fetch the attempts
        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }
        $sql = "SELECT
                {$this->sql->fields}
                FROM {$this->sql->from}
                WHERE {$this->sql->where}
                {$sort}";

        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params, $this->get_page_start(), $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params);
        }
    }
}
