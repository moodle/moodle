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

require_login();
require_capability('moodle/site:config', context_system::instance());

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
        break;

    case 'enable':
        // add to enabled list
        if (!in_array($editor, $active_editors)) {
            $active_editors[] = $editor;
            $active_editors = array_unique($active_editors);
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
            }
        }
        break;

    case 'uninstall':
        if ($editor === 'textarea') {
            redirect($returnurl);
        }
        if (get_string_manager()->string_exists('pluginname', 'editor_'.$editor)) {
            $strplugin = get_string('pluginname', 'editor_'.$editor);
        } else {
            $strplugin = $editor;
        }

        $PAGE->set_title($strplugin);
        echo $OUTPUT->header();

        if (!$confirm) {
            echo $OUTPUT->heading(get_string('editors', 'core_editor'));

            $deleteurl = new moodle_url('/admin/editors.php', array('action'=>'uninstall', 'editor'=>$editor, 'sesskey'=>sesskey(), 'confirm'=>1));

            echo $OUTPUT->confirm(get_string('editordeleteconfirm', 'core_editor', $strplugin),
                $deleteurl, $returnurl);
            echo $OUTPUT->footer();
            die();

        } else {
            // Remove from enabled list.
            $key = array_search($editor, $active_editors);
            unset($active_editors[$key]);
            set_config('texteditors', implode(',', $active_editors));

            // Delete everything!!
            uninstall_plugin('editor', $editor);

            $a = new stdClass();
            $a->name = $strplugin;
            $a->directory = "$CFG->dirroot/lib/editor/$editor";
            echo $OUTPUT->notification(get_string('plugindeletefiles', '', $a), 'notifysuccess');
            echo $OUTPUT->continue_button($returnurl);
            echo $OUTPUT->footer();
            die();
        }

    default:
        break;
}

// at least one editor must be active
if (empty($active_editors)) {
    $active_editors = array('textarea');
}

set_config('texteditors', implode(',', $active_editors));

if ($return) {
    redirect ($returnurl);
}
