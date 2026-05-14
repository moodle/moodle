<?php

/**
 * Allows admin to edit all auth plugin settings.
 *
 * JH: copied and Hax0rd from admin/enrol.php and admin/filters.php
 *
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');

require_admin();

$returnurl = new moodle_url('/admin/settings.php', array('section'=>'manageauths'));

$PAGE->set_url($returnurl);

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$auth   = optional_param('auth', '', PARAM_PLUGIN);

// Fix the list of enabled auths.
$authhelper = \core\di::get(\core\authentication::class);
$authhelper->get_enabled_plugins(true);
if (empty($CFG->auth)) {
    $authsenabled = [];
} else {
    $authsenabled = explode(',', $CFG->auth);
}

if (!empty($auth) && !$authhelper->plugin_exists($auth)) {
    throw new \moodle_exception('pluginnotinstalled', 'auth', $returnurl, $auth);
}

// Process the actions.

if (!confirm_sesskey()) {
    redirect($returnurl);
}

switch ($action) {
    case 'disable':
        // Remove from enabled list.
        $class = \core_plugin_manager::resolve_plugininfo_class('auth');
        $class::enable_plugin($auth, false);
        break;

    case 'enable':
        // Add to enabled list.
        $class = \core_plugin_manager::resolve_plugininfo_class('auth');
        $class::enable_plugin($auth, true);
        break;

    case 'down':
        $key = array_search($auth, $authsenabled);
        // check auth plugin is valid
        if ($key === false) {
            throw new \moodle_exception('pluginnotenabled', 'auth', $returnurl, $auth);
        }
        // move down the list
        if ($key < (count($authsenabled) - 1)) {
            $fsave = $authsenabled[$key];
            $authsenabled[$key] = $authsenabled[$key + 1];
            $authsenabled[$key + 1] = $fsave;
            $value = implode(',', $authsenabled);
            add_to_config_log('auth', $CFG->auth, $value, 'core');
            set_config('auth', $value);
        }
        break;

    case 'up':
        $key = array_search($auth, $authsenabled);
        // check auth is valid
        if ($key === false) {
            throw new \moodle_exception('pluginnotenabled', 'auth', $returnurl, $auth);
        }
        // move up the list
        if ($key >= 1) {
            $fsave = $authsenabled[$key];
            $authsenabled[$key] = $authsenabled[$key - 1];
            $authsenabled[$key - 1] = $fsave;
            $value = implode(',', $authsenabled);
            add_to_config_log('auth', $CFG->auth, $value, 'core');
            set_config('auth', $value);
        }
        break;

    default:
        break;
}

redirect($returnurl);
