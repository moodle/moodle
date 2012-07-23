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
 * Random course generator.
 *
 * @package    tool
 * @subpackage generator
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../../config.php');
require_once('locallib.php');


require_login();
$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);
if (!is_siteadmin()) {
    error('Only for admins');
}

if (!debugging('', DEBUG_DEVELOPER)) {
    error('This script is for developers only!!!');
}

$PAGE->set_url('/admin/tool/generator/index.php');
$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('base');
$generator = new generator_web();
$generator->setup();
$generator->display();
$generator->generate_data();
$generator->complete();
