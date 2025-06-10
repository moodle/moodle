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
 * This file contains the logic for the provision classes form.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../config.php');
}
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/classes/panopto_provision_course_form.php');
require_once(dirname(__FILE__) . '/lib/block_panopto_lib.php');
require_once(dirname(__FILE__) . '/lib/panopto_data.php');

global $courses;

// Populate list of servernames to select from.
$aserverarray = [];
$appkeyarray = [];

$numservers = get_config('block_panopto', 'server_number');
$numservers = isset($numservers) ? $numservers : 0;

// Increment numservers by 1 to take into account starting at 0.
++$numservers;

for ($serverwalker = 1; $serverwalker <= $numservers; $serverwalker++) {

    // Generate strings corresponding to potential servernames in the config.
    $thisservername = get_config('block_panopto', 'server_name' . $serverwalker);
    $thisappkey = get_config('block_panopto', 'application_key' . $serverwalker);

    $hasservername = !panopto_is_string_empty($thisservername);
    if ($hasservername && !panopto_is_string_empty($thisappkey)) {
        // Array reference so we should substract 1 to start at 0.
        $aserverarray[$serverwalker - 1] = $thisservername;
        $appkeyarray[$serverwalker - 1] = $thisappkey;
    }
}

// If only one server, simply provision with that server.
// Setting these values will circumvent loading the selection form prior to provisioning.
if (count($aserverarray) == 1) {
    // Get first element from associative array. aServerArray and appKeyArray will have same key values.
    $key = array_keys($aserverarray);
    $selectedserver = trim($aserverarray[$key[0]]);
    $selectedkey = trim($appkeyarray[$key[0]]);
}

require_login();


// This page requires a course ID to be passed in as a param.
// If accessed directly without clicking on a link for the course,
// no id is passed and the script fails. Similarly if no ID is passed with via a link (should never happen) the script will fail.
$courseid = required_param('id', PARAM_INT);

// Course context.
$context = context_course::instance($courseid, MUST_EXIST);
$PAGE->set_context($context);

// Return URL is course page.
$returnurl = optional_param('return_url', $CFG->wwwroot . '/course/view.php?id=' . $courseid, PARAM_LOCALURL);
$urlparams['return_url'] = $returnurl;
$PAGE->set_url('/blocks/panopto/provision_course_internal.php?id=' . $courseid, $urlparams);
$PAGE->set_pagelayout('base');

$mform = new panopto_provision_course_form($PAGE->url);
// Set Moodle page info.
$provisiontitle = get_string('provision_courses', 'block_panopto');
$PAGE->set_title($provisiontitle);
$PAGE->set_heading($provisiontitle);

// Course context.
require_capability('block/panopto:provision_course', $context);
$editcourseurl = new moodle_url($returnurl);
$PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $editcourseurl);

$manageblocks = new moodle_url('/admin/blocks.php');
$panoptosettings = new moodle_url('/admin/settings.php?section=blocksettingpanopto');
$PAGE->navbar->add(get_string('blocks'), $manageblocks);
$PAGE->navbar->add(get_string('pluginname', 'block_panopto'), $panoptosettings);
$PAGE->navbar->add($provisiontitle, new moodle_url($PAGE->url));

echo $OUTPUT->header();

if ($mform->is_cancelled()) {
    redirect(new moodle_url($returnurl));
} else if ($data = $mform->get_data()) {
    // If data has been submitted use it then lose it for next time.
    $selectedserver = trim($aserverarray[$data->servers]);
    $selectedkey = trim($appkeyarray[$data->servers]);

    if (isset($selectedserver) && !empty($selectedserver) &&
        isset($selectedkey) && !empty($selectedkey)) {

        $panoptodata = new \panopto_data($courseid);

        if (!isset($panoptodata->servername) || empty($panoptodata->servername) ||
            ($panoptodata->servername !== $selectedserver)) {
            $panoptodata->sessiongroupid = null;
        }

        $panoptodata->servername = $selectedserver;
        $panoptodata->applicationkey = $selectedkey;

        $provisioningdata = $panoptodata->get_provisioning_info();
        $provisioneddata = $panoptodata->provision_course($provisioningdata, false);

        include('views/provisioned_course.html.php');
        echo "<a href='$returnurl'>" . get_string('back_to_course', 'block_panopto') . '</a>';
    } else {
        $mform->display();
    }
} else {

    // If there are no servers specified for provisioning, give a failure notice and allow user to return to course page.
    if (count($aserverarray) < 1) {
        echo get_string('no_server', 'block_panopto') .
        "<br/><a href='$returnurl'>" . get_string('back_to_course', 'block_panopto') . '</a>';

    } else if (count($aserverarray) === 1) {
        // Get first element from associative array. aServerArray and appKeyArray will have same key values.
        $key = array_keys($aserverarray);
        $selectedserver = trim($aserverarray[$key[0]]);
        $selectedkey = trim($appkeyarray[$key[0]]);

        $panoptodata = new \panopto_data($courseid);

        // If we are not using the same server remove the folder ID reference.
        // NOTE: A Moodle course can only point to one Panopto server at a time.
        // So reprovisioning to a different server erases the folder mapping to the original server.
        if (!isset($panoptodata->servername) || empty($panoptodata->servername) ||
            ($panoptodata->servername !== $selectedserver)) {
            $panoptodata->sessiongroupid = null;
        }

        $panoptodata->servername = $selectedserver;
        $panoptodata->applicationkey = $selectedkey;

        $provisioningdata = $panoptodata->get_provisioning_info();
        $provisioneddata = $panoptodata->provision_course($provisioningdata, false);

        include('views/provisioned_course.html.php');
        echo "<a href='$returnurl'>" . get_string('back_to_course', 'block_panopto') . '</a>';
    } else {
        $panoptodata = new \panopto_data($courseid);
        if (in_array($panoptodata->servername, $aserverarray)) {
            $provisioningdata = $panoptodata->get_provisioning_info();
            $provisioneddata = $panoptodata->provision_course($provisioningdata, false);

            include('views/provisioned_course.html.php');
            echo "<a href='$returnurl'>" . get_string('back_to_course', 'block_panopto') . '</a>';
        } else {
            $mform->display();
        }
    }
}

echo $OUTPUT->footer();

/* End of file provision_course.php */
