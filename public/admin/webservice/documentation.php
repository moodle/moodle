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
 * Web services API documentation
 *
 * @package   webservice
 * @copyright 2011 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author Jerome Mouneyrac
 */

use core_external\external_api;

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/webservice/lib.php');

admin_externalpage_setup('webservicedocumentation');

// TODO: MDL-76078 - Incorrect inter-communication, core cannot have plugin dependencies like this.

//display the documentation for all documented protocols,
//regardless if they are activated or not
$protocols = array();
$protocols['rest'] = true;
$protocols['xmlrpc'] = true;

/// Check if we are in printable mode
$printableformat = optional_param('print', false, PARAM_BOOL);

/// OUTPUT
echo $OUTPUT->header();

// Get all the function descriptions.
$functions = $DB->get_records('external_functions', [], 'name');
$functiondescs = [];
foreach ($functions as $function) {

    // Skip invalid or otherwise incorrectly defined functions, otherwise the entire page is rendered inaccessible.
    try {
        $functiondescs[$function->name] = external_api::external_function_info($function);
    } catch (Throwable $exception) {
        echo $OUTPUT->notification($exception->getMessage(), \core\output\notification::NOTIFY_ERROR);
    }
}

$renderer = $PAGE->get_renderer('core', 'webservice');
echo $renderer->documentation_html($functiondescs,
        $printableformat, $protocols, array(), $PAGE->url);

/// trigger browser print operation
if (!empty($printableformat)) {
    $PAGE->requires->js_function_call('window.print', array());
}

echo $OUTPUT->footer();

