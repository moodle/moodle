<?php // $Id$

require_once('../config.php');
require_once($CFG->dirroot . '/admin/adminlib.php');

// for external pages, most of this code is duplicated in the admin_externalpage_print_header()
// and admin_externalpage_print_footer() functions... just include adminlib.php!
//
// lines marked //d at the end are handled (for other pages) by admin_externalpage_print_header()
// and admin_externalpage_print_footer()
require_once($CFG->libdir . '/blocklib.php'); //d
require_once($CFG->dirroot . '/admin/pagelib.php'); //d

if ($site = get_site()) { //d
    require_login(); //d
} //d

// Question: what pageid should be used for this?

define('TEMPORARY_ADMIN_PAGE_ID',26); //d

define('BLOCK_L_MIN_WIDTH',160); //d
define('BLOCK_L_MAX_WIDTH',210); //d

$pagetype = PAGE_ADMIN; //d
$pageclass = 'page_admin'; //d
page_map_class($pagetype, $pageclass); //d

$PAGE = page_create_object($pagetype,TEMPORARY_ADMIN_PAGE_ID); //d

$PAGE->init_full(); //d

unset($root); //d

$root = $ADMIN->locate($PAGE->section); //d

if (!($root instanceof admin_settingpage)) { //d
    error('Section does not exist, is invalid, or should not be accessed via this URL.'); //d
	die; //d
} //d

if (!($root->check_access())) { //d
    error('Access denied.'); //d
	die; //d
} //d

// WRITING SUBMITTED DATA (IF ANY) -------------------------------------------------------------------------------

if ($data = data_submitted()) {
    if (confirm_sesskey()) {
        $errors = $root->write_settings((array)$data);
        if (empty($errors)) {
    	    redirect("$CFG->wwwroot/admin/settings.php?section=" . $PAGE->section, get_string('changessaved'),1);
    	} else {
    	    error('The following errors occurred when trying to save settings: <br />' . $errors);
    	}
	} else {
	    error(get_string('confirmsesskeybad', 'error'));
		die;
	}
}

// ---------------------------------------------------------------------------------------------------------------

$pageblocks = blocks_setup($PAGE);

$preferred_width_left = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), BLOCK_L_MAX_WIDTH);

// print header stuff
$PAGE->print_header();
echo '<table id="layout-table"><tr>';
echo '<td style="width: ' . $preferred_width_left . 'px;" id="left-column">';
blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
echo '</td>';
echo '<td id="middle-column" width="*">';
echo '<form action="settings.php" method="post" name="mainform">';
echo '<input type="hidden" name="section" value="' . $PAGE->section . '" />';
echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
print_simple_box_start('','100%','',5,'generalbox','');

echo $root->output_html();

echo '<center><input type="submit" value="Save Changes" /></center>';
echo '</form>';
print_simple_box_end();
echo '</td></tr></table>'; //d

print_footer(); //d

?>