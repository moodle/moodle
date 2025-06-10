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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('lib.php');

require_login();

$s = function($key) {
    return get_string($key, 'block_my_picture');
};

ini_set('max_execution_time', '36000');

if (!is_siteadmin($USER->id)) {
    error('need_permission', 'block_my_picture');
}

$header = $s('reprocess_all_title');
$pluginname = $s('pluginname');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/my_picture/reprocess_all.php');
$PAGE->navbar->add($header);
$PAGE->set_title($pluginname . ': ' . $header);
$PAGE->set_heading($SITE->shortname . ': ' . $pluginname);

echo $OUTPUT->header();
echo $OUTPUT->heading($header);

$params = array('deleted' => '0');
$users = $DB->get_records('user', $params, '', 'id, idnumber');

echo '<div>';
echo $s('all_start') . '<br />';

$forceupdate = true;
mypic_batch_update($users, $forceupdate, '<br />');

echo '</div>';
echo $OUTPUT->footer();