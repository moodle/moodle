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
 * @copyright 2022 Derick Turner
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
class user_roles_editable extends \core\output\inplace_editable {

    /** @var $context */
    private $context = null;

    /** @var \stdClass[] $viewableroles */
    private $roles;

    /** @var \stdClass[] $viewableroles */
    private $userroles;

    /** @var \stdClass[] $viewableroles */
    private $viewableroles;

    /** @var \stdClass[] $assignableroles */
    private $assignableroles;

    /**
     * Constructor.
     *
     * @param \stdClass $course The current course
     * @param \context $context The course context
     * @param \stdClass $user The current user
     * @param \stdClass[] $courseroles The list of course roles.
     * @param \stdClass[] $assignableroles The list of assignable roles in this course.
     * @param \stdClass[] $profileroles The list of roles that should be visible in a users profile.
     * @param \stdClass[] $userroles The list of user roles.
     */
    public function __construct($company, $context, $user, $currentvalue, $assignableroles = null) {
        if (empty($assignableroles)) {
            debugging('Constructor for user_roles_editable now needs to be passed the roles available to the manager');
        }

        // Check capabilities to get editable value.
        $editable = iomad::has_capability('block/iomad_company_admin:company_manager', $context);

        // Invent an itemid.
        $itemid = $company->id . ':' . $user->id;

        $value = $currentvalue;

        // Remember these for the display value.
        $this->assignableroles = $assignableroles;
        $this->context = $context;

        parent::__construct('block_iomad_company_admin', 'user_roles', $itemid, $editable, $value, $value);

        $this->edithint = get_string('xrolesassignments', 'block_iomad_company_admin', fullname($user));
        $this->editlabel = get_string('xrolesassignments', 'block_iomad_company_admin', fullname($user));

        $this->set_type_select($assignableroles);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $listofroles = [];
        $role = json_decode($this->value);

        
        if ($this->editable || array_key_exists($role, $this->profileroles)) {
            $listofroles[] = format_string($this->assignableroles[$role], true, ['context' => $this->context]);
        }

        if (!empty($listofroles)) {
            $this->displayvalue = format_string($this->assignableroles[$role], true, ['context' => $this->context]);
        } else if (!empty($rolesids) && empty($viewablerolesids)) {
            $this->displayvalue = get_string('novisibleroles', 'block_iomad_company_admin');
        } else {
            $this->displayvalue = get_string('noroles', 'block_iomad_company_admin');
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
        $roleid = json_decode($newvalue);
        $user = core_user::get_user($userid);

        // Check user is enrolled in the course.
        $context = \context_system::instance();
        core_external::validate_context($context);

        // Check permissions.
        iomad::require_capability('block/iomad_company_admin:editusers', $context);

        if (!$DB->get_records('company_users', ['userid' => $userid, 'companyid' => $companyid])) {
            throw new coding_exception('User does not belong to the company');
        }

        // Check that all the roles belong to the company.
        $company = new company($companyid);

        // Deal with role selector.
        $usertypeselect = ['0' => get_string('user', 'block_iomad_company_admin')];
        if (iomad::has_capability('block/iomad_company_admin:assign_company_manager', $context)) {
            $usertypeselect[1] = get_string('companymanager', 'block_iomad_company_admin');
        }
        if (iomad::has_capability('block/iomad_company_admin:assign_department_manager', $context)) {
            $usertypeselect[2] = get_string('departmentmanager', 'block_iomad_company_admin');
        }
        if (iomad::has_capability('block/iomad_company_admin:assign_company_reporter', $context)) {
            $usertypeselect[4] = get_string('companyreporter', 'block_iomad_company_admin');
        }
        if (!$CFG->iomad_autoenrol_managers && iomad::has_capability('block/iomad_company_admin:assign_educator', $context)) {
            $usertypeselect[10] = get_string('educator', 'block_iomad_company_admin');
            if (iomad::has_capability('block/iomad_company_admin:assign_company_manager', $context)) {
                $usertypeselect[11] = get_string('educator', 'block_iomad_company_admin') . ' + ' . get_string('companymanager', 'block_iomad_company_admin');
            }
            if (iomad::has_capability('block/iomad_company_admin:assign_department_manager', $context)) {
                $usertypeselect[12] = get_string('educator', 'block_iomad_company_admin') . ' + ' . get_string('departmentmanager', 'block_iomad_company_admin');
            }
            if (iomad::has_capability('block/iomad_company_admin:assign_company_reporter', $context)) {
                $usertypeselect[14] = get_string('educator', 'block_iomad_company_admin') . ' + ' . get_string('companyreporter', 'block_iomad_company_admin');
            }
        }

        if (!isset($usertypeselect[$roleid])) {
            throw new coding_exception('roles cannot be assigned in this course.');
        }

        // Process changes.
        $userlevels = $DB->get_records('company_users', ['companyid' => $companyid, 'userid' => $userid]);
        if ($roleid > 9) {
            $educator = 1;
            $managertype = $roleid - 10;
        } else {
            $educator = 0;
            $managertype = $roleid;
        }

        foreach ($userlevels as $userlevel) {
            company::upsert_company_user($userid, $company->id, $userlevel->departmentid, $managertype, $educator, false);
        }

        return new self($company, $context, $user, $roleid, $usertypeselect);
    }
}
