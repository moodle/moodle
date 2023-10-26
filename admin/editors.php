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
        // Remove from enabled list.
        $class = \core_plugin_manager::resolve_plugininfo_class('editor');
        $class::enable_plugin($editor, false);
        break;

    case 'enable':
        // Add to enabled list.
        if (!in_array($editor, $active_editors)) {
            $class = \core_plugin_manager::resolve_plugininfo_class('editor');
            $class::enable_plugin($editor, true);
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
                set_config('texteditors', implode(',', $active_editors));
                core_plugin_manager::reset_caches();
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
                set_config('texteditors', implode(',', $active_editors));
                core_plugin_manager::reset_caches();
            }
        }
        break;

    default:
        break;
}

if ($return) {
    redirect ($returnurl);
}
