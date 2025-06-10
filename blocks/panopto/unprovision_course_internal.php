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
 * This file contains the logic to unprovision classes.
 *
 * @package block_panopto
 * @copyright  Panopto 2020
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../config.php');
}
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/classes/panopto_unprovision_course_form.php');
require_once(dirname(__FILE__) . '/lib/block_panopto_lib.php');
require_once(dirname(__FILE__) . '/lib/panopto_data.php');

global $courses;

$numservers = get_config('block_panopto', 'server_number');
$numservers = isset($numservers) ? $numservers : 0;

// Increment numservers by 1 to take into account starting at 0.
++$numservers;

require_login();

// This page requires a course ID to be passed in as a param. If accessed directly without clicking on a link for the course,
// no id is passed and the script fails. Similarly if no ID is passed with via a link (should never happen) the script will fail.
$courseid = required_param('id', PARAM_INT);

// Course context.
$context = context_course::instance($courseid, MUST_EXIST);
$PAGE->set_context($context);

// Return URL is course page.
$returnurl = optional_param('return_url', $CFG->wwwroot . '/course/view.php?id=' . $courseid, PARAM_LOCALURL);
$urlparams['return_url'] = $returnurl;
$PAGE->set_url('/blocks/panopto/unprovision_course_internal.php?id=' . $courseid, $urlparams);
$PAGE->set_pagelayout('base');

$mform = new panopto_unprovision_course_form($PAGE->url);
// Set Moodle page info.
$unprovisiontitle = get_string('unprovision_courses', 'block_panopto');
$PAGE->set_title($unprovisiontitle);
$PAGE->set_heading($unprovisiontitle);

// Course context.
require_capability('block/panopto:provision_course', $context);
$editcourseurl = new moodle_url($returnurl);
$PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $editcourseurl);

$manageblocks = new moodle_url('/admin/blocks.php');
$panoptosettings = new moodle_url('/admin/settings.php?section=blocksettingpanopto');
$PAGE->navbar->add(get_string('blocks'), $manageblocks);
$PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $panoptosettings);
$PAGE->navbar->add($unprovisiontitle, new moodle_url($PAGE->url));

echo $OUTPUT->header();

if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else {

    $panoptodata = new \panopto_data($courseid);
    $unprovisioninginfo = $panoptodata->get_provisioning_info();
    $unprovisionwassuccess = $panoptodata->unprovision_course();

    include('views/unprovisioned_course.html.php');
    echo "<a href='$returnurl'>" . get_string('back_to_course', 'block_panopto') . '</a>';
}

echo $OUTPUT->footer();

/* End of file unprovision_course.php */
