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
 * Prints a particular instance of workshop
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace workshop with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // workshop instance ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('workshop', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        error('Course is misconfigured');
    }

    if (! $workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $workshop = $DB->get_record('workshop', array('id' => $a))) {
        error('Course module is incorrect');
    }
    if (! $course = $DB->get_record('course', array('id' => $workshop->course))) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('workshop', $workshop->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, "workshop", "view", "view.php?id=$cm->id", "$workshop->id");

/// Print the page header
$strworkshops = get_string('modulenameplural', 'workshop');
$strworkshop  = get_string('modulename', 'workshop');

$navlinks = array();
$navlinks[] = array('name' => $strworkshops, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($workshop->name), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);

print_header_simple(format_string($workshop->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, $strworkshop), navmenu($course, $cm));

/// Print the main part of the page

echo "<a href=\"editgradingform.php?cmid={$cm->id}\">Edit grading form</a>";
echo " | ";
echo "<a href=\"submission.php?cmid={$cm->id}\">My submission</a>";

/// Finish the page
print_footer($course);
