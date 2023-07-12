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
 * @package    report_trainingsessions
 * @category   report
 * @version    moodle 2.x
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @subpackage  cli
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * This script is to be used from PHP command line and will create a set
 * of Virtual VMoodle automatically from a CSV nodelist description.
 * Template names can be used to feed initial data of new VMoodles.
 * The standard structure of the nodelist is given by the nodelist-dest.csv file.
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/clilib.php'); // Cli only functions.
require_once($CFG->libdir.'/adminlib.php'); // Various admin-only functions.
require_once($CFG->libdir.'/upgradelib.php'); // General upgrade/install related functions.
require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/csvrenderers.php');
require_once($CFG->libdir.'/excellib.class.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');

// Fakes an admin identity for all the process.
$USER = get_admin();

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    array(
        'interactive' => false,
        'help'        => false,
        'launch'      => false,
        'userid'      => false,
        'courseid'    => false,
        'outputpath'  => false
    ),
    array(
        'h' => 'help',
        'l' => 'launch',
        'u' => 'userid',
        'c' => 'courseid',
        'P' => 'outputpath'
    )
);

$interactive = !empty($options['interactive']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help = "Command line Statistics extractor.
Please note you must execute this script with the same uid as apache!

Options:
--interactive No interactive questions or confirmations
-h, --help      Print out this help
-l, --launch    Launch the extraction of statistics
-u, --userid    the userid for extracting one unique report, if null, get all users in the course.
-c, --courseid  the course id as context
-P, --outputpath  the path where to output. Defaults in moodledata/temp/trainingsessions/<date_of_day>

Example:
\$sudo -u www-data /usr/bin/php blocks/vmoodle/cli/bulkcreatenodes.php
"; // TODO: localize - to be translated later when everything is finished.

    echo $help;
    die;
}

if (empty($options['outputpath'])) {
    $options['outputpath'] = $CFG->dataroot.'/temp';
    $date = date('Ymd_Hi', time());
    if (!is_dir($options['outputpath'].'/trainingsessions/'.$date)) {
        if (!mkdir($options['outputpath'].'/trainingsessions/'.$date, 0777, true)) {
            die("could not create output directory");
        }
    }
    $options['outputpath'] = $options['outputpath'].'/trainingsessions/'.$date;
} else {
    if (!is_dir($options['outputpath'])) {
        die("could not find output path\n");
    }
}

if (empty($CFG->version)) {
    cli_error(get_string('missingconfigversion', 'debug'));
}

mtrace('Starting CLI trainingsession reports in '.$options['outputpath']."\n");
$config = get_config('report_trainingsession');

// Get all options from config file.

$userid = $options['userid']; // User id.
$courseid = $options['courseid']; // Course as context.

if (empty($options['launch'])) {
    mtrace("Preview mode\n");
}

if ($userid) {
    $processedusers[] = $userid;
    $user = $DB->get_record('user', array('id' => $userid));
    if (empty($options['launch'])) {
        mtrace('User to process : '.$user->username."\n");
    }
} else {
    $context = context_course::instance($courseid);
    $processedusers = array();
    if ($users = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments)) {
        foreach ($users as $u) {
            $processedusers[] = $u->id;
            if (empty($options['launch'])) {
                mtrace('User to process : '.$u->username."\n");
            }
        }
    }
}

if (!empty($options['launch'])) {

    foreach ($processedusers as $userid) {
        $data = new StdClass;
        $data->from = (empty($options['from'])) ? 0 : $options['from'];

        $logs = use_stats_extract_logs($data->from, time(), $userid, null);

        $aggregate = use_stats_aggregate_logs($logs, $data->from, time());

        $filename = 'allcourses_sessions_report_'.date('d-M-Y', time()).'.csv';

        // Sending HTTP headers.

        $overall = report_trainingsessions_print_allcourses_csv($csvfilecontent, $aggregate);

        $data->elapsed = $overall->elapsed;
        $data->events = $overall->events;

        $csvfilecontentheader = report_trainingsessions_print_header_csv($userid, $courseid, $data);

        report_trainingsessions_print_sessions_csv($csvsessions, $aggregate['sessions'], $courseid);

        echo "Opening output file as $filename\n";
        if ($file = fopen($options['outputpath'].'/'.$filename, 'w+')) {
            fputs($file, $csvfilecontentheader.$csvfilecontent."\n#\n# Sessions\n#\n".$csvsessions);
            fclose($file);
        } else {
            echo "Failed opening output file\n";
        }
    }
    die;
}
