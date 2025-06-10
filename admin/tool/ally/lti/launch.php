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
 * Ally report LTI launch script.
 *
 * @package   tool_ally
 * @author    Sam Chaffee / Guy Thomas
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_ally\lti\launch_config;

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

require_login(null, false);
require_capability('tool/ally:clientconfig', context_system::instance());

$config = get_config('tool_ally');
$launchconfig = new launch_config($config, $CFG);
$launchcontainer = $launchconfig->get_launchcontainer();

$instance = (object) [
    'id' => 0,
    'course' => SITEID,
    'name' => 'Ally client config', // Localise?
    'typeid' => null,
    'instructorchoicesendname' => 1,
    'instructorchoicesendemailaddr' => 1,
    'instructorchoiceallowroster' => null,
    'instructorcustomparameters' => null,
    'instructorchoiceacceptgrades' => 1,
    'resourcekey' => $launchconfig->get_key(),
    'password' => $launchconfig->get_secret(),
    'launchcontainer' => $launchconfig->get_launchcontainer(),
    'toolurl' => $launchconfig->get_url(),
    'securetoolurl' => '',
    'servicesalt' => uniqid('', true),
    'debuglaunch' => 0,
];

lti_launch_tool($instance);
