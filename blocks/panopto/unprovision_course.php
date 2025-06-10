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
 * the unprovision course logic for Panopto
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
require_once(dirname(__FILE__) . '/classes/panopto_unprovision_form.php');
require_once(dirname(__FILE__) . '/lib/panopto_data.php');
require_once(dirname(__FILE__) . '/lib/block_panopto_lib.php');

global $courses;

require_login();


// Set course context if we are in a course, otherwise use system context.
$courseidparam = optional_param('course_id', 0, PARAM_INT);
if ($courseidparam != 0) {
    $context = context_course::instance($courseidparam, MUST_EXIST);
} else {
    $context = context_system::instance();
}

$PAGE->set_context($context);

$returnurl = optional_param('return_url', $CFG->wwwroot . '/admin/settings.php?section=blocksettingpanopto', PARAM_LOCALURL);

$urlparams['return_url'] = $returnurl;

$PAGE->set_url('/blocks/panopto/unprovision_course.php', $urlparams);
$PAGE->set_pagelayout('base');

$mform = new panopto_unprovision_form($PAGE->url);

if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else {
    $unprovisiontitle = get_string('unprovision_courses', 'block_panopto');
    $PAGE->set_pagelayout('base');
    $PAGE->set_title($unprovisiontitle);
    $PAGE->set_heading($unprovisiontitle);

    if ($courseidparam != 0) {
        // Course context.
        require_capability('block/panopto:provision_course', $context);

        $courses = [$courseidparam];
        $editcourseurl = new moodle_url($returnurl);
        $PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $editcourseurl);
    } else {
        // System context.
        require_capability('block/panopto:provision_multiple', $context);

        $data = $mform->get_data();
        if ($data) {
            $courses = $data->courses;
        }

        $manageblocks = new moodle_url('/admin/blocks.php');
        $panoptosettings = new moodle_url('/admin/settings.php?section=blocksettingpanopto');
        $PAGE->navbar->add(get_string('blocks'), $manageblocks);
        $PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $panoptosettings);
    }

    $PAGE->navbar->add($unprovisiontitle, new moodle_url($PAGE->url));

    echo $OUTPUT->header();

    if ($courses) {
        $coursecount = count($courses);

        foreach ($courses as $courseid) {
            if (empty($courseid)) {
                continue;
            }

            // Set the current Moodle course to retrieve info for / unprovision.
            $panoptodata = new \panopto_data($courseid);
            $unprovisioninginfo = $panoptodata->get_provisioning_info();
            $unprovisionwassuccess = $panoptodata->unprovision_course();

            include('views/unprovisioned_course.html.php');
        }
        echo "<a href='$returnurl'>" . get_string('back_to_config', 'block_panopto') . '</a>';
    } else {
        $mform->display();
    }

    echo $OUTPUT->footer();
}
/* End of file unprovision_course.php */
