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
 * Strings for component 'tool_checklearningrecords', language 'en', branch 'MOODLE_22_STABLE'
 *
 * @package    tool
 * @subpackage checklearningrecords
 * @copyright  2020 E-Learn Design https://www.e-learndesign
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Attempt to fix broken license LIT records.
 * 
 */
function do_fixbrokenlicenses($brokenlicenses) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/local/iomad_track/lib.php');

    $fixed = array();
    $stillbroken = array();

    foreach ($brokenlicenses as $brokenlicense) {
        // Check what is broken.
        if (empty($brokenlicense->licenseallocated)) {
            if ($licenseallocation = $DB->get_record('companylicense_users',
                                                     array('userid' => $brokenlicense->userid,
                                                          'licensecourseid' => $brokenlicense->courseid,
                                                          'timecompleted' => $brokenlicense->timecompleted))) {
                if (!empty($licenseallocation->issuedate)) {
                    $brokenlicense->licenseallocated = $licenseallocation->issuedate;
                } else {
                    $brokenlicense->licenseallocated = $brokenlicense->modifiedtime;
                    $licenseallocation->issuedate = $brokenlicense->modifiedtime;
                    $DB->update_record('companylicense_users', $licenseallocation);
                }
            } else {
                $brokenlicense->licenseallocated = $brokenlicense->timeenrolled;
            }
        }
        if (empty($brokenlicense->licenseid) || empty($brokenlicense->licensename)) {
            if (!empty($licenseallocation)) {
                if ($licenserec = $DB->get_record('companylicense', array('id' => $licenseallocation->licenseid))) {
                    $brokenlicense->licenseid = $licenseallocation->licenseid;
                    $brokenlicense->licensename = $licenserec->name;
                }
            } else {
                if (!empty($brokenlicense->licenseid)) {
                    if ($licenserec = $DB->get_record('companylicense', array('id' => $licenseallocation->licenseid))) {
                        $brokenlicense->licenseid = $licenseallocation->licenseid;
                        $brokenlicense->licensename = $licenserec->name;
                    }
                } else {
                    if (!empty($brokenlicense->licenseallocated)) {
                        if ($licenseallocation = $DB->get_record('companylicense_users',
                                                                  array('userid' => $brokenlicense->userid,
                                                                  'licensecourseid' => $brokenlicense->courseid,
                                                                  'timecompleted' => $brokenlicense->timecompleted,
                                                                  'issuedate' => $brokenlicense->issuedate))) {

                            if ($licenserec = $DB->get_record('companylicense', array('id' => $licenseallocation->licenseid))) {
                                $brokenlicense->licenseid = $licenseallocation->licenseid;
                                $brokenlicense->licensename = $licenserec->name;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($brokenlicense->licenseallocated) && !empty($brokenlicense->licenseid) && !empty($brokenlicense->licensename)) {
            $DB->update_record('local_iomad_track', $brokenlicense);
            $fixed[$brokenlicense->id] = $brokenlicense;
        } else {
            $stillbroken[$brokenlicense->id] = $brokenlicense;
        }
    }
    if (!CLI_SCRIPT) {
        echo "Fixed " . count($fixed) . " records. </br>";
        echo "Remaining = " . count($stillbroken) . " broken records. IDs:</br>" . join(',', array_keys($stillbroken)) . "</br>";
    } else {
        mtrace("Fixed " . count($fixed) . " records. </br>");
        mtrace("Remaining = " . count($stillbroken) . " broken records. IDs:</br>" . join(',', array_keys($stillbroken)) . "</br>");
    }
}

/**
 * Attempt to fix broken completion LIT records.
 * 
 */
function do_fixbrokencompletions($brokencompletions) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/local/iomad_track/lib.php');

    $fixed = array();
    $broken = array();

    foreach ($brokencompletions as $brokencompletion) {
        // Check what is broken.
        // Try and get the completion record.
        if ($comprecord = $DB->get_record('course_completions', array('userid' => $brokencompletion->userid,
                                                                      'course' => $brokencompletion->courseid,
                                                                      'timecompleted' => $brokencompletion->timecompleted))) {
            $brokencompletion->timestarted = $comprecord->timestarted;
            $brokencompletion->timecompleted = $comprecord->timecompleted;
            if (!empty($comprecord->timeenrolled)) {
                $brokencompletion->timeenrolled = $comprecord->timeenrolled;
            } else {
                // Need to get the actual enrolment time.
                if ($enrolrec = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                 JOIN {enrol} e ON (ue.enrolid = e.id)
                                                 WHERE ue.userid = :userid
                                                 AND e.courseid = :courseid
                                                 AND e.status = 0",
                                                 array('userid' => $brokencompletion->userid,
                                                       'courseid' => $brokencompletion->courseid))) {
                    $brokencompletion->timeenrolled = $enrolrec->timestart;
                } else {
                    if (!empty($brokencompletion->timestarted)) {
                        $brokencompletion->timeenrolled = $brokencompletion->timestarted;
                    }
                }
            }
        } else {
            if ($enrolrec = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                 JOIN {enrol} e ON (ue.enrolid = e.id)
                                                 WHERE ue.userid = :userid
                                                 AND e.courseid = :courseid
                                                 AND e.status = 0",
                                                 array('userid' => $brokencompletion->userid,
                                                       'courseid' => $brokencompletion->courseid))) {
                    if ($enrolrec->timestart < $brokencompletion->timecompleted || $enrolrec->timestart < $brokencompletion->timestarted) {
                        $brokencompletion->timeenrolled = $enrolrec->timestart;
                    }
                    if ($brokencompletion->timestarted == 0 ) {
                        $brokencompletion->timestarted = $enrolrec->timestart;
                    }
            } else {
                if (!empty($brokencompletion->timeenrolled) && empty($brokencompletion->timestarted)) {
                    $brokencompletion->timestarted = $brokencompletion->timeenrolled;
                }
                if (!empty($brokencompletion->timestarted) && empty($brokencompletion->timeenrolled)) {
                    $brokencompletion->timeenrolled = $brokencompletion->timestarted;
                }
            }
        }
        if (!empty($brokencompletion->timestarted) && !empty($brokencompletion->timeenrolled)) {
           $DB->update_record('local_iomad_track', $brokencompletion);
           $fixed[$brokencompletion->id] = $brokencompletion;
        } else {
            $broken[$brokencompletion->id] = $brokencompletion;
        }
    }
    if (!CLI_SCRIPT) {
        echo "Fixed " . count($fixed) . " records. </br>";
        echo "Remaining = " . count($broken) . " broken records. IDs:</br>" . join(',', array_keys($broken)) . "</br>";
    } else {
        mtrace("Fixed " . count($fixed) . " records. </br>");
        mtrace("Remaining = " . count($broken) . " broken records. IDs:</br>" . join(',', array_keys($broken)) . "</br>");
    }
}

/**
 * Attempt to fix missing completion LIT records.
 * 
 */
function do_fixmissingcompletions($missingcompletions) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/local/iomad_track/lib.php');

    $fixed = array();
    $broken = array();

    foreach ($missingcompletions as $missingcompletion) {
        $event = \core\event\course_completed::create(array(
                                                            'objectid' => $missingcompletion->ccid,
                                                            'relateduserid' => $missingcompletion->userid,
                                                            'context' => context_course::instance($missingcompletion->courseid),
                                                            'courseid' => $missingcompletion->courseid,
                                                            'other' => array('relateduserid' => $missingcompletion->userid)
                                                        ));
        $event->trigger();

    }
    if (!CLI_SCRIPT) {
        echo "Fired course completed even for " . count($missingcompletions) . " entries</br>";
    } else {
        mtrace("Fired course completed even for " . count($missingcompletions) . " entries</br>");
    }
}