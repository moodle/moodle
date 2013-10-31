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
 * @package    Block Approve Enroll
 * @copyright  2011 onwards E-Learn Design Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Checks if the current user has any outstanding approvals.
 *
 * returns Boolean
 *
 **/
function approve_enrol_has_users() {
    global $CFG, $DB, $USER, $SESSION;
    require_once($CFG->dirroot.'/local/perficio/lib/company.php');
    require_once($CFG->dirroot.'/local/perficio/lib/user.php');

    // Set the companyid to bypass the company select form if possible.
    if (!empty($SESSION->currenteditingcompany)) {
        $companyid = $SESSION->currenteditingcompany;
    } else if (!empty($USER->company)) {
        $companyid = company_user::companyid();
    } else {
        return false;
    }

    // Check if we can have users of my type.
    if (is_siteadmin($USER->id)) {
        $approvaltype = 'both';
    } else {
        // What type of manager am I?
        if ($manager = $DB->get_record('company_users', array('userid' => $USER->id))) {
            if (!empty($manager->managertype) && $manager->managertype == 1)) {
                $approvaltype = 'manager';
            } else if (!empty($manager->managertype) && $manager->managertype == 1)) {
                $approvaltype = 'company';
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    if ($approvaltype == 'both' || $approvaltype == 'manager') {
        // Get the list of users I am responsible for.
        $myuserids = company::get_my_users_list($companyid);
        if (!empty($myuserids) && $DB->get_records_sql("SELECT iaa.* FROM {block_iomad_approve_access} iaa
                                               RIGHT JOIN {courseclassroom} cc ON cc.id=iaa.activityid
                                               AND cc.approvaltype in (1,3)
                                               WHERE iaa.companyid=:companyid AND iaa.manager_ok = 0
                                               AND iaa.userid != :myuserid
                                               AND iaa.userid
                                               IN ($myuserids)", array('companyid'=>$companyid, 'myuserid'=>$USER->id)) {
            return true;
        }
    }
    if ($approvaltype == 'both' || $approvaltype == 'company') {
        if (!empty($myuserids) && $DB->get_records_sql("SELECT iaa.* FROM {block_iomad_approve_access} iaa
                                  RIGHT JOIN {courseclassroom} cc ON cc.id=iaa.activityid
                                  WHERE iaa.companyid=:companyid
                                  AND iaa.userid != :myuserid
                                  AND iaa.userid IN ($myuserids)
                                  AND (
                                   cc.approvaltype in (2,3)
                                   AND iaa.tm_ok = 0 )
                                  OR (
                                   cc.approvaltype = 1
                                   AND iaa.manager_ok = 0)", array('companyid'=>$companyid, 'myuserid'=>$USER->id))) {
            return true;
        }
    }

    // Hasn't returned yet, return false as default.
    return false;
}

/**
 * Gets the list of outstanding approvals for the current user.
 *
 * returns array
 *
 **/
function approve_enroll_get_my_users() {
    global $CFG, $DB, $USER, $SESSION;

    require_once($CFG->dirroot.'/local/perficio/lib/company.php');
    require_once($CFG->dirroot.'/local/perficio/lib/user.php');

    // Set the companyid to bypass the company select form if possible.
    if (!empty($SESSION->currenteditingcompany)) {
        $companyid = $SESSION->currenteditingcompany;
    } else if (!empty($USER->company)) {
        $companyid = company_user::companyid();
    } else {
        return false;
    }

    // Check if we can have users of my type.
    if (is_siteadmin($USER->id)) {
        $approvaltype = 'both';
    } else {
        // What type of manager am I?
        if ($manager = $DB->get_record('company_users', array('userid' => $USER->id))) {
            if (!empty($manager->managertype) && $manager->managertype == 1)) {
                $approvaltype = 'manager';
            } else if (!empty($manager->managertype) && $manager->managertype == 1)) {
                $approvaltype = 'company';
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Get the list of users I am responsible for.
    $myuserids = company::get_my_users_list($companyid);
    if (!empty($myuserids)) {
        if ($approvaltype == 'manager') {
            //  need to deal with departments here.
            if ($userarray = $DB->get_records_sql("SELECT iaa.* FROM {block_iomad_approce_access} iaa
                                               RIGHT JOIN {courseclassroom} cc ON cc.id=iaa.activityid
                                               AND cc.approvaltype in (1,3)
                                               WHERE iaa.companyid=:companyid AND iaa.manager_ok = 0
                                               AND iaa.userid != :myuserid
                                               AND iaa.userid
                                               IN ($myuserids)", array('companyid'=>$companyid, 'myuserid'=>$USER->id))) {
                return $userarray;
            }
        }

        if ($approvaltype == 'company') {
            if ($userarray = $DB->get_records_sql("SELECT iaa.* FROM {block_iomad_approce_access} iaa
                                               RIGHT JOIN {courseclassroom} cc ON cc.id=iaa.activityid
                                               WHERE iaa.companyid=:companyid
                                               AND iaa.userid != :myuserid
                                               AND iaa.userid IN ($myuserids)
                                               AND (
                                                cc.approvaltype in (2,3)
                                                AND iaa.tm_ok = 0 )
                                               OR (
                                                cc.approvaltype = 1
                                                AND iaa.manager_ok = 0)", array('companyid'=>$companyid, 'myuserid'=>$USER->id))) {
                return $userarray;
            }
        }

        if ($approvaltype == 'both') {
            if ($userarray = $DB->get_records_sql("SELECT * FROM {block_iomad_approce_access}
                                                   WHERE companyid=:companyid
                                                   AND (tm_ok = 0 OR manager_ok = 0)
                                                   AND userid != :myuserid
                                                   AND userid IN ($myuserids)",
                                                   array('companyid'=>$companyid, 'myuserid'=>$USER->id))) {
                return $userarray;
            }
        }
    }
    }

    return array();
}

/**
 * Assigns an approved user to a training event.
 *
 * Inputs-
 *        $user = stdclass();
 *        $event = stdclass();
 *
 **/
function approve_access_register_user($user, $event) {
    global $DB;

    $courseclassroomrecord = new stdclass();
    $courseclassroomrecord->userid = $user->id;
    $courseclassroomrecord->courseclassroomid = $event->id;
    if (!$DB->insert_record('courseclassroom_users', $courseclassroomrecord)) {
        print_error(get_string('updatefailed', 'block_iomad_approve_access'));
    }
}
