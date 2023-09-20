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
 * Actions controller
 *
 * Perform a synchronous action to modify the question bank UI and redirect back to the previous page.
 * These features are mostly progressively enhanced by actions.js and web services, but this remains as a fallback.
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$action = required_param('action', PARAM_TEXT);
$global = optional_param('global', false, PARAM_BOOL);
$returnurl = optional_param('returnurl', '/question/bank/columnsortorder/sortcolumns.php', PARAM_LOCALURL);

require_login();

if ($global) {
    require_capability('moodle/site:config', context_system::instance());
}

if ($action === 'debugreset' && $CFG->debug === DEBUG_DEVELOPER) {
    $columnmanager = new \qbank_columnsortorder\column_manager($global);
    $columnmanager::set_hidden_columns([], $global);
    $columnmanager::set_column_order([], $global);
    $columnmanager::set_column_size('', $global);
    redirect(new moodle_url($returnurl));
}

require_sesskey();
$columnmanager = new \qbank_columnsortorder\column_manager($global);
switch ($action) {
    case 'add':
    case 'remove':
        $column = required_param('column', PARAM_RAW);
        [$columnclass, ] = explode(\core_question\local\bank\column_base::ID_SEPARATOR, $column);
        if (!class_exists($columnclass)) {
            throw new invalid_parameter_exception("'{$columnclass}' is not a valid column class.");
        }
        $hiddencolumns = $columnmanager->hiddencolumns;
        if ($action === 'add') {
            $key = array_search($column, $hiddencolumns);
            if ($key !== false) {
                unset($hiddencolumns[$key]);
            }
        } else {
            if (!in_array($column, $hiddencolumns)) {
                $hiddencolumns[] = $column;
            }
        }
        $columnmanager::set_hidden_columns($hiddencolumns, $global);
        break;

    case 'savewidths':
        $rawwidths = optional_param_array('width', [], PARAM_INT);
        $widths = [];
        foreach (array_filter($rawwidths) as $escapedclass => $width) {
            $class = str_replace('__', '\\', $escapedclass);
            // Validate that the class exists and the width is valid.
            // Since the browser uses Constraint Validation to prevent the form being submitted with an invalid width,
            // the only way we'll get one here is if someone is messing around, so don't worry about re-displaying the
            // form with an error message, just ignore the invalid value.
            if (class_exists($class) && $width >= 10) {
                $widths[] = (object)[
                    'column' => $class,
                    'width' => $width,
                ];
            }
        }
        $columnmanager::set_column_size(json_encode($widths), $global);
        break;

    case 'reset':
        $columnmanager::set_hidden_columns(null, $global);
        $columnmanager::set_column_order(null, $global);
        $columnmanager::set_column_size(null, $global);
        break;
}
redirect(new moodle_url($returnurl));
