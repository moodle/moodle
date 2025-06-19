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
 * Snap non-AJAX handler
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_snap\controller\kernel;
use theme_snap\controller\router;
use theme_snap\controller\addsection_controller;

require_once(__DIR__.'/../../config.php');

$systemcontext = context_system::instance();

$action    = required_param('action', PARAM_ALPHAEXT);
$contextid = optional_param('contextid', $systemcontext->id, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

require_login($course, false, $cm, false, true);

// @codingStandardsIgnoreLine
/** @var $PAGE moodle_page */
$PAGE->set_context($context);
$PAGE->set_url('/theme/snap/index.php', array('action' => $action, 'contextid' => $context->id));

$router = new router();
$router->add_controller(new addsection_controller());

$kernel = new kernel($router);
$kernel->handle($action);
