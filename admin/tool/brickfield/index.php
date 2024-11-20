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
 * Accessibility report
 *
 * @package    tool_brickfield
 * @copyright  2020 Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_brickfield\event\report_downloaded;
use tool_brickfield\event\report_viewed;
use tool_brickfield\accessibility;
use tool_brickfield\analysis;
use tool_brickfield\local\tool\filter;
use tool_brickfield\local\tool\tool;
use tool_brickfield\manager;
use tool_brickfield\output\renderer;
use tool_brickfield\registration;
use tool_brickfield\scheduler;
use tool_brickfield\task\process_analysis_requests;

require('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

// If this feature has been disabled, do nothing.
accessibility::require_accessibility_enabled();

// Check for valid registration.
$registration = new registration();
if (!$registration->toolkit_is_active()) {
    $urlregistration = manager::registration_url();
    redirect($urlregistration->out());
}

$config = get_config(manager::PLUGINNAME);
$courseid = optional_param('courseid', 0, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$tab = optional_param('tab', 'errors', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$target = optional_param('target', '', PARAM_ALPHA);
$layout = optional_param('layout', 'admin', PARAM_ALPHA);

if ($courseid != 0) {
    // If accessing a course, check that the user has capability to use toolkit at course level.
    if (!$course = $DB->get_record('course', ['id' => $courseid], '*')) {
        throw new moodle_exception('invalidcourseid', manager::PLUGINNAME);
    }
    require_login($course);
    $context = context_course::instance($courseid);
    require_capability(accessibility::get_capability_name('viewcoursetools'), $context);
} else if ($categoryid != 0) {
    require_login();
    $context = context_coursecat::instance($categoryid);
    require_capability(accessibility::get_capability_name('viewcoursetools'), $context);
} else {
    require_login();
    // If accessing system level, check that the user has capability to use toolkit at system level.
    $context = context_system::instance();
    require_capability(accessibility::get_capability_name('viewsystemtools'), $context);
}

// Event logging of page view or summary download.
if ($target == 'pdf') {
    $event = report_downloaded::create(['context' => $context]);
} else {
    $event = report_viewed::create(['context' => $context,
        'other' => ['course' => $courseid, 'category' => $categoryid, 'tab' => $tab, 'target' => $target]]);
}
$event->trigger();

$action = optional_param('action', '', PARAM_ALPHA);
// Handle any single operation actions.
if ($action == 'requestanalysis') {
    if ($courseid != 0) {
        scheduler::request_course_analysis($courseid);
        if ($courseid == SITEID) {
            redirect(accessibility::get_plugin_url());
        } else {
            redirect(new \moodle_url('/course/view.php', ['id' => $courseid]), analysis::redirect_message());
        }
    }
}

// We need all of the tools available for various functions in the renderers.
$tools = tool::build_all_accessibilitytools();
if (isset($tools[$tab])) {
    $tool = $tools[$tab];
} else {
    throw new moodle_exception('invalidaccessibilitytool', manager::PLUGINNAME);
}

$perpagedefault = $config->perpage;
$perpage = optional_param('perpage', $perpagedefault, PARAM_INT);
$navurl = new moodle_url(accessibility::get_plugin_url(), ['courseid' => $courseid]);
$url = new moodle_url($navurl, ['tab' => $tab, 'perpage' => $perpage]);

$tool->set_filter(new filter($courseid, $categoryid, $tab, $page, $perpage, $url, $target));

// Course and site require different navigation setups.
if ($courseid > SITEID) {
    $PAGE->navigation->override_active_url($navurl);
} else {
    admin_externalpage_setup('tool_brickfield_reports', '', null, '', ['pagelayout' => 'report']);
}
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout($layout);
$straccessibility = get_string('accessibilityreport', manager::PLUGINNAME);
$output = $PAGE->get_renderer(manager::PLUGINNAME);
$toolname = $tool->get_toolshortname();
$PAGE->set_title($toolname.' '.$straccessibility);
$PAGE->set_heading($straccessibility);

if ($tool->data_is_valid() && ($tool->get_output_target() == 'pdf')) {
    // PDF output doesn't return.
    $tool->get_output();
} else {
    echo $output->header();
    $courseid = ($courseid == 0) ? SITEID : $courseid;
    if (analysis::is_course_analyzed($courseid)) {
        echo $output->tabs($tool->get_filter(), $tools);
        echo $output->cachealert();

        if ($registration->validation_pending()) {
            echo $output->notvalidatedalert();
        }

        if (!$tool->data_is_valid()) {
            echo($tool->data_error());
        } else {
            echo $tool->get_output();
        }
    } else {
        $analysisdisabled = $output->cachealert();
        if (!empty($analysisdisabled)) {
            echo $analysisdisabled;
        } else {
            echo $output->analysisalert($courseid);
        }
    }

    echo $output->footer();
}
