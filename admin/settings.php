<?php // $Id$

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/pagelib.php');

if ($site = get_site()) {
    require_login();
} 

define('BLOCK_L_MIN_WIDTH',160);
define('BLOCK_L_MAX_WIDTH',210);

page_map_class(PAGE_ADMIN, 'page_admin');

$PAGE = page_create_object(PAGE_ADMIN, 0); // there must be some id number

$section = optional_param('section', '', PARAM_ALPHAEXT);

$PAGE->init_extra($section); // hack alert!

$adminediting = optional_param('adminedit', -1, PARAM_BOOL);
$return       = optional_param('return','', PARAM_ALPHA);
   
if (!isset($USER->adminediting)) {
    $USER->adminediting = false;
}

if ($PAGE->user_allowed_editing()) {
    if ($adminediting == 1) {
        $USER->adminediting = true;
    } elseif ($adminediting == 0) {
        $USER->adminediting = false;
    }
}

$adminroot = admin_get_root();

$root = $adminroot->locate($PAGE->section);

if (!is_a($root, 'admin_settingpage')) {
    error(get_string('sectionerror', 'admin'));
    die;
}

if (!($root->check_access())) {
    error(get_string('accessdenied', 'admin'));
    die;
}

// WRITING SUBMITTED DATA (IF ANY) -------------------------------------------------------------------------------

if ($data = data_submitted()) {
    if (confirm_sesskey()) {
        $errors = $root->write_settings((array)$data);
        if (empty($errors)) {
            switch ($return) {
                case 'site':
                    redirect("$CFG->wwwroot/", get_string('changessaved'),1);
                case 'admin':
                    redirect("$CFG->wwwroot/admin/", get_string('changessaved'),1);
                default:
                    redirect("$CFG->wwwroot/admin/settings.php?section=" . $PAGE->section, get_string('changessaved'),1);
            }
        } else {
            error(get_string('errorwithsettings', 'admin') . ' <br />' . $errors);
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
echo '<input type="hidden" name="return" value="' . $return . '" />';
print_heading($root->visiblename);
print_simple_box_start('','100%','',5,'generalbox','');

echo $root->output_html();

echo '<center><input type="submit" value="Save Changes" /></center>';
print_simple_box_end();
echo '</form>';
echo '</td></tr></table>';

print_footer();

?>