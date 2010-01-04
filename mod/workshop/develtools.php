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
 * Workshop development toolkit
 *
 * Provides some tools that may be useful during the workshop development
 * and debugging. This is not intended for productive enviroments.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$cmid = required_param('cmid', PARAM_INT);              // course module id
$tool = optional_param('tool', 'menu', PARAM_ALPHA);    // toolkit action

debugging('', DEBUG_DEVELOPER) || die('For development purposes only');
has_capability('moodle/site:config', get_system_context()) || die('You are not allowed to run this');

$cm         = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop   = new workshop_api($workshop, $cm, $course); // wrap the record with a full API

require_login($course, false, $cm);

$PAGE->set_url('mod/workshop/develtools.php', array('cmid' => $cm->id));
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'workshop')));

$navigation = build_navigation('Development tools', $cm);
$menu       = navmenu($course, $cm);

switch ($tool) {
case 'mksubmissions':
    $authors                = $workshop->get_peer_authors(false);
    $authorswithsubmission  = $workshop->get_peer_authors(true);
    $authors                = array_diff_key($authors, $authorswithsubmission);
    echo $OUTPUT->header($navigation, $menu);
    foreach ($authors as $authorid => $author) {
        $timenow = time() - rand(0, 60 * 60 * 24 * 7); // submitted sometimes during last week
        $submission                 = new stdClass();
        $submission->workshopid     = $workshop->id;
        $submission->example        = 0;
        $submission->userid         = $authorid;
        $submission->timecreated    = $timenow;
        $submission->timemodified   = $timenow;
        $submission->title          = $author->firstname . '\'s submission';
        $submission->data           = '';
        $submission->dataformat     = FORMAT_HTML;
        $submission->datatrust      = 0;
        $submission->id = $DB->insert_record('workshop_submissions', $submission);
        // todo print some info
    }
    echo $OUTPUT->continue_button($PAGE->url->out());
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->header($navigation, $menu);
echo $OUTPUT->heading('Workshop development tools', 1);
echo '<a href="' . $PAGE->url->out(false, array('tool' => 'mksubmissions')) . '">Fake submissions</a>';
echo $OUTPUT->footer();
