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
 * AJAX functionalities for mod_journal
 *
 * @package mod_journal
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

define('AJAX_SCRIPT', true);
define('NO_DEBUG_DISPLAY', true);

require('../../../config.php');
require_once($CFG->dirroot.'/mod/journal/lib.php');

require_login();
$PAGE->set_context(context_system::instance());

$result = array(
    'status' => '',
    'content' => ''
);

try {
    $action = required_param('action', PARAM_ALPHA);
    if ($action !== 'savecart') {
        session_write_close();
    }
    if (file_exists($CFG->dirroot.'/mod/journal/ajax/'.$action.'.php')) {
        require($CFG->dirroot.'/mod/journal/ajax/'.$action.'.php');
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $ex) {
    $result['status'] = 'error';
    $result['content'] = $ex->getMessage();
    $result['errorobj'] = json_encode($ex);

    debugging($ex->getMessage(), DEBUG_ALL, $ex->getTrace());
}

header('Content-Type: application/json');
echo json_encode($result);
