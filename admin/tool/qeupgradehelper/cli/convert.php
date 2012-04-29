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
 * Script to allow upgrading of quizzes with attempts that were previously
 * skipped.
 *
 * @package    tool_qeupgradehelper
 * @copyright  2012 Eric Merrill, Oakland Unversity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/locallib.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once($CFG->libdir.'/clilib.php');      // CLI only functions.


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('quiz'=>false, 'timelimit'=>false, 'countlimit'=>false, 'help'=>false),
                                               array('c'=>'countlimit', 't'=>'timelimit', 'h'=>'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}


if ($options['help']) {
    $help =
"Question engine upgrade helper CLI tool.
Will upgrade all remaining question attempts if no options are specified.

Options:
-c, --countlimit=<n>    Process n number of quizzes then exit
-t, --timelimit=<n>     Process quizzes for n number of seconds, then exit. A quiz
                        currently in progress will not be interrupted.
--quiz=<quizid>         Process quiz quizid only
-h, --help              Print out this help

countlimit and timelimit can be used together. First one to trigger will stop execution.

Example:
\$sudo -u www-data /usr/bin/php admin/tool/qeupgradehelper/cliupgrade.php
";

    echo $help;
    die;
}




if (!tool_qeupgradehelper_is_upgraded()) {
    mtrace('qeupgradehelper: site not yet upgraded. Doing nothing.');
    return;
}

require_once(dirname(dirname(__FILE__)) . '/afterupgradelib.php');


$starttime = time();

// Setup the stop time.
if ($options['timelimit']) {
    $stoptime = time() + $options['timelimit'];
} else {
    $stoptime = false;
}

// If we are doing a quiz id, limit to one.
if ($options['quiz']) {
    $options['countlimit'] = 1;
}

$count = 0;


mtrace('qeupgradehelper: processing ...');

/* This while statement does a few things
 * Basically if an option is set to false, then that subsection will return
 * true, and will short circuit the test condition for that option, and always
 * being true. Both options are anded together, so either one can trigger to stop.
 */
while ((!$stoptime || (time() < $stoptime)) && (!$options['countlimit'] || ($count < $options['countlimit']))) {
    if ($options['quiz']) {
        $quizid = $options['quiz'];
    } else {
        $quiz = tool_qeupgradehelper_get_quiz_for_upgrade();
        if (!$quiz) {
            mtrace('qeupgradehelper: No more quizzes to process.');
            break; // No more to do.
        }

        $quizid = $quiz->id;
    }
    $quizsummary = tool_qeupgradehelper_get_quiz($quizid);
    if ($quizsummary) {
        mtrace('  starting upgrade of attempts at quiz ' . $quizid);
        $upgrader = new tool_qeupgradehelper_attempt_upgrader(
                $quizsummary->id, $quizsummary->numtoconvert);
        $upgrader->convert_all_quiz_attempts();
        mtrace('  upgrade of quiz ' . $quizid . ' complete.');
    } else {
        mtrace('quiz ' . $quizid . ' not found or already upgraded.');
    }

    $count++;
}


mtrace('qeupgradehelper: Done. Processed '.$count.' quizes in '.(time()-$starttime).' seconds');
return;
