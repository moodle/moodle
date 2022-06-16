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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\output;

use context_course;
use core_user;
use core_external;
use coding_exception;
use company;
use iomad;

defined('MOODLE_INTERNAL') || die();

/**
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_departments_editable extends \core\output\inplace_editable {

    /** @var $context */
    private $context = null;

    /** @var \stdClass[] $coursedepartments */
    private $coursedepartments;

    /** @var \stdClass[] $profiledepartments */
    private $profiledepartments;

    /** @var \stdClass[] $viewabledepartments */
    private $viewabledepartments;

    /** @var \stdClass[] $assignabledepartments */
    private $assignabledepartments;

    /**
     * Constructor.
     *
     * @param \stdClass $course The current course
     * @param \context $context The course context
     * @param \stdClass $user The current user
     * @param \stdClass[] $coursedepartments The list of course departments.
     * @param \stdClass[] $assignabledepartments The list of assignable departments in this course.
     * @param \stdClass[] $profiledepartments The list of departments that should be visible in a users profile.
     * @param \stdClass[] $userdepartments The list of user departments.
     */
    public function __construct($company, $context, $user, $userdepartments, $departments, $assignabledepartments = null) {
        if (empty($assignabledepartments)) {
            debugging('Constructor for user_departments_editable now needs to be passed the departments available to the manager');
        }

        // Check capabilities to get editable value.
        $editable = iomad::has_capability('block/iomad_company_admin:editusers', $context);

        // Invent an itemid.
        $itemid = $company->id . ':' . $user->id;

        $ids = array_values($userdepartments);

        $value = json_encode($ids);

        // Remember these for the display value.
        $this->userdepartments = $userdepartments;
        $this->departments = $departments;
        $this->assignabledepartments = $assignabledepartments;
        $this->viewabledepartments = array_keys($assignabledepartments);
        $this->context = $context;

        parent::__construct('block_iomad_company_admin', 'user_departments', $itemid, $editable, $value, $value);

        $this->edithint = get_string('xdepartmentassignments', 'block_iomad_company_admin', fullname($user));
        $this->editlabel = get_string('xdepartmentassignments', 'block_iomad_company_admin', fullname($user));

        $attributes = ['multiple' => true];
        $this->set_type_autocomplete($assignabledepartments, $attributes);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $listofdepartments = [];
        $departmentids = json_decode($this->value);

        $viewabledepartmentids = array_intersect($departmentids, array_values($this->userdepartments));

        foreach ($viewabledepartmentids as $id) {
            // If this is a student, we only show a subset of the departments.
            if ($this->editable || array_key_exists($id, $this->profiledepartments)) {
                $listofdepartments[] = format_string($this->assignabledepartments[$id], true, ['context' => $this->context]);
            }
        }

        if (!empty($listofdepartments)) {
            $this->displayvalue = implode(', ', $listofdepartments);
        } else if (!empty($departmentids) && empty($viewabledepartmentids)) {
            $this->displayvalue = get_string('novisibledepartments', 'block_iomad_company_admin');
        } else {
            $this->displayvalue = get_string('nodepartments', 'block_iomad_company_admin');
        }

        return parent::export_for_template($output);
    }

    /**
     * Updates the value in database and returns itself, called from inplace_editable callback
     *
     * @param int $itemid
     * @param mixed $newvalue
     * @return \self
     */
    public static function update($itemid, $newvalue) {
        global $DB, $CFG, $USER;

        require_once($CFG->libdir . '/external/externallib.php');
        // Check caps.
        // Do the thing.
        // Return one of me.
        // Validate the inputs.
        list($companyid, $userid) = explode(':', $itemid, 2);

        $companyid = clean_param($companyid, PARAM_INT);
        $company = new company($companyid);
        $userid = clean_param($userid, PARAM_INT);
        $departmentids = json_decode($newvalue);
        foreach ($departmentids as $index => $departmentid) {
            $departmentids[$index] = clean_param($departmentid, PARAM_INT);
        }

        // Check user is enrolled in the course.
        $context = \context_system::instance();
        core_external::validate_context($context);

        // Check permissions.
        iomad::require_capability('block/iomad_company_admin:editusers', $context);

        if (!$DB->get_records('company_users', ['userid' => $userid, 'companyid' => $companyid])) {
            throw new coding_exception('User does not belong to the company');
        }

        // Check that all the departments belong to the company.
        $company = new company($companyid);
        $alldepartments = $DB->get_records('department', ['company' => $companyid]);
        $parentlevel = company::get_company_parentnode($companyid); 
        if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', \context_system::instance())) {
            $userlevels = array($parentlevel->id => $parentlevel->id);
        } else {
            $userlevels = $company->get_userlevel($USER);
        }

        $departmenttree = [];
        foreach ($userlevels as $userlevelid => $userlevel) {
            $departmenttree[] = company::get_all_subdepartments_raw($userlevelid);
        }

        $assignabledepartments = company::array_flatten(company::get_department_list($departmenttree[0]));

        $userdepartmentsbyid = $DB->get_records_sql("SELECT d.id, d.name FROM {department} d
                                                     JOIN {company_users} cu ON (d.id = cu.departmentid AND d.company = cu.companyid)
                                                     WHERE cu.companyid = :companyid
                                                     AND cu.userid = :userid
                                                     ORDER BY d.name",
                                                     ['companyid' => $companyid, 'userid' => $userid]);

        // Set an array where the index is the departmentid.
        $userdepartments = array();
        $managertype = 0;
        $educator = 0;
        foreach ($userdepartmentsbyid as $id => $department) {
            $userdepartments[$id] = $department;
            if (!empty($department->managertype)) {
                $managertype = $department->managertype;
            }
            if (!empty($department->educator)) {
                $educator = $department->educator;
            }
        }

        $departmentstoprocess = [];
        foreach ($departmentids as $departmentid) {
            if (!isset($assignabledepartments[$departmentid])) {
                throw new coding_exception('department cannot be assigned in this course.');
            }
            $departmentstoprocess[$departmentid] = $departmentid;
        }

        // Process adds.
        foreach ($departmentstoprocess as $departmentid) {
            if (!isset($userdepartments[$departmentid])) {
                // Add them.
                company::upsert_company_user($userid, $company->id, $departmentid, $managertype, $educator, false);
                // Keep this variable in sync.
                $department = new \stdClass();
                $department->id = $id;
                $department->departmentid = $departmentid;
                $userdepartments[$department->departmentid] = $department;
            }
        }

        // Process removals.
        foreach ($userdepartments as $departmentid => $departmentname) {
            if (isset($userdepartments[$departmentid]) && !isset($departmentstoprocess[$departmentid])) {
                // Remove them.
                $DB->delete_records('company_users', ['userid' => $userid, 'companyid' => $company->id, 'departmentid' => $departmentid]);
                unset($userdepartments[$departmentid]);
            }
        }
        //  Has the user been removed from all departments?
        if (empty($userdepartments)) {
            // Assign them to the top level department.
            company::upsert_company_user($userid, $company->id, $parentlevel->id, $managertype, $educator, false);
            $departmentids[$parentlevel->id] = $parentlevel->id;
        }

        $user = core_user::get_user($userid);
        return new self($company, $context, $user, $departmentids, $assignabledepartments, $assignabledepartments);
    }
}
