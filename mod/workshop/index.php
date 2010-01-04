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
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// Replace workshop with the name of your module and remove this line

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

if (! $course = $DB->get_record('course', array('id' => $id))) {
    error('Course ID is incorrect');
}

require_course_login($course);

add_to_log($course->id, 'workshop', 'view all', "index.php?id=$course->id", '');


/// Get all required stringsworkshop

$strworkshops = get_string('modulenameplural', 'workshop');
$strworkshop  = get_string('modulename', 'workshop');


/// Print the header

$navlinks = array();
$navlinks[] = array('name' => $strworkshops, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple($strworkshops, '', $navigation, '', '', true, '', navmenu($course));

/// Get all the appropriate data

if (! $workshops = get_all_instances_in_course('workshop', $course)) {
    notice('There are no workshops', "../../course/view.php?id=$course->id");
    die;
}

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($workshops as $workshop) {
    if (!$workshop->visible) {
        //Show dimmed if the mod is hidden
        $link = "<a class=\"dimmed\" href=\"view.php?id=$workshop->coursemodule\">$workshop->name</a>";
    } else {
        //Show normal if the mod is visible
        $link = "<a href=\"view.php?id=$workshop->coursemodule\">$workshop->name</a>";
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($workshop->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}

print_heading($strworkshops);
print_table($table);

/// Finish the page

print_footer($course);
