<?php // $Id$

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/pagelib.php');

$section      = required_param('section', PARAM_SAFEDIR);
$return       = optional_param('return','', PARAM_ALPHA);
$adminediting = optional_param('adminedit', -1, PARAM_BOOL);

/// no guest autologin
require_login(0, false);

$adminroot =& admin_get_root(); // need all settings
$page      =& $adminroot->locate($section);

if (empty($page) or !is_a($page, 'admin_settingpage')) {
    print_error('sectionerror', 'admin', "$CFG->wwwroot/$CFG->admin/");
    die;
}

if (!($page->check_access())) {
    print_error('accessdenied', 'admin');
    die;
}

/// WRITING SUBMITTED DATA (IF ANY) -------------------------------------------------------------------------------

$statusmsg = '';
$errormsg  = '';
$focus = '';

if ($data = data_submitted() and confirm_sesskey()) {
    if (admin_write_settings($data)) {
        $statusmsg = get_string('changessaved');
    }

    if (empty($adminroot->errors)) {
        switch ($return) {
            case 'site':  redirect("$CFG->wwwroot/");
            case 'admin': redirect("$CFG->wwwroot/$CFG->admin/");
        }
    } else {
        $errormsg = get_string('errorwithsettings', 'admin');
        $firsterror = reset($adminroot->errors);
        $focus = $firsterror->id;
    }
    $adminroot =& admin_get_root(true); //reload tree
    $page      =& $adminroot->locate($section);
}

/// very hacky page setup
page_map_class(PAGE_ADMIN, 'page_admin');
$PAGE = page_create_object(PAGE_ADMIN, 0); // there must be any constant id number
$PAGE->init_extra($section);
$CFG->pagepath = 'admin/setting/'.$section;

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


/// print header stuff ------------------------------------------------------------

if (empty($SITE->fullname)) {
    print_header($page->visiblename, $page->visiblename, '', $focus);
    print_simple_box(get_string('configintrosite', 'admin'), 'center', '50%');

    if ($errormsg !== '') {
        notify ($errormsg);

    } else if ($statusmsg !== '') {
        notify ($statusmsg, 'notifysuccess');
    }

    // ---------------------------------------------------------------------------------------------------------------

    echo '<form action="settings.php" method="post" id="adminsettings">';
    echo '<div class="settingsform clearfix">';
    echo '<input type="hidden" name="section" value="'.$PAGE->section.'" />';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo '<input type="hidden" name="return" value="'.$return.'" />';

    echo $page->output_html();

    echo '<div class="form-buttons"><input class="form-submit" type="submit" value="'.get_string('savechanges','admin').'" /></div>';

    echo '</div>';
    echo '</form>';

} else {
    $pageblocks = blocks_setup($PAGE);

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                            BLOCK_R_MAX_WIDTH);

    $PAGE->print_header('', $focus);

    echo '<table id="layout-table"><tr>';
    $lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
    foreach ($lt as $column) {
        switch ($column) {
            case 'left':
    echo '<td style="width: '.$preferred_width_left.'px;" id="left-column">';
    print_container_start();
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    print_container_end();
    echo '</td>';
            break;
            case 'middle':
    echo '<td id="middle-column">';
    print_container_start();
    echo '<a name="startofcontent"></a>';

    if ($errormsg !== '') {
        notify ($errormsg);

    } else if ($statusmsg !== '') {
        notify ($statusmsg, 'notifysuccess');
    }

    // ---------------------------------------------------------------------------------------------------------------

    echo '<form action="settings.php" method="post" id="adminsettings">';
    echo '<div class="settingsform clearfix">';
    echo '<input type="hidden" name="section" value="'.$PAGE->section.'" />';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo '<input type="hidden" name="return" value="'.$return.'" />';
    print_heading($page->visiblename);

    echo $page->output_html();

    echo '<div class="form-buttons"><input class="form-submit" type="submit" value="'.get_string('savechanges','admin').'" /></div>';

    echo '</div>';
    echo '</form>';

    print_container_end();
    echo '</td>';
            break;
            case 'right':
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT)) {
        echo '<td style="width: '.$preferred_width_right.'px;" id="right-column">';
        print_container_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        print_container_end();
        echo '</td>';
    }
            break;
        }
    }
    echo '</tr></table>';
}

if (!empty($CFG->adminusehtmleditor)) {
    use_html_editor();
}

print_footer();

?>
