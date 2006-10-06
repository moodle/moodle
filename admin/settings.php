<?php // $Id$

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/pagelib.php');

if ($site = get_site()) {
    require_login();
}


page_map_class(PAGE_ADMIN, 'page_admin');

$PAGE = page_create_object(PAGE_ADMIN, 0); // there must be any constant id number

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

$CFG->pagepath = 'admin/setting/'.$section;

// WRITING SUBMITTED DATA (IF ANY) -------------------------------------------------------------------------------

if ($data = data_submitted()) {
    if (confirm_sesskey()) {
        $olddbsessions = !empty($CFG->dbsessions);
        $errors = $root->write_settings((array)$data);
        //force logout if dbsession setting changes
        if ($olddbsessions != !empty($CFG->dbsessions)) {
            require_logout();
        }
        if (empty($errors)) {
            switch ($return) {
                case 'site':
                    redirect("$CFG->wwwroot/");
                case 'admin':
                    redirect("$CFG->wwwroot/$CFG->admin/");
                default:
                    // following redirect should display confirmation message because it redirects
                    // to the same page, user might not know if save button worked
                    redirect("$CFG->wwwroot/$CFG->admin/settings.php?section=" . $PAGE->section, get_string('changessaved'),2);
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


if (!empty($SITE->fullname)) {
    $pageblocks = blocks_setup($PAGE);

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                            BLOCK_R_MAX_WIDTH);

    // print header stuff
    $PAGE->print_header();

    echo '<table id="layout-table"><tr>';
    echo '<td style="width: ' . $preferred_width_left . 'px;" id="left-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    echo '</td>';
    echo '<td id="middle-column">';
} else {

    print_header();
    print_simple_box(get_string('configintrosite', 'admin'), 'center', '50%');

}

echo '<form action="settings.php" method="post" name="adminsettings" id="adminsettings">';
echo '<input type="hidden" name="section" value="' . $PAGE->section . '" />';
echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
echo '<input type="hidden" name="return" value="' . $return . '" />';
print_heading($root->visiblename);

echo $root->output_html();

echo '<div class="form-buttons"><input class="form-submit" type="submit" value="' . get_string('savechanges','admin') . '" /></div>';

echo '</form>';

if (!empty($SITE->fullname)) {
    echo '</td>';
    echo '<td style="width: ' . $preferred_width_right . 'px;" id="right-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    echo '</td></tr></table>';
}

if (!empty($CFG->adminusehtmleditor)) {
    use_html_editor();
}

print_footer();

?>
