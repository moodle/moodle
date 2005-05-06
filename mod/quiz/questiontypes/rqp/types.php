<?php

// This page lists all the available RQP question types

    require_once('../../../../config.php');
    require_once('lib.php');

    // Check user admin
    require_login();
    if (!isadmin()) {
        error('You need to be an admin user to use this page.', $CFG->wwwroot . '/login/index.php');
    }

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

    $sesskey = required_param('sesskey', PARAM_RAW);
    if (!confirm_sesskey($sesskey)) {
        error(get_string('confirmsesskeybad', 'error'));
    }

    // Print the header
    $stradmin = get_string('admin');
    $strconfiguration = get_string('configuration');
    $strmanagemodules = get_string('managemodules');
    $strmodulename = get_string('modulename', 'quiz');
    $stritemtypes = get_string('itemtypes', 'quiz');
    $navigation = '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/index.php">' . $stradmin . '</a> -> ' .
    '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/configure.php">' . $strconfiguration . '</a> -> ' .
    '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/modules.php">' . $strmanagemodules . '</a> -> ' .
    '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/module.php?module=quiz&amp;sesskey=' . s(rawurlencode($sesskey)) . '">' . $strmodulename . '</a> -> ' .
    $stritemtypes;
    print_header($site->shortname . ': ' . $strmodulename . ': ' . $stritemtypes, $site->fullname, $navigation, '', '', true, '', '');

    // Get list of types
    $types = quiz_rqp_get_types();

    // Setup the table
    $strname = get_string('name');
    $strservers = get_string('servers', 'quiz');
    $strcapabilities = get_string('capabilities', 'quiz');
    $straction = get_string('action');
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $table->head  = array($strname, $strservers, $strcapabilities, $straction);
    $table->align = array('left', 'left', 'left', 'left');
    $table->size  = array('*', '*', '*', '*');

    if ($types) {
        foreach ($types as $type) {
            $link = '<a href="edittype.php?id=' . s(rawurlencode($type->id)) . '&amp;sesskey=' . s(rawurlencode($sesskey)) . '">' . s($type->name) . '</a>';
            $servers = get_string('rendering', 'quiz') . ': ' . s($type->rendering_server);
            if ($type->cloning_server) {
                $servers .= "<br />\n" . get_string('cloning', 'quiz') . ': ' . s($type->cloning_server);
            }
            $capabilities = '';
            $actions = '<a title="' . $stredit . '" href="edittype.php?id=' . s(rawurlencode($type->id)) . '&amp;sesskey=' . s(rawurlencode($sesskey)) . '"><img src="../../../../pix/t/edit.gif" border="0" alt="' . $stredit . '" /></a>&nbsp;<a title="' . $strdelete . '" href="edittype.php?delete=' . s(rawurlencode($type->id)) . '&amp;sesskey=' . s(rawurlencode($sesskey)) . '"><img src="../../../../pix/t/delete.gif" border="0" alt="' . $strdelete . '" /></a>';
            $table->data[] = array($link, $servers, $capabilities, $actions);
        }
    }

    // Print the table
    print_heading($stritemtypes);
    print_table($table);

    // Print add link
   echo '<p align="center"><a href="edittype.php?sesskey=' . s(rawurlencode($sesskey)) . '">' . get_string('additemtype', 'quiz') . "</a></p>\n";

    // Finish the page
    print_footer();

?>
