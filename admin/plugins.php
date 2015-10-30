<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * UI for general plugins management
 *
 * Supported HTTP parameters:
 *
 *  ?fetchremote=1      - check for available updates
 *  ?updatesonly=1      - display plugins with available update only
 *  ?contribonly=1      - display non-standard add-ons only
 *  ?uninstall=foo_bar  - uninstall the given plugin
 *  ?delete=foo_bar     - delete the plugin folder (it must not be installed)
 *  &confirm=1          - confirm the uninstall or delete action
 *
 * @package    core
 * @subpackage admin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/filelib.php');

$fetchremote = optional_param('fetchremote', false, PARAM_BOOL);
$updatesonly = optional_param('updatesonly', false, PARAM_BOOL);
$contribonly = optional_param('contribonly', false, PARAM_BOOL);
$uninstall   = optional_param('uninstall', '', PARAM_COMPONENT);
$delete      = optional_param('delete', '', PARAM_COMPONENT);
$confirmed   = optional_param('confirm', false, PARAM_BOOL);
$return      = optional_param('return', 'overview', PARAM_ALPHA);

// NOTE: do not use admin_externalpage_setup() here because it loads
//       full admin tree which is not possible during uninstallation.

require_login();
$syscontext = context_system::instance();
require_capability('moodle/site:config', $syscontext);

$pluginman = core_plugin_manager::instance();

if ($uninstall) {
    require_sesskey();

    if (!$confirmed) {
        admin_externalpage_setup('pluginsoverview');
    } else {
        $PAGE->set_url('/admin/plugins.php');
        $PAGE->set_context($syscontext);
        $PAGE->set_pagelayout('maintenance');
        $PAGE->set_popup_notification_allowed(false);
    }

    /** @var core_admin_renderer $output */
    $output = $PAGE->get_renderer('core', 'admin');

    $pluginfo = $pluginman->get_plugin_info($uninstall);

    // Make sure we know the plugin.
    if (is_null($pluginfo)) {
        throw new moodle_exception('err_uninstalling_unknown_plugin', 'core_plugin', '', array('plugin' => $uninstall),
            'core_plugin_manager::get_plugin_info() returned null for the plugin to be uninstalled');
    }

    $pluginname = $pluginman->plugin_name($pluginfo->component);
    $PAGE->set_title($pluginname);
    $PAGE->navbar->add(get_string('uninstalling', 'core_plugin', array('name' => $pluginname)));

    if (!$pluginman->can_uninstall_plugin($pluginfo->component)) {
        throw new moodle_exception('err_cannot_uninstall_plugin', 'core_plugin', '',
            array('plugin' => $pluginfo->component),
            'core_plugin_manager::can_uninstall_plugin() returned false');
    }

    if (!$confirmed) {
        $continueurl = new moodle_url($PAGE->url, array('uninstall' => $pluginfo->component, 'sesskey' => sesskey(), 'confirm' => 1, 'return'=>$return));
        $cancelurl = $pluginfo->get_return_url_after_uninstall($return);
        echo $output->plugin_uninstall_confirm_page($pluginman, $pluginfo, $continueurl, $cancelurl);
        exit();

    } else {
        $SESSION->pluginuninstallreturn = $pluginfo->get_return_url_after_uninstall($return);
        $progress = new progress_trace_buffer(new text_progress_trace(), false);
        $pluginman->uninstall_plugin($pluginfo->component, $progress);
        $progress->finished();

        if ($pluginman->is_plugin_folder_removable($pluginfo->component)) {
            $continueurl = new moodle_url($PAGE->url, array('delete' => $pluginfo->component, 'sesskey' => sesskey(), 'confirm' => 1));
            echo $output->plugin_uninstall_results_removable_page($pluginman, $pluginfo, $progress, $continueurl);
            // Reset op code caches.
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            exit();

        } else {
            echo $output->plugin_uninstall_results_page($pluginman, $pluginfo, $progress);
            // Reset op code caches.
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            exit();
        }
    }
}

if ($delete and $confirmed) {
    require_sesskey();

    $PAGE->set_url('/admin/plugins.php');
    $PAGE->set_context($syscontext);
    $PAGE->set_pagelayout('maintenance');
    $PAGE->set_popup_notification_allowed(false);

    /** @var core_admin_renderer $output */
    $output = $PAGE->get_renderer('core', 'admin');

    $pluginfo = $pluginman->get_plugin_info($delete);

    // Make sure we know the plugin.
    if (is_null($pluginfo)) {
        throw new moodle_exception('err_removing_unknown_plugin', 'core_plugin', '', array('plugin' => $delete),
            'core_plugin_manager::get_plugin_info() returned null for the plugin to be deleted');
    }

    $pluginname = $pluginman->plugin_name($pluginfo->component);
    $PAGE->set_title($pluginname);
    $PAGE->navbar->add(get_string('uninstalling', 'core_plugin', array('name' => $pluginname)));

    // Make sure it is not installed.
    if (!is_null($pluginfo->versiondb)) {
        throw new moodle_exception('err_removing_installed_plugin', 'core_plugin', '',
            array('plugin' => $pluginfo->component, 'versiondb' => $pluginfo->versiondb),
            'core_plugin_manager::get_plugin_info() returned not-null versiondb for the plugin to be deleted');
    }

    // Make sure the folder is removable.
    if (!$pluginman->is_plugin_folder_removable($pluginfo->component)) {
        throw new moodle_exception('err_removing_unremovable_folder', 'core_plugin', '',
            array('plugin' => $pluginfo->component, 'rootdir' => $pluginfo->rootdir),
            'plugin root folder is not removable as expected');
    }

    // Make sure the folder is within Moodle installation tree.
    if (strpos($pluginfo->rootdir, $CFG->dirroot) !== 0) {
        throw new moodle_exception('err_unexpected_plugin_rootdir', 'core_plugin', '',
            array('plugin' => $pluginfo->component, 'rootdir' => $pluginfo->rootdir, 'dirroot' => $CFG->dirroot),
            'plugin root folder not in the moodle dirroot');
    }

    // So long, and thanks for all the bugs.
    fulldelete($pluginfo->rootdir);
    // Reset op code caches.
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    // We need to execute upgrade to make sure everything including caches is up to date.
    redirect(new moodle_url('/admin/index.php'));
}

admin_externalpage_setup('pluginsoverview');

/** @var core_admin_renderer $output */
$output = $PAGE->get_renderer('core', 'admin');

$checker = \core\update\checker::instance();

// Filtering options.
$options = array(
    'updatesonly' => $updatesonly,
    'contribonly' => $contribonly,
);

if ($fetchremote) {
    require_sesskey();
    $checker->fetch();
    redirect(new moodle_url($PAGE->url, $options));
}

$deployer = \core\update\deployer::instance();
if ($deployer->enabled()) {
    $myurl = new moodle_url($PAGE->url, array('updatesonly' => $updatesonly, 'contribonly' => $contribonly));
    $deployer->initialize($myurl, new moodle_url('/admin'));

    $deploydata = $deployer->submitted_data();
    if (!empty($deploydata)) {
        echo $output->upgrade_plugin_confirm_deploy_page($deployer, $deploydata);
        die();
    }
}

echo $output->plugin_management_page($pluginman, $checker, $options);
