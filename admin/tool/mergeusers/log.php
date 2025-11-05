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
 * View one merging log.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_mergeusers\local\logger;

require('../../../config.php');

global $CFG, $DB, $PAGE;
require_once($CFG->libdir . '/adminlib.php');

// Report all PHP errors.
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_login();
require_capability('tool/mergeusers:viewlog', context_system::instance());
admin_externalpage_setup('tool_mergeusers_viewlog');
$id = required_param('id', PARAM_INT);

/** @var \tool_mergeusers\output\renderer $renderer */
$renderer = $PAGE->get_renderer('tool_mergeusers');
$logger = new logger();

$log = $logger->detail_from($id);

if (empty($log)) {
    throw new moodle_exception(
        'wronglogid',
        'tool_mergeusers',
        new moodle_url('/admin/tool/mergeusers/index.php'),
    );
}

$from = $DB->get_record('user', ['id' => $log->fromuserid]);
if (!$from) {
    $from = new stdClass();
    $from->id = $log->fromuserid;
    $from->username = get_string('deleted');
    $from->deleted = 1;
}

$to = $DB->get_record('user', ['id' => $log->touserid]);
if (!$to) {
    $to = new stdClass();
    $to->id = $log->touserid;
    $to->username = get_string('deleted');
    $to->deleted = 1;
}

echo $renderer->results_page($to, $from, $log->success, $log->log, $log->id);
