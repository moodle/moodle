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
 * Renders text with the active filters and returns it. Used to create previews of equations
 * using whatever tex filters are enabled.
 *
 * @package    atto_equation
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../../config.php');

$contextid = required_param('contextid', PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);
$PAGE->set_url('/lib/editor/atto/plugins/equation/ajax.php');
$PAGE->set_context($context);

require_login($course, false, $cm);
require_sesskey();

$action = required_param('action', PARAM_ALPHA);

if ($action === 'filtertext') {
    $text = required_param('text', PARAM_RAW);

    $result = filter_manager::instance()->filter_text($text, $context);
    echo $OUTPUT->header();
    echo $result;

    die();
}

print_error('invalidarguments');
