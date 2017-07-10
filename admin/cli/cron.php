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
 * CLI cron
 *
 * This script looks through all the module directories for cron.php files
 * and runs them.  These files can contain cleanup functions, email functions
 * or anything that needs to be run on a regular basis.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions
require_once($CFG->libdir.'/cronlib.php');

// now get cli options
list($options, $unrecognized) = cli_get_params(array('help'=>false),
                                               array('h'=>'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Execute periodic cron actions.

Options:
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/cli/cron.php
";

    echo $help;
    die;
}


if (!function_exists('grade_update')) { //workaround for buggy PHP versions
    require_once($CFG->libdir . '/gradelib.php');
}



echo 'Update user grades';

$sql = "SELECT courseid, grademax, itemnumber, iteminstance FROM moodle.mdl_grade_grades g
	join moodle.mdl_grade_items i on g.itemid = i.id
	where i.itemmodule = 'quizoff' and aggregationstatus = 'used'";

	$rs = $DB->get_recordset_sql($sql, NULL);

	foreach ($rs as $gd) {
		$courseid = $gd->courseid;
		$grademax = $gd->grademax;
		$itemnumber = $gd->itemnumber;
		$iteminstance = $gd->iteminstance;
		$params['grademax'] = $grademax + 1;
		grade_update('mod/quizoff', $courseid, 'mod', 'quizoff', $iteminstance, $itemnumber, NULL, $params);
		$params['grademax'] = $grademax;
		grade_update('mod/quizoff', $courseid, 'mod', 'quizoff', $iteminstance, $itemnumber, NULL, $params);
		grade_regrade_final_grades($courseid);
		echo "Course " . $courseid . " updated";
	}
	if ($rs) {
        	$rs->close();
   	}
echo '  Grade update end  ';


cron_run();
