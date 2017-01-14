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
$ajaxcap = optional_param('ajaxcap', '', PARAM_CLEAN);
$ajaxvalue = optional_param('ajaxvalue', '', PARAM_CLEAN);

// Set the companyid
// (before output in case it redirects)
$context = context_system::instance();
$companyid = iomad::get_my_companyid($context);

// access stuff
require_login();
iomad::require_capability('block/iomad_company_admin:restrict_capabilities', $context);

// check if ajax callback
if ($ajaxcap) {
    error_log('Got it '.$ajaxcap.' '.$ajaxvalue);
    $parts = explode('.', $ajaxcap);
    list($companyid, $roleid, $capability) = $parts;
    
    // if box is unticked (false) an entry is created (or kept)
    // if box is ticked (true) any entry is deleted
    $restriction = $DB->get_record('company_role_restriction', array(
            'roleid' => $roleid,
            'companyid' => $companyid,
            'capability' => $capability,
    ));
    if ($ajaxvalue=='false') {
        if (!$restriction) {
            $restriction = new stdClass();
            $restriction->companyid = $companyid;
            $restriction->roleid = $roleid;
            $restriction->capability = $capability;
            $DB->insert_record('company_role_restriction', $restriction);
        }
    } else {
        if ($restriction) {
            $DB->delete_records('company_role_restriction', array('id' => $restriction->id));
        }
    }
    reload_all_capabilities();
    die;
}

// Set the name for the page.
$linktext = get_string('restrictcapabilities', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_capabilities.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('name', 'local_iomad_dashboard') . " - $linktext");

$PAGE->requires->jquery();

// Correct the navbar.
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);
echo $OUTPUT->header();

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

if ($roleid) {
    $capabilities = iomad_company_admin::get_iomad_capabilities($roleid, $companyid);
    echo $output->capabilities($capabilities, $roleid, $companyid);
    echo $output->roles_button($linkurl);

} else {

    // get the list of roles to choose from
    $roles = iomad_company_admin::get_roles();
    echo $output->role_select($roles, $linkurl, $companyid);
}
?>
<script>
$(".checkbox").change(function() {
	$.post("<?php echo $linkurl; ?>", {
		ajaxcap:this.value,
		ajaxvalue:this.checked
	});
});
</script>
<?php
echo $OUTPUT->footer();
