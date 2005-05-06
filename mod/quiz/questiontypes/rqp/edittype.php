<?php
    require_once('../../../../config.php');
    require_once('lib.php');
    require_once('remote.php');

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
    $type->id = optional_param('id', NULL, PARAM_INT);
    $type->name = optional_param('name', NULL, PARAM_RAW);
    if (is_null($type->name)) {
        $delete = optional_param('delete', NULL, PARAM_INT);
        if (!is_null($delete)) {
            $type->id = $delete;
            $confirm = optional_param('confirm', NULL, PARAM_RAW);
        }
    }
    else {
        $delete = NULL;
        $type->rendering_server = required_param('rendering_server', PARAM_RAW);
        $type->cloning_server   = required_param('cloning_server', PARAM_RAW);
    }

    // Print the header
    $stradmin           = get_string('admin');
    $strconfiguration   = get_string('configuration');
    $strmanagemodules   = get_string('managemodules');
    $strmodulename      = get_string('modulename', 'quiz');
    $stritemtypes       = get_string('itemtypes', 'quiz');
    $streditingitemtype = get_string(is_null($type->id) ? 'addingitemtype' : 'editingitemtype', 'quiz');
    $navigation = '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/index.php">' . $stradmin . '</a> -> ' .
    '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/configure.php">' . $strconfiguration . '</a> -> ' .
    '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/modules.php">' . $strmanagemodules . '</a> -> ' .
    '<a href="' . s($CFG->wwwroot) . '/' . s($CFG->admin) . '/module.php?module=quiz&amp;sesskey=' . s(rawurlencode($sesskey)) . '">' . $strmodulename . '</a> -> ' .
    '<a href="types.php?sesskey=' . s(rawurlencode($sesskey)) . '">' . $stritemtypes . '</a> -> ' .
    $streditingitemtype;
    print_header($site->shortname . ': ' . $strmodulename . ': ' . $stritemtypes . ': ' . $streditingitemtype, $site->fullname, $navigation, '', '', true, '', '');
    
    print_heading($streditingitemtype);

    $err = array();

    // Save the type
    while (!is_null($type->name)) { // using like if but with support for break
        // Check basic settings
        if (empty($type->name)) {
            $err['name'] = get_string('missingitemtypename', 'quiz');
            break;
        }
        if (empty($type->rendering_server)) {
            $err['rendering_server'] = get_string('missingrenderingserver', 'quiz');
            break;
        }
        // Check servers exist and work
        $servers = remote_server_info($type, true, !empty($type->cloning_server));
        if (false === $servers['rendering']) {
            $err['rendering_server'] = get_string('renderingserverconnectfailed', 'quiz', $type->rendering_server);
            break;
        }
        if (!empty($type->cloning_server)) {
            if (false === $servers['cloning']) {
                $err['cloning_server'] = get_string('cloningserverconnectfailed', 'quiz', $type->cloning_server);
                break;
            }
        }
        if (!$servers['rendering']->rendering) {
            $err['rendering_server'] = get_string('renderingserverdoesnt', 'quiz', $type->rendering_server);
            break;
        }
        if (!empty($type->cloning_server)) {
            if (!$servers['cloning']->cloning) {
                if ($type->cloning_server !== $type->rendering_server) {
                    $err['cloning_server'] = get_string('cloningserverdoesnt', 'quiz', $type->cloning_server);
                    break;
                }
                if (!$servers['rendering']->implicitCloning) {
                    $err['cloning_server'] = get_string('cloningrenderingserverdoesnt', 'quiz', $type->cloning_server);
                    break;
                }
            }
        }
        // Set the server specific flags
        $type->flags = 0;
        $type->flags |= $servers['rendering']->itemCaching ? REMOTE_ITEM_CACHE : 0;
        if (!empty($type->cloning_server)) {
            $type->flags |= $servers['cloning']->cloning ? REMOTE_CLONING : 0;
            if ($type->cloning_server === $type->rendering_server) {
                $type->flags |= $servers['rendering']->implicitCloning ? REMOTE_IMPLICIT_CLONING : 0;
            }
            $type->flags |= $servers['cloning']->templateCaching ? REMOTE_TEMPLATE_CACHE : 0;
        }
        // Save the type in the database
        if (!quiz_rqp_save_type($type)) {
            error('Could not save the item type', 'types.php?sesskey=' . s(rawurlencode($sesskey)));
        }
        redirect('types.php?sesskey=' . rawurlencode($sesskey), get_string('success'));
        // error and redirect both call exit so we never go round the loop
    }

    if (is_null($type->name)) {
        // Load the type
        if (is_null($type->id)) {
            $type->name             = '';
            $type->rendering_server = '';
            $type->cloning_server   = '';
        } else {
            if (! ($type = get_record('quiz_rqp_type', 'id', $type->id))) {
                error('Could not load the item type', 'types.php?sesskey=' . s(rawurlencode($sesskey)));
            }
        }
    }

    // Display the delete form
    if (!is_null($delete)) {
        if (md5($delete) === $confirm) {
            if (!quiz_rqp_delete_type($delete)) {
                error('Could not delete the item type', 'types.php?sesskey=' . s(rawurlencode($sesskey)));
            }
            redirect('types.php?sesskey=' . rawurlencode($sesskey), get_string('success'));
        }
        notice_yesno(get_string('deleteitemtypecheck', 'quiz', $type->name), 'edittype.php?delete=' . s(rawurlencode($delete)) . '&amp;confirm=' . s(rawurlencode(md5($delete))) . '&amp;sesskey=' . s(rawurlencode($sesskey)), 'types.php?sesskey=' . s(rawurlencode($sesskey)));
        print_footer();
        exit;
    }
?>

<form name="theform" method="post" action="edittype.php">
<center>
<table cellpadding="5">

<tr valign="top">
    <td align="right"><b><?php print_string('name'); ?>:</b>
    </td>
    <td>
        <input type="text" name="name" size="50" value="<?php  p($type->name); ?>" />
        <?php
            if (isset($err['name'])) {
                formerr($err['name']);
            }
        ?>
    </td>
</tr>

<tr valign="top">
    <td align="right"><b><?php print_string('renderingserver', 'quiz'); ?>:</b>
    </td>
    <td>
        <input type="text" name="rendering_server" size="50" value="<?php  p($type->rendering_server); ?>" />
        <?php
            if (isset($err['rendering_server'])) {
                formerr($err['rendering_server']);
            }
        ?>
    </td>
</tr>

<tr valign="top">
    <td align="right"><b><?php print_string('cloningserver', 'quiz'); ?>:</b>
    </td>
    <td>
        <input type="text" name="cloning_server" size="50" value="<?php  p($type->cloning_server); ?>" />
        <?php
            if (isset($err['cloning_server'])) {
                formerr($err['cloning_server']);
            }
        ?>
    </td>
</tr>

</table>

<input type="hidden" name="id" value="<?php p($type->id); ?>" />
<input type="hidden" name="sesskey" value="<?php p($sesskey); ?>" />
<input type="submit" value="<?php print_string('savechanges'); ?>" />

</center>
</form>

<?php
    print_footer();
?>
