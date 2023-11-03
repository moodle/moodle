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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @subpackage learnerscript
 * @author: manikanta
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/badgeslib.php');
use block_learnerscript\local\reportbase;
//use block_learnerscript\lsreports\badge;
use block_learnerscript\report;
use block_learnerscript\local\querylib;
use block_learnerscript\local\ls as ls;
use context_system;
use badge;
use completion_info;
use stdClass;
class report_badges extends reportbase implements report {

    public function __construct($report, $reportproperties) {
        global $USER;
        parent::__construct($report, $reportproperties);
        $this->columns = array('badges' => array('name', 'issuername', 'coursename', 'timecreated', 'description', 'criteria', 'recipients', 'expiredate'));
        $this->parent = true;
        $this->components = array('columns', 'filters', 'permissions', 'plot');
        $this->courselevel = false;
        $this->filters = array('courses');
        $this->orderable = array('name');
        $this->defaultcolumn = 'b.id';
        $this->excludedroles = array("'student'");
    }
    function init() {

    }
    function count() {
        $this->sql  = "SELECT COUNT(DISTINCT b.id) ";
    }
    function select(){
        $this->sql = "SELECT b.id, b.courseid, b.attachment, b.issuerurl, b.usercreated, b.expireperiod ";
        if (!empty($this->selectedcolumns)) {
            if (in_array('name', $this->selectedcolumns)) {
                $this->sql .= ", b.name AS name";
            }
            if (in_array('issuername', $this->selectedcolumns)) {
                $this->sql .= ", b.issuername AS issuername";
            }
            if (in_array('timecreated', $this->selectedcolumns)) {
                $this->sql .= ", b.timecreated AS timecreated";
            }
            if (in_array('description', $this->selectedcolumns)) {
                $this->sql .= ", b.description AS description";
            }
            if (in_array('expiredate', $this->selectedcolumns)) {
                $this->sql .= ", b.expiredate AS expiredate";
            }
        }
    }
    function from() {
        $this->sql .= " FROM {badge} AS b";
    }
    function joins() {
        $this->sql .= " LEFT JOIN {course} AS c ON c.id = b.courseid AND c.visible = 1";
    }
    function where() {
        $this->sql .=" WHERE  b.status != 0 AND b.status != 2 AND b.status != 4";
        if ($this->ls_startdate >= 0 && $this->ls_enddate) {
            $this->params['ls_fstartdate'] = ROUND($this->ls_startdate);
            $this->params['ls_fenddate'] = ROUND($this->ls_enddate);
            $this->sql .= " AND b.timecreated BETWEEN :ls_fstartdate AND :ls_fenddate ";
        }
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if ($this->rolewisecourses != '') {
                $this->sql .= " AND b.courseid IN ($this->rolewisecourses) ";
            } 
        }
        parent::where();
    }
    function search() {
        global $DB;
        if (isset($this->search) && $this->search) {
            $statsql = array();
            $this->searchable =array('b.issuername', 'b.name', 'c.fullname');
            foreach ($this->searchable as $key => $value) {
                $statsql[] =$DB->sql_like($value, "'%" . $this->search . "%'",$casesensitive = false,$accentsensitive = true, $notlike = false);
            }
            $fields = implode(" OR ", $statsql);         
            $this->sql .= " AND ($fields) ";
        }
    }
    function filters() {
        if (!empty($this->params['filter_courses']) && $this->params['filter_courses'] <> SITEID  && !$this->scheduling) {
            $this->sql .= " AND b.courseid IN (:filter_courses)";
        }
    }
    function groupby() {
        
    }

    public function get_rows($badges) {
        global $DB, $CFG, $PAGE, $OUTPUT, $USER;
        $systemcontext = context_system::instance();
        $data = array();
        if (!empty($badges)) {
            foreach ($badges as $badge) {
                if (!$badge->id) {
                    continue;
                }
                $batchinstance = new badge($badge->id);
                $context = $batchinstance->get_context();
                $badgeimage = print_badge_image($batchinstance, $context);
                $getcriteria = $PAGE->get_renderer('core_badges');
                $criteria = $getcriteria->print_badge_criteria($batchinstance);
                $courserecord = $DB->get_record('course', array('id' => $badge->courseid));
                $completioninfo = new completion_info($courserecord);
                $activityinforeport = new stdClass();
                $params = array();
                $recipients = $DB->count_records_sql('SELECT COUNT(b.userid)
                                        FROM {badge_issued} b INNER JOIN {user} u ON b.userid = u.id
                                        WHERE b.badgeid = :badgeid AND u.deleted = 0 AND u.confirmed = 1
                                        ', array('badgeid' => $badge->id));
                if ($this->ls_startdate >= 0 && $this->ls_enddate) {
                    $datefiltersql = " AND gg.timemodified BETWEEN :startdate AND :enddate ";
                    $datesql = " AND timemodified BETWEEN :startdate AND :enddate ";
                    $params['startdate'] = $this->ls_startdate;
                    $params['enddate'] = $this->ls_enddate;
                }
                $activityinforeport->name = '<a href="' . $CFG->wwwroot . '/badges/overview.php?id=' . $badge->id . '" target="_blank" class="edit">' . $badgeimage . ' ' . $badge->name . '</a>';

                // $activityinforeport->username = $userrecord->firstname .'  '. $userrecord->lastname;
                $activityinforeport->issuername = $badge->issuername;
                if ($badge->courseid = null || empty($badge->courseid)) {
                    $activityinforeport->coursename = $courserecord->fullname ? $courserecord->fullname : 'System';
                } else {
                    $reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'courseprofile'), IGNORE_MULTIPLE);
                    $profilepermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($this->userid, $systemcontext);
                    if (empty($reportid) || empty($profilepermissions)) {
                        $activityinforeport->coursename = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$courserecord->id.'" />'.$courserecord->fullname.'</a>';
                    } else {
                        $activityinforeport->coursename = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$courserecord->id.'" target="_blank" class="edit">'.($courserecord->fullname ? $courserecord->fullname : 'System').'</a>';
                    }
                }

                $activityinforeport->timecreated = date('l, d F Y H:i A', $badge->timecreated);
                if (!empty($badge->expiredate)) {
                    $badgeexpiredate = date('l, d F Y', $badge->expiredate);
                } else if (!empty($badge->expireperiod)) {
                    if ($badge->expireperiod < 60) {
                        $badgeexpiredate = get_string('expireperiods', 'badges', round($badge->expireperiod, 2));
                    } else if ($badge->expireperiod < 60 * 60) {
                        $badgeexpiredate = get_string('expireperiodm', 'badges', round($badge->expireperiod / 60, 2));
                    } else if ($badge->expireperiod < 60 * 60 * 24) {
                        $badgeexpiredate = get_string('expireperiodh', 'badges', round($badge->expireperiod / 60 / 60, 2));
                    } else {
                        $badgeexpiredate = get_string('expireperiod', 'badges', round($badge->expireperiod / 60 / 60 / 24, 2));
                    }
                } else {
                    $badgeexpiredate = "--";
                }
                $activityinforeport->expiredate = $badgeexpiredate;
                $activityinforeport->description = $badge->description;
                $activityinforeport->criteria = $criteria;
                // $activityinforeport->recipients = $badge->recipients;
                $activityinforeport->recipients = '<a href="'. $CFG->wwwroot. '/badges/recipients.php?id='.$badge->id.'" target="_blank" class="edit">'.$recipients.'</a>';
                $data[] = $activityinforeport;
            }
        }
        return $data;
    }
}
