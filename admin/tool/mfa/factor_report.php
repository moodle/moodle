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
 * Reporting page for each factor vs auth type
 *
 * @package   tool_mfa
 * @author    Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright 2019 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('factorreport');

$reset = optional_param('reset', null, PARAM_TEXT);
$userid = optional_param('id', null, PARAM_INT);
$view = optional_param('view', null, PARAM_TEXT);

$PAGE->set_title(get_string('factorreport', 'tool_mfa'));
$PAGE->set_heading(get_string('factorreport', 'tool_mfa'));
$renderer = $PAGE->get_renderer('tool_mfa');

// Handle page actions.
if (!empty($reset) && confirm_sesskey()) {
    // Check factor is valid.
    $factor = \tool_mfa\plugininfo\factor::get_factor($reset);
    if (!$factor instanceof \tool_mfa\local\factor\object_factor_base) {
        throw new moodle_exception('error:factornotfound', 'tool_mfa');
    }

    // One user.
    if (!empty($userid)) {
        // Just reset the factor and reload.
        $DB->delete_records('tool_mfa', ['factor' => $factor->name, 'userid' => $userid]);
        $stringarr = ['factor' => $factor->name, 'username' => $userid];
        redirect(new moodle_url($PAGE->url, ['view' => $factor->name]), get_string('resetsuccess', 'tool_mfa', $stringarr));
    }

    // Bulk action for locked users.
    $locklevel = (int) get_config('tool_mfa', 'lockout');
    $sql = "SELECT DISTINCT(userid)
              FROM {tool_mfa}
             WHERE factor = ?
               AND lockcounter >= ?
               AND revoked = 0";
    $lockedusers = $DB->get_records_sql($sql, [$factor->name, $locklevel]);
    $lockedusers = array_map(function ($el) {
        return $el->userid;
    }, (array) $lockedusers);
    $SESSION->bulk_users = $lockedusers;
    redirect(new moodle_url('/admin/user/user_bulk.php'));
}

// Configure the lookback period for the report.
$days = optional_param('days', 0, PARAM_INT);
if ($days === 0) {
    $lookback = 0;
} else {
    $lookback = time() - (DAYSECS * $days);
}

// Construct a select to use for viewing time periods.
$selectarr = [
    0 => get_string('alltime', 'tool_mfa'),
    1 => get_string('numday', '', 1),
    7 => get_string('numweek', '', 1),
    31 => get_string('nummonth', '', 1),
    90 => get_string('nummonths', '', 3),
    180 => get_string('nummonths', '', 6),
    365 => get_string('numyear', '', 1),
];
$select = new single_select($PAGE->url, 'days', $selectarr);

echo $renderer->header();

if (!empty($view)) {
    // View locked users for a particular factor.
    $factor = \tool_mfa\plugininfo\factor::get_factor($view);
    if (!$factor instanceof \tool_mfa\local\factor\object_factor_base) {
        throw new moodle_exception('error:factornotfound', 'tool_mfa');
    }

    $backbutton = new single_button(new moodle_url($PAGE->url), get_string('back'));
    echo $renderer->heading(get_string('lockedusersforfactor', 'tool_mfa', $factor->get_display_name()));
    echo \html_writer::tag('p', $renderer->factor_locked_users_table($factor));
    echo $renderer->render($backbutton);
} else {
    echo $renderer->heading(get_string('factorreport', 'tool_mfa'));

    // Regular page content.
    echo html_writer::tag('p', get_string('selectperiod', 'tool_mfa'));
    echo $renderer->render($select);

    // Render the factors in use table.
    echo html_writer::tag('p', $renderer->factors_in_use_table($lookback));

    echo $renderer->heading(get_string('lockedusersforallfactors', 'tool_mfa'));

    // Now output a locked factors table.
    echo html_writer::tag('p', $renderer->factors_locked_table());
}

echo $OUTPUT->footer();
