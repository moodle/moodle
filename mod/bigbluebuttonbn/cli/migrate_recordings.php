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
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\task\upgrade_recordings_task;

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
        'forcemigrate' => false
    ],
    [
        'h' => 'help',
        'c' => 'courseid',
        'b' => 'bigbluebuttoncmid',
        'f' => 'forcemigrate'
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
        "Execute / Rexecute migration for recordings.
Sometimes when the remote BigBlueButton server is temporarily not accessible and we upgrade to the new way of storing recordings
some activities end up without recordings. This script will allow to go through each meeting and fetch all recordings
that have not yet been fetched from the remote BigblueButton server.


Options:
-h, --help                  Print out this help
-c, --courseid              Course identifier (id) in which we look for BigblueButton activities and recordings. If not specified
                            we check every single BigblueButton activity.
-b, --bigbluebuttoncmid     Identifier for the bigbluebutton activity we would like to specifically retrieve recordings for. If not
                            specified we check every single BigblueButton activity
                            (scoped or not to a course depending on -c option).
-f,--forcemigrate           Force the 'remigration' of recordings, so even if a recording has been marked as migrated in the logs
                            we still fetch the data from the Bigbluebutton server.
Example:
\$ sudo -u www-data /usr/bin/php mod/bigbluebuttonbn/cli/migrate_recordings.php -c=4 -f=1
";

    echo $help;
    die;
}
global $DB;

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
    $adhoctask = new upgrade_recordings_task();
    cli_writeln("Processing BigbluebButton {$instance->get_meeting_name()}(id:{$instance->get_instance_id()}),"
        . " in course {$bbcm->get_course()->fullname}(id:{$bbcm->get_course()->id})....");
    if ($options['forcemigrate']) {
        // We set the value of the log back to the original value so it get evalated once again.
        $DB->set_field('bigbluebuttonbn_logs', 'log', logger::EVENT_IMPORT,
            [
                'courseid' => $instance->get_course_id(),
                'bigbluebuttonbnid' => $instance->get_instance_id(),
                'log' => logger::EVENT_IMPORT_MIGRATED
            ]);
        $DB->set_field('bigbluebuttonbn_logs', 'log', logger::EVENT_CREATE,
            [
                'courseid' => $instance->get_course_id(),
                'bigbluebuttonbnid' => $instance->get_instance_id(),
                'log' => logger::EVENT_CREATE_MIGRATED
            ]);
    }
    $meetingids = $DB->get_fieldset_sql(
        'SELECT DISTINCT meetingid FROM {bigbluebuttonbn_logs} WHERE log = :createlog OR log = :importlog',
        [
            'importlog' => logger::EVENT_IMPORT,
            'createlog' => logger::EVENT_CREATE
        ]
    );
    if (empty($meetingids)) {
        cli_writeln("\t->No meetings logs found...");
    }
    foreach ($meetingids as $mid) {
        cli_write("\t->Processing meeting ID {$mid} ...recordings");
        $adhoctask->set_custom_data((object)
        [
            'meetingid' => $mid,
            'isimported' => false
        ]);
        $adhoctask->execute();
        cli_writeln("...imported recordings....");
        $adhoctask->set_custom_data((object)
        [
            'meetingid' => $mid,
            'isimported' => true
        ]);
        $adhoctask->execute();
    }
}
