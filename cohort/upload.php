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
 * A form for cohort upload.
 *
 * @package    core_cohort
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->dirroot.'/cohort/upload_form.php');
require_once($CFG->libdir . '/csvlib.class.php');

$contextid = optional_param('contextid', 0, PARAM_INT);

require_login();

if ($contextid) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
} else {
    $context = context_system::instance();
}
if ($context->contextlevel != CONTEXT_COURSECAT && $context->contextlevel != CONTEXT_SYSTEM) {
    print_error('invalidcontext');
}

require_capability('moodle/cohort:manage', $context);

$PAGE->set_context($context);
$baseurl = new moodle_url('/cohort/upload.php', array('contextid' => $context->id));
$PAGE->set_url($baseurl);
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('admin');

if ($context->contextlevel == CONTEXT_COURSECAT) {
    $PAGE->set_category_by_id($context->instanceid);
    navigation_node::override_active_url(new moodle_url('/cohort/index.php', array('contextid' => $context->id)));
} else {
    navigation_node::override_active_url(new moodle_url('/cohort/index.php', array()));
}

$uploadform = new cohort_upload_form(null, array('contextid' => $context->id));

$returnurl = new moodle_url('/cohort/index.php', array('contextid' => $context->id));

if ($uploadform->is_cancelled()) {
    redirect($returnurl);
}

$strheading = get_string('uploadcohorts', 'cohort');
$PAGE->navbar->add($strheading);

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($strheading, 'uploadcohorts', 'cohort');

if ($editcontrols = cohort_edit_controls($context, $baseurl)) {
    echo $OUTPUT->render($editcontrols);
}

if ($data = $uploadform->get_data()) {
    $cohortsdata = $uploadform->get_cohorts_data();
    foreach ($cohortsdata as $cohort) {
        cohort_add_cohort($cohort);
    }
    echo $OUTPUT->notification(get_string('uploadedcohorts', 'cohort', count($cohortsdata)), 'notifysuccess');
    echo $OUTPUT->continue_button($returnurl);
} else {
    $uploadform->display();
}

echo $OUTPUT->footer();

