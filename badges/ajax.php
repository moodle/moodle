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
 * Sends request to check web site availability.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2013 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');

require_login();
$PAGE->set_url('/badges/ajax.php');
$PAGE->set_context(context_system::instance());

// Unlock session during potentially long curl request.
session_get_instance()->write_close();

$result = badges_check_backpack_accessibility();

$outcome = new stdClass();
$outcome->code = $result;
$outcome->response = get_string('error:backpacknotavailable', 'badges') . $OUTPUT->help_icon('backpackavailability', 'badges');
echo json_encode($outcome);

die();
