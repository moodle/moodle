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
 * @package   local_template_selector
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   Standard Moodle template selector
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/local/template_selector/lib.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/template_selector/search.php');

// In developer debug mode, when there is a debug=1 in the URL send as plain text
// for easier debugging.
if (debugging('', DEBUG_DEVELOPER) && optional_param('debug', false, PARAM_BOOL)) {
    header('Content-type: text/plain; charset=UTF-8');
    $debugmode = true;
} else {
    header('Content-type: application/json');
    $debugmode = false;
}

// Check access.
if (!isloggedin()) {
    print_error('mustbeloggedin');
}
if (!confirm_sesskey()) {
    print_error('invalidsesskey');
}

// Get the search parameter.
$search = required_param('search', PARAM_RAW);

// Get and validate the selectorid parameter.
$selectorhash = required_param('selectorid', PARAM_ALPHANUM);
if (!isset($USER->templateselectors[$selectorhash])) {
    print_error('unknowntemplateselector');
}

// Get the options.
$options = $USER->templateselectors[$selectorhash];

if ($debugmode) {
    echo 'Search string: ', $search, "\n";
    echo 'Options: ';
    var_dump($options);
    echo "\n";
}

// Create the appropriate templateselector.
$classname = $options['class'];
unset($options['class']);
$name = $options['name'];
unset($options['name']);
if (isset($options['file'])) {
    require_once($CFG->dirroot . '/' . $options['file']);
    unset($options['file']);
}
$templateselector = new $classname($name, $options);

// Do the search and output the results.
$templates = $templateselector->find_templates($search);
foreach ($templates as &$group) {
    foreach ($group as $template) {
        $output = new stdClass;
        $output->id = $template->id;
        $output->name = $templateselector->output_template($template);
        if (!empty($template->disabled)) {
            $output->disabled = true;
        }
        $group[$template->id] = $output;
    }
}

echo json_encode(array('results' => $templates));
