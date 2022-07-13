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
 * Page to select WHAT to do with a given resource stored on MoodleNet.
 *
 * This collates and presents the same options as a user would see for a drag and drop upload.
 * That is, it leverages the dndupload_register() hooks and delegates the resource handling to the dndupload_handle hooks.
 *
 * This page requires a course, section an resourceurl to be provided via import_info.
 *
 * @package     tool_moodlenet
 * @copyright   2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use tool_moodlenet\local\import_handler_registry;
use tool_moodlenet\local\import_processor;
use tool_moodlenet\local\import_info;
use tool_moodlenet\local\import_strategy_file;
use tool_moodlenet\local\import_strategy_link;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

$module = optional_param('module', null, PARAM_PLUGIN);
$import = optional_param('import', null, PARAM_ALPHA);
$cancel = optional_param('cancel', null, PARAM_ALPHA);
$id = required_param('id', PARAM_ALPHANUM);

if (is_null($importinfo = import_info::load($id))) {
    throw new moodle_exception('missinginvalidpostdata', 'tool_moodlenet');
}

// Resolve course and section params.
// If course is not already set in the importinfo, we require it in the URL params.
$config = $importinfo->get_config();
if (!isset($config->course)) {
    $course = required_param('course', PARAM_INT);
    $config->course = $course;
    $config->section = 0;
    $importinfo->set_config($config);
    $importinfo->save();
}

// Access control.
require_login($config->course, false);
require_capability('moodle/course:manageactivities', context_course::instance($config->course));
if (!get_config('tool_moodlenet', 'enablemoodlenet')) {
    throw new \moodle_exception('moodlenetnotenabled', 'tool_moodlenet');
}

// If the user cancelled, break early.
if ($cancel) {
    redirect(new moodle_url('/course/view.php', ['id' => $config->course]));
}

// Set up required objects.
$course = get_course($config->course);
$handlerregistry = new import_handler_registry($course, $USER);
switch ($config->type) {
    case 'file':
        $strategy = new import_strategy_file();
        break;
    case 'link':
    default:
        $strategy = new import_strategy_link();
        break;
}

if ($import && $module) {
    confirm_sesskey();

    $handlerinfo = $handlerregistry->get_resource_handler_for_mod_and_strategy($importinfo->get_resource(), $module, $strategy);
    if (is_null($handlerinfo)) {
        throw new coding_exception("Invalid handler '$module'. The import handler could not be found.");
    }
    $importproc = new import_processor($course, $config->section, $importinfo->get_resource(), $handlerinfo, $handlerregistry);
    $importproc->process();

    $importinfo->purge(); // We don't need information about the import any more.

    redirect(new moodle_url('/course/view.php', ['id' => $course->id]));
}

// Setup the page and display the form.
$PAGE->set_context(context_course::instance($course->id));
$PAGE->set_pagelayout('base');
$PAGE->set_title(get_string('coursetitle', 'moodle', array('course' => $course->fullname)));
$PAGE->set_heading($course->fullname);
$PAGE->set_url(new moodle_url('/admin/tool/moodlenet/options.php'));

// Fetch the handlers supporting this resource. We'll display each of these as an option in the form.
$handlercontext = [];
foreach ($handlerregistry->get_resource_handlers_for_strategy($importinfo->get_resource(), $strategy) as $handler) {
    $handlercontext[] = [
        'module' => $handler->get_module_name(),
        'message' => $handler->get_description(),
    ];
}

// Template context.
$context = [
    'resourcename' => $importinfo->get_resource()->get_name(),
    'resourcetype' => $importinfo->get_config()->type,
    'resourceurl' => urlencode($importinfo->get_resource()->get_url()->get_value()),
    'course' => $course->id,
    'section' => $config->section,
    'sesskey' => sesskey(),
    'handlers' => $handlercontext,
    'oneoption' => sizeof($handlercontext) === 1
];

echo $OUTPUT->header();
echo $PAGE->get_renderer('core')->render_from_template('tool_moodlenet/import_options_select', $context);
echo $OUTPUT->footer();
