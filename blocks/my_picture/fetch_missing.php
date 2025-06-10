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
 * Connects to LSU web service for downloading and updating user photos
 *
 * @package    block_my_picture
 * @copyright  2008, Adam Zapletal, 2017, Robert Russo, Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Set and get the config variable.
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('lib.php');

// Increase the max execution time to 2 hrs.
ini_set('max_execution_time', '7200');

require_login();

$s = function($key) {
    return get_string($key, 'block_my_picture');
};

if (!is_siteadmin($USER->id)) {
    print_error('need_permission', 'block_mypic');
}

$header = $s('fetch_missing_title');
$pluginname = $s('pluginname');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/my_picture/fetch_missing.php');
$PAGE->navbar->add($header);
$PAGE->set_title($pluginname . ': ' . $header);
$PAGE->set_heading($SITE->shortname . ': ' . $pluginname);

echo $OUTPUT->header();
echo $OUTPUT->heading($header);
echo '<div>';
echo $s('fetching_start') . '<br />';

$limit = get_config('block_my_picture', 'cron_users');
$users = mypic_get_users_without_pictures($limit);

if ($users) {
    $forceupdate = false;
    mypic_batch_update($users, $forceupdate, '<br />');
} else {
    echo $s('no_missing_pictures') . '<br />';
}

echo '</div>';
echo $OUTPUT->footer();