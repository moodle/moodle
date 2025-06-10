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
 * Class lti
 *
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 * @package local_intellidata
 */

require_once("../../config.php");


$context = context_system::instance();

require_login();
require_capability('local/intellidata:viewlti', $context);

$PAGE->set_context($context);

$ltiservice = new \local_intellidata\services\lti_service();
list($endpoint, $parms, $debug) = $ltiservice->lti_get_launch_data();

$renderer = $PAGE->get_renderer("local_intellidata");

echo $renderer->render(new \local_intellidata\output\lti_launch($parms, $endpoint, $debug));


