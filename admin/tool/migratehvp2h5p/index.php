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
 * This tool can upgrade mod_hvp activities (Joubel) to the new mod_h5p activity (Moodle HQ).
 *
 * The upgrade can be done on any HVP activity instance.
 * The new HP5activity module was introduced in Moodle 3.9 and although it almost reproduces
 * the features of the existing mod_hvp, it wasn't designed to replace it entirely as there
 * are some features than the current mod_h5pactivity doesn't support, such as saving status or H5P hub.
 *
 * This screen is the main entry-point to the plugin, it gives the admin a list
 * of options available to them.
 *
 * @package     tool_migratehvp2h5p
 * @copyright   2020 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use tool_migratehvp2h5p\output\hvpactivities_table;
use tool_migratehvp2h5p\output\listnotmigrated;
use tool_migratehvp2h5p\api;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$context = context_system::instance();
$url = new moodle_url('/admin/tool/migratehvp2h5p/index.php');

$activityids = optional_param_array('activityids', [], PARAM_INT);
$keeporiginal = optional_param('keeporiginal', api::KEEPORIGINAL, PARAM_INT);
$copy2cb = optional_param('copy2cb', api::COPY2CBYESWITHLINK, PARAM_INT);

// This calls require_login and checks moodle/site:config.
admin_externalpage_setup('migratehvp2h5p');

$notices = [];
if (!empty($activityids)) {
    foreach ($activityids as $activityid) {
        try {
            $messages = api::migrate_hvp2h5p($activityid, $keeporiginal, $copy2cb);
            if (empty($messages)) {
                // Use the default message when no message is raised by the migration method.
                $notices[] = [get_string('migrate_success', 'tool_migratehvp2h5p', $activityid), notification::NOTIFY_SUCCESS];
            } else {
                // Merge message with previous notices.
                $notices = array_merge($messages, $notices);
            }
        } catch (moodle_exception $e) {
            $errormsg = get_string('migrate_fail', 'tool_migratehvp2h5p', $activityid);
            $errormsg .= ': '.$e->getMessage();
            $notices[] = [$errormsg, notification::NOTIFY_ERROR];
        }
    }
} else {
    try {
        api::check_requirements($copy2cb);
    } catch (moodle_exception $e) {
        $notices[] = [$e->getMessage(), notification::NOTIFY_ERROR];
    }
}

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$PAGE->set_title(get_string('pluginname', 'tool_migratehvp2h5p'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('hvpactivities', 'tool_migratehvp2h5p'));

foreach ($notices as $notice) {
    echo $OUTPUT->notification($notice[0], $notice[1]);
}

$table = new hvpactivities_table();
$table->baseurl = $url;
$activitylist = new listnotmigrated($table);
echo $OUTPUT->render($activitylist);

echo $OUTPUT->footer();
