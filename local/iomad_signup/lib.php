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

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/local/iomad/lib/company.php');

/**
 * Event handler for 'user_created'
 * For 'email' authentication (only) add this user
 * to the defined role and company.
 */
function local_iomad_signup_user_created($user) {
    global $CFG, $DB;

    // the plugin needs to be enabled
    if (!$CFG->local_iomad_signup_enable) {
        return true;
    }

    // If not 'email' auth then we are not interested
    if (!in_array($user->auth, explode(',', $CFG->local_iomad_signup_auth))) {
        return true;
    }

    // Get context
    $context = context_system::instance();

    // Check if we have a domain already for this users email address.
    if ($domaininfo = $DB->get_record('company_domains', array('domain' => substr(strrchr($user->email, "@"), 1)))) {
        // Get company.
        $company = new company($domaininfo->companyid);

        // assign the user to the company.
        $company->assign_user_to_company($user->id);
    } else if (!empty($CFG->local_iomad_signup_company)) {
        // Do we have a company to assign?
        // Get company.
        $company = new company($CFG->local_iomad_signup_company);

        // assign the user to the company.
        $company->assign_user_to_company($user->id);
    }
    
    // Do we have a role to assign?
    if (!empty($CFG->local_iomad_signup_role)) {
        // Get role
        if ($role = $DB->get_record('role', array('id' => $CFG->local_iomad_signup_role), '*', MUST_EXIST)) {

            // assign the user to the role
            role_assign($role->id, $user->id, $context->id);
        }
    }

    return true;
}
