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
 * Process ajax requests
 *
 * @copyright Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require(__DIR__.'/../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$sesskey = optional_param('sesskey', false, PARAM_TEXT);
$itemorder = optional_param('itemorder', false, PARAM_SEQUENCE);

$cm = get_coursemodule_from_id('feedback', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$feedback = $DB->get_record('feedback', array('id'=>$cm->instance), '*', MUST_EXIST);

require_sesskey();

$context = context_module::instance($cm->id);
require_login($course, true, $cm);
require_capability('mod/feedback:edititems', $context);

$return = false;

switch ($action) {
    case 'saveitemorder':
        $itemlist = explode(',', trim($itemorder, ','));
        if (count($itemlist) > 0) {
            $return = feedback_ajax_saveitemorder($itemlist, $feedback);
        }
        break;
}

echo json_encode($return);
die;
