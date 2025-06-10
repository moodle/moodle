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
 * A test authenticated page that we can check over the network
 *
 * @package    tool_crawler
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/../config.php');

require_login(null, false);

$header = get_string('hellorobotheading', 'tool_crawler');

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/crawler/tests/test1.php');
$PAGE->set_title($header);
$PAGE->set_heading($header);

echo $OUTPUT->header();

echo get_string('hellorobot', 'tool_crawler', array('botusername' => $USER->username));

echo $OUTPUT->footer();
