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
 * My Media LTI launch page.
 *
 * @package    local_mymedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/kaltura/locallib.php');

global $USER;

require_login();

$context = context_user::instance($USER->id);
require_capability('local/mymedia:view', $context);

$launch = array();
$launch['id'] = 1;
$launch['cmid'] = 0;
$launch['title'] = 'My Media';
$launch['module'] = KAF_MYMEDIA_MODULE;
$launch['course'] = $PAGE->course;
$launch['width'] = '300';
$launch['height'] = '300';
$launch['custom_publishdata'] = '';

if (local_kaltura_validate_mymedia_required_params($launch)) {
    $content = local_kaltura_request_lti_launch($launch);
    echo $content;
} else {
    echo get_string('invalid_launch_parameters', 'local_mymedia');
}