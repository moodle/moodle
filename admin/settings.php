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



/// WRITING SUBMITTED DATA (IF ANY) -------------------------------------------------------------------------------

$statusmsg = '';

if ($data = data_submitted()) {
    if (confirm_sesskey()) {
        $olddbsessions = !empty($CFG->dbsessions);
        $unslashed = (array)stripslashes_recursive($data);
        $errors = $root->write_settings($unslashed);
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
                    $statusmsg = get_string('changessaved');
            }
        } else {
            $statusmsg = get_string('errorwithsettings', 'admin') . ' <br />' . $errors;
        }
    } else {
        error(get_string('confirmsesskeybad', 'error'));
    }
    // now update $SITE - it might have been changed
    $SITE = get_record('course', 'id', $SITE->id);
    $COURSE = clone($SITE);
}


/// print header stuff ------------------------------------------------------------
// header must be printed after the redirects and require_logout

if (empty($SITE->fullname)) {
    print_header();
    print_simple_box(get_string('configintrosite', 'admin'), 'center', '50%');

    if ($statusmsg != '') {
        notify ($statusmsg);
    }

    // ---------------------------------------------------------------------------------------------------------------

    echo '<form action="settings.php" method="post" id="adminsettings">';
    echo '<div class="settingsform">';
    echo '<input type="hidden" name="section" value="' . $PAGE->section . '" />';
    echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
    echo '<input type="hidden" name="return" value="' . $return . '" />';
    print_heading($root->visiblename);

    echo $root->output_html();

    echo '<div class="form-buttons"><input class="form-submit" type="submit" value="' . get_string('savechanges','admin') . '" /></div>';

    echo '</div>';
    echo '</form>';
}

if (!empty($SITE->fullname)) {
    $pageblocks = blocks_setup($PAGE);

    $preferred_width_left  = bounded_number(BLOCK_L_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]),
                                            BLOCK_L_MAX_WIDTH);
    $preferred_width_right = bounded_number(BLOCK_R_MIN_WIDTH, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]),
                                            BLOCK_R_MAX_WIDTH);

    $PAGE->print_header();

    echo '<table id="layout-table"><tr>';
    $lt = (empty($THEME->layouttable)) ? array('left', 'middle', 'right') : $THEME->layouttable;
    foreach ($lt as $column) {
        switch ($column) {
            case 'left':
    echo '<td style="width: ' . $preferred_width_left . 'px;" id="left-column">';
    if (!empty($THEME->roundcorners)) {
        echo '<div class="bt"><div></div></div>';
        echo '<div class="i1"><div class="i2"><div class="i3">';
    }
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    if (!empty($THEME->roundcorners)) {
        echo '</div></div></div>';
        echo '<div class="bb"><div></div></div>';
    }
    echo '</td>';
            break;
            case 'middle':
    echo '<td id="middle-column">';
    if (!empty($THEME->roundcorners)) {
        echo '<div class="bt"><div></div></div>';
        echo '<div class="i1"><div class="i2"><div class="i3">';
    }
    echo '<a name="startofcontent"></a>';

    if ($statusmsg != '') {
        notify ($statusmsg);
    }

    // ---------------------------------------------------------------------------------------------------------------

    echo '<form action="settings.php" method="post" id="adminsettings">';
    echo '<div class="settingsform">';
    echo '<input type="hidden" name="section" value="' . $PAGE->section . '" />';
    echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
    echo '<input type="hidden" name="return" value="' . $return . '" />';
    print_heading($root->visiblename);

    echo $root->output_html();

    echo '<div class="form-buttons"><input class="form-submit" type="submit" value="' . get_string('savechanges','admin') . '" /></div>';

    echo '</div>';
    echo '</form>';

    if (!empty($THEME->roundcorners)) {
        echo '</div></div></div>';
        echo '<div class="bb"><div></div></div>';
    }
    echo '</td>';
            break;
            case 'right':
    if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT)) {
        echo '<td style="width: ' . $preferred_width_right . 'px;" id="right-column">';
        if (!empty($THEME->roundcorners)) {
            echo '<div class="bt"><div></div></div>';
            echo '<div class="i1"><div class="i2"><div class="i3">';
        }
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        if (!empty($THEME->roundcorners)) {
            echo '</div></div></div>';
            echo '<div class="bb"><div></div></div>';
        }
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
