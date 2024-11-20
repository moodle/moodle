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
 * Recordings CLI Migration script.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent [at] call-learning [dt] fr)
 */

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\recording;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
global $CFG;
require_once($CFG->libdir . '/clilib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'courseid' => 0,
        'bigbluebuttonid' => 0,
        'run' => false
    ],
    [
        'h' => 'help',
        'c' => 'courseid',
        'b' => 'bigbluebuttoncmid',
        'r' => 'run'
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
        "Check for dismissed recording and see if they appear on the server.
Sometimes when the remote BigBlueButton server is temporarily not accessible it has been seen that the recordings
are set to 'dismissed' status. For now this is a workaround until we refactor slightly the recording API.

Options:
-h, --help                  Print out this help
-c, --courseid              Course identifier (id) in which we look for BigBlueButton activities and recordings. If not specified
                            we check every single BigBlueButton activity.
-b, --bigbluebuttoncmid     Identifier for the BigBlueButton activity we would like to specifically retrieve recordings for. If not
                            specified we check every single BigBlueButton activity
                            (scoped or not to a course depending on -c option).
-r,--run                    If false (default, just display information. By default we just display the information.
Example:
\$ sudo -u www-data /usr/bin/php mod/bigbluebuttonbn/cli/update_dismissed_recordings.php -c=4 -r=1
";

    echo $help;
    die;
}

$bbcms = [];
if (!empty($options['courseid'])) {
    $courseid = $options['courseid'];
    $modinfos = get_fast_modinfo($courseid)->get_instances_of('bigbluebuttonbn');
    $bbcms = array_values($modinfos);
} else if (!empty($options['bigbluebuttoncmid'])) {
    [$course, $bbcm] = get_course_and_cm_from_cmid($options['bigbluebuttoncmid']);
    $bbcms = [$bbcm];
} else {
    // All bigbluebutton activities.
    foreach ($DB->get_fieldset_select('bigbluebuttonbn', 'id', '') as $bbid) {
        [$course, $bbcm] = get_course_and_cm_from_instance($bbid, 'bigbluebuttonbn');
        array_push($bbcms, $bbcm);
    }
}
foreach ($bbcms as $bbcm) {
    $instance = instance::get_from_cmid($bbcm->id);
    cli_writeln("Processing  BigBlueButton {$instance->get_meeting_name()}(id:{$instance->get_instance_id()}),"
        . " in course {$bbcm->get_course()->fullname}(id:{$bbcm->get_course()->id})....");
    $recordings = recording::get_records(['status' => recording::RECORDING_STATUS_DISMISSED,
        'bigbluebuttonbnid' => $instance->get_instance_id()]);
    $recordingkeys = array_map(function($rec) {
        return $rec->get('recordingid');
    }, $recordings);
    $recordingmeta = \mod_bigbluebuttonbn\local\proxy\recording_proxy::fetch_recordings($recordingkeys);
    if (empty($recordings)) {
        cli_writeln("\t->No recordings found ...");
    } else {
        foreach ($recordings as $recording) {
            if (!empty($recordingmeta[$recording->get('recordingid')])) {
                $recordingwithmeta = new recording(0, $recording->to_record(), $recordingmeta[$recording->get('recordingid')]);
                cli_writeln("\t-> Recording data found for " . $recordingwithmeta->get('name') . ' ID:' .
                    $recordingwithmeta->get('recordingid'));
                if ($options['run']) {
                    $recordingwithmeta->set('status', recording::RECORDING_STATUS_PROCESSED);
                    $recordingwithmeta->save();
                    cli_writeln("\t\t-> Metadata and status updated...");
                }
            } else {
                cli_writeln("\t-> No recording data found for " . $recording->get('recordingid'));
            }
        }
    }
}
