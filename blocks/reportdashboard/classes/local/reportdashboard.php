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
namespace block_reportdashboard\local;
defined('MOODLE_INTERNAL') || die();
use stdclass;
use block_learnerscript\local\ls as ls;
use context_system;

class reportdashboard {
    /**
     * [add_dashboardblocks description]
     * @method add_dashboardblocks
     * @param  [type]              $data [description]
     */
    public function add_dashboardblocks($data) {
        global $CFG, $PAGE, $DB;
        $i = 0;
        $dashboardurl = $PAGE->subpage;
        foreach ($data as $reportinstance) {
            if (isset($reportinstance['report']) && $reportinstance['report'] != '') {
                $reportinstance['wieght' . $i . ''] = isset($reportinstance['wieght' . $i . ''])
                ? $reportinstance['wieght' . $i . ''] : 10;
                if ($i % 2 == 0) {
                    $PAGE->blocks->add_block('reportdashboard', 'side-db-one', $reportinstance['wieght' . $i . ''], false, null, $dashboardurl);
                } else {
                    $PAGE->blocks->add_block('reportdashboard', 'side-db-two', $reportinstance['wieght' . $i . ''], false, null, $dashboardurl);
                }
                $lastblockinstanceid = $DB->get_field_sql("SELECT id FROM {block_instances} WHERE blockname = 'reportdashboard' ORDER BY id DESC", array(), IGNORE_MULTIPLE);
                $report = $DB->get_record('block_learnerscript', array('id' => $reportinstance['report']));
                require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
                $reportcontenttypes = (new ls)->cr_listof_reporttypes($reportinstance['report']);
                reset($reportcontenttypes);
                $firstkey = key($reportcontenttypes);
                $blockdata = new stdClass();
                $blockdata->title = $report->name;
                $blockdata->reportcontenttype = $reportinstance['Report Type'];
                $blockdata->reportlist = $reportinstance['report'];
                $blockdata->contextlevel = $_SESSION['ls_contextlevel'];
                $DB->set_field('block_instances', 'configdata', base64_encode(serialize($blockdata)),
                                array('id' => $lastblockinstanceid));
                $i++;
            }
        }
    }
    /**
     * [add_tilesblocks description]
     * @method add_tilesblocks
     * @param  [type]          $data [description]
     */
    public function add_tilesblocks($data) {
        global $CFG, $PAGE, $DB;
        $i = 0;
        $dashboardurl = $PAGE->subpage;
        foreach ($data as $reportinstance) {
            if (isset($reportinstance['report']) && $reportinstance['report'] != '') {
                $reportinstance['wieght' . $i . ''] = isset($reportinstance['wieght' . $i . ''])
                ? $reportinstance['wieght' . $i . ''] : 10;
                if ($i % 3 == 0) {
                    $PAGE->blocks->add_block('reporttiles', 'side-db-first',
                        $reportinstance['wieght' . $i . ''], false, null, $dashboardurl);
                } else if ($i % 3 == 1) {
                    $PAGE->blocks->add_block('reporttiles', 'side-db-second',
                        $reportinstance['wieght' . $i . ''], false, null, $dashboardurl);
                } else {
                    $PAGE->blocks->add_block('reporttiles', 'side-db-third',
                        $reportinstance['wieght' . $i . ''], false, null, $dashboardurl);
                }
                $lastblockinstanceid = $DB->get_field_sql("SELECT id FROM {block_instances}
                                        WHERE blockname = 'reporttiles' ORDER BY id DESC",
                                        array(), IGNORE_MULTIPLE);
                $report = $DB->get_record('block_learnerscript', array('id' => $reportinstance['report']));
                require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
                $reportcontenttypes = (new ls)->cr_listof_reporttypes($reportinstance['report']);

                reset($reportcontenttypes);
                $firstkey = key($reportcontenttypes);
                $blockdata = new stdClass();
                $blockdata->title = $report->name;
                $blockdata->reportlist = $reportinstance['report'];
                $blockdata->reporttype = $reportinstance['Report Type'];
                $blockdata->logo = 0;
                $blockdata->contextlevel = $_SESSION['ls_contextlevel'];
                $DB->set_field('block_instances', 'configdata', base64_encode(serialize($blockdata)),
                    array('id' => $lastblockinstanceid));
                $i++;
            }
        }
    }

    public function delete_widget($deletereport, $blockinstanceid, $reportid = false) {
        global $DB, $CFG;
        $context = context_system::instance();
        if ($deletereport == 1) {
            $report = $DB->get_record('block_learnerscript',  array('id' => $reportid));
            if ($DB->delete_records('block_instances', array('blockname' => 'reportdashboard', 'id' => $blockinstanceid))) {
                (new ls)->delete_report($report, $context);
            }
            if (empty($_SESSION['role'])) {
              $redirecturl = $CFG->wwwroot.'/blocks/reportdashboard/dashboard.php';
            } else {
              $redirecturl = $CFG->wwwroot.'/blocks/reportdashboard/dashboard.php?role='.$_SESSION['role'];
            }
            header("Location: $redirecturl");
            die;
        } else if ($deletereport == 0) {
            if (empty($_SESSION['role'])) {
                $redirecturl = $CFG->wwwroot.'/blocks/reportdashboard/dashboard.php';
            } else {
                $redirecturl = $CFG->wwwroot.'/blocks/reportdashboard/dashboard.php?role='.$_SESSION['role'];
            }
            header("Location: $redirecturl");
            die;
        }
        return true;
    }

    public function is_dashboardempty($dashboardid) {
        global $DB, $USER;
        $sql = "SELECT configdata FROM {block_instances}
                 WHERE subpagepattern = :subpagepattern";
        $params = array();
        $params['subpagepattern'] = $dashboardid;
        $reportcount = 0;
        $blocksdata = $DB->get_fieldset_sql($sql, $params);
        foreach ($blocksdata as $key => $value) {
            $value = unserialize(base64_decode($value));
            $report = $DB->get_record('block_learnerscript', array('id' => $value->reportlist));
            if (isset($report->id)) {
                $haspermission = (new ls)->cr_check_report_permissions($report, $USER->id, context_system::instance());
                if ($haspermission) {
                    $reportcount++;
                }
            }
        }
        return ($reportcount > 0);
    }
    public function delete_dashboard_instances($role, $deletedashboard, $blockinstanceid = 0, $contextlevel) {
        global $DB, $PAGE;
        $pagetypepattern = 'blocks-reportdashboard-dashboard';
        if (!empty($role) && $contextlevel>10) {
            $pagetypepattern .= '-' . $role . '_' . $contextlevel;
        } else if(!empty($role)){
            $pagetypepattern .= '-' . $role;
        }

        $lsinstancessql = "SELECT id, id as instance
                             FROM {block_instances}
                            WHERE pagetypepattern = '$pagetypepattern'";

        if ($deletedashboard != 1) {
            $lsinstancessql .= " AND subpagepattern = '$deletedashboard' ";
        }
        if ($blockinstanceid > 0) {
            $lsinstancessql .= " AND id = $blockinstanceid ";
        }
        $lsinstances = $DB->get_records_sql_menu($lsinstancessql);
        if (!empty($lsinstances)) {
            blocks_delete_instances($lsinstances);
        }
        return true;
    }
}
