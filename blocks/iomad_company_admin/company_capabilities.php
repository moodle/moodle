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
 * Control company capabilities.
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');

// parameters
$roleid = optional_param('roleid', 0, PARAM_INT);

// access stuff
$context = context_system::instance();
require_login();
require_capability('block/iomad_company_admin:restrict_capabilities', $context);
$PAGE->set_context($context);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('restrictcapabilities', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Do mysterious blockpage thingy
$blockpage = new blockpage($PAGE, $OUTPUT, 'iomad_company_admin', 'block', 'restrictcapabilities');
$blockpage->setup();
$blockpage->display_header();

// Set the companyid
$companyid = iomad::get_my_companyid($context);

if ($roleid) {
    $capabilities = iomad_company_admin::get_iomad_capabilities($roleid);
    echo "<pre>"; var_dump($capabilities); die;
    
} else {
    
    // get the list of roles to choose from
    $roles = iomad_company_admin::get_roles();
    echo $output->role_select($roles, $linkurl);
}

echo $OUTPUT->footer();