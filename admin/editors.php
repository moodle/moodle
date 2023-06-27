<?php

/**
 * Allows admin to configure editors.
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

$action  = required_param('action', PARAM_ALPHANUMEXT);
$editor  = required_param('editor', PARAM_PLUGIN);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/admin/editors.php', array('action'=>$action, 'editor'=>$editor));
$PAGE->set_context(context_system::instance());

require_admin();

$returnurl = "$CFG->wwwroot/$CFG->admin/settings.php?section=manageeditors";

// get currently installed and enabled auth plugins
$available_editors = editors_get_available();
if (!empty($editor) and empty($available_editors[$editor])) {
    redirect ($returnurl);
}

$active_editors = explode(',', $CFG->texteditors);
foreach ($active_editors as $key=>$active) {
    if (empty($available_editors[$active])) {
        unset($active_editors[$key]);
    }
}

////////////////////////////////////////////////////////////////////////////////
// process actions

if (!confirm_sesskey()) {
    redirect($returnurl);
}


$return = true;
switch ($action) {
    case 'disable':
        // remove from enabled list
        $key = array_search($editor, $active_editors);
        unset($active_editors[$key]);
        add_to_config_log('editor_visibility', '1', '0', $editor);
        break;

    case 'enable':
        // add to enabled list
        if (!in_array($editor, $active_editors)) {
            $active_editors[] = $editor;
            $active_editors = array_unique($active_editors);
            add_to_config_log('editor_visibility', '0', '1', $editor);
        }
        break;

    case 'down':
        $key = array_search($editor, $active_editors);
        // check auth plugin is valid
        if ($key !== false) {
            // move down the list
            if ($key < (count($active_editors) - 1)) {
                $fsave = $active_editors[$key];
                $active_editors[$key] = $active_editors[$key + 1];
                $active_editors[$key + 1] = $fsave;
                add_to_config_log('editor_position', $key, $key + 1, $editor);
            }
        }
        break;

    case 'up':
        $key = array_search($editor, $active_editors);
        // check auth is valid
        if ($key !== false) {
            // move up the list
            if ($key >= 1) {
                $fsave = $active_editors[$key];
                $active_editors[$key] = $active_editors[$key - 1];
                $active_editors[$key - 1] = $fsave;
                add_to_config_log('editor_position', $key, $key - 1, $editor);
            }
        }
        break;

    default:
        break;
}

// at least one editor must be active
if (empty($active_editors)) {
    $active_editors = array('textarea');
}

set_config('texteditors', implode(',', $active_editors));
core_plugin_manager::reset_caches();

if ($return) {
    redirect ($returnurl);
}
