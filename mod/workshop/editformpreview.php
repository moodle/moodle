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
 * Preview the assessment form.
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

$cmid     = required_param('cmid', PARAM_INT);
$cm       = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course   = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshop = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}
$workshop = new workshop($workshop, $cm, $course);

require_capability('mod/workshop:editdimensions', $workshop->context);
$PAGE->set_url($workshop->previewform_url());
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('editingassessmentform', 'workshop'), $workshop->editform_url(), navigation_node::TYPE_CUSTOM);
$PAGE->navbar->add(get_string('previewassessmentform', 'workshop'));
$PAGE->set_secondary_active_tab('workshopassessement');
$PAGE->activityheader->set_attrs([
    "hidecompletion" => true,
    "description" => ''
]);
$currenttab = 'editform';

// load the grading strategy logic
$strategy = $workshop->grading_strategy_instance();

// load the assessment form
$mform = $strategy->get_assessment_form($workshop->editform_url(), 'preview');

// output starts here
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('assessmentform', 'workshop'), 3);
$mform->display();
echo $OUTPUT->footer();
