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
 * Edit grading form in for a particular instance of workshop
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$cmid = required_param('cmid', PARAM_INT);            // course module id
        
if (!$cm = get_coursemodule_from_id('workshop', $cmid)) {
    print_error('invalidcoursemodule');
}   
        
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}   

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (isguestuser()) {
    print_error('guestnoedit', 'workshop', "$CFG->wwwroot/mod/workshop/view.php?id=$cmid");
}

if (!$workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
    print_error('invalidid', 'workshop');
}

// where should the user be sent after closing the editing form
$returnurl = "{$CFG->wwwroot}/mod/workshop/view.php?id={$cm->id}";
// the URL of this editing form
$selfurl   = "{$CFG->wwwroot}/mod/workshop/editgradingform.php?cmid={$cm->id}";

// load the grading strategy logic
$strategylib = dirname(__FILE__) . '/grading/' . $workshop->strategy . '/strategy.php';
if (file_exists($strategylib)) {
    require_once($strategylib);
} else {
    print_error('errloadingstrategylib', 'workshop', $returnurl);
}
$classname = 'workshop_' . $workshop->strategy . '_strategy';
$strategy = new $classname($workshop);

// load the assessment form definition from the database
// this must be called before get_edit_strategy_form() where we have to know
// the number of repeating fieldsets
$formdata = $strategy->load_grading_form();

// load the form to edit the grading strategy dimensions
$mform = $strategy->get_edit_strategy_form($selfurl);

// initialize form data
$mform->set_data($formdata);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} elseif ($data = $mform->get_data()) {
    $strategy->save_grading_form($data);
    if (isset($data->saveandclose)) {
        redirect($returnurl);
    } else {
        redirect($selfurl);
    }
}

// build the navigation and the header
$navlinks = array();
$navlinks[] = array('name' => get_string('modulenameplural', 'workshop'), 
                    'link' => "index.php?id=$course->id", 
                    'type' => 'activity');
$navlinks[] = array('name' => format_string($workshop->name), 
                    'link' => "view.php?id=$cm->id",
                    'type' => 'activityinstance');
$navlinks[] = array('name' => get_string('editinggradingform', 'workshop'), 
                    'link' => '',
                    'type' => 'title');
$navigation = build_navigation($navlinks);

// OUTPUT STARTS HERE

print_header_simple(format_string($workshop->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, get_string('modulename', 'workshop')), navmenu($course, $cm));

print_heading(get_string('strategy' . $workshop->strategy, 'workshop'));

$mform->display();

/// Finish the page
print_footer($course);
