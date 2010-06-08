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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This page lists all the instances of wiki in a particular course
 *
 * @package mod-wiki-2.0
 * @copyrigth 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyrigth 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT); // course

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('invalidcourseid');
}

require_course_login($course->id, true, $cm);

add_to_log($course->id, 'wiki', 'view all', "index.php?id=$course->id", "");

/// Get all required stringswiki

$strwikis = get_string("modulenameplural", "wiki");
$strwiki = get_string("modulename", "wiki");

/// Print the header

$navlinks = array();
$navlinks[] = array('name' => $strwikis, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple("$strwikis", "", $navigation, "", "", true, "", navmenu($course));

/// Get all the appropriate data

if (!$wikis = get_all_instances_in_course("wiki", $course)) {
    notice("There are no wikis", "../../course/view.php?id=$course->id");
    die;
}

$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    $sections = get_all_sections($course->id);
}

/// Print the list of instances (your module will probably extend this)

$timenow = time();
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strname = get_string("name");

if ($usesections) {
    $table->head  = array ($strsectionname, $strname);
    $table->align = array("center", "left");
} else {
    $table->head  = array ($strname);
    $table->align = array("left");
}

foreach ($wikis as $wiki) {
    if (!$wiki->visible) {
        //Show dimmed if the mod is hidden
        $link = "<a class=\"dimmed\" href=\"view.php?id=$wiki->coursemodule\">$wiki->name</a>";
    } else {
        //Show normal if the mod is visible
        $link = "<a href=\"view.php?id=$wiki->coursemodule\">$wiki->name</a>";
    }

    if ($usesections) {
        $table->data[] = array(get_section_name($course, $sections[$wiki->section]), $link);
    } else {
        $table->data[] = array($link);
    }
}

echo "<br />";

print_table($table);

/// Finish the page

print_footer($course);
