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
 * Execute an update action on a course format and structure.
 *
 * @package    core_courseformat
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

use core\url;
use core\exception\moodle_exception;
use core_courseformat\base as course_format;

$action = required_param('action', PARAM_ALPHANUMEXT);
$courseid = required_param('courseid', PARAM_INT);
$targetsectionid = optional_param('targetsectionid', null, PARAM_INT);
$targetcmid = optional_param('targetcmid', null, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);


// All state updates are designed to be batch compatible. However, we also
// accept single id values for simplicity.
$ids = optional_param_array('ids', [], PARAM_INT);
if (empty($ids)) {
    $ids = [required_param('id', PARAM_INT)];
}
if (empty($ids)) {
    throw new moodle_exception('missingparam', '', '', 'ids');
}

$format = course_get_format($courseid);
$course = $format->get_course();

if ($returnurl === null) {
    $returnurl = new url('/course/view.php', ['id' => $course->id]);
}

// Normalize the return URL.
$returnurl = new moodle_url($returnurl);

$currenturl = new moodle_url(
    '/course/format/update.php',
    [
        'action' => $action,
        'courseid' => $courseid,
        'targetsectionid' => $targetsectionid,
        'targetcmid' => $targetcmid,
        'returnurl' => $returnurl,
        'sesskey' => sesskey(),
    ]
);
foreach ($ids as $key => $id) {
    $currenturl->param("ids[]", $id);
}

require_sesskey();

$PAGE->set_url($currenturl);
$PAGE->set_context($format->get_context());
$PAGE->set_pagelayout('course');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_heading($course->fullname);

require_login($course);
require_all_capabilities(
    ['moodle/course:update', 'moodle/course:sectionvisibility', 'moodle/course:activityvisibility'],
    $format->get_context(),
);

// Some actions may require a confirmation dialog.
$actionuiclass = $format->get_output_classname('courseupdate');
/** @var core_courseformat\output\local\courseupdate $actionui */
$actionui = new $actionuiclass($format, $currenturl, $returnurl);

if (
    !$confirm
    && $actionui->is_confirmation_required($action)
) {
    /** @var \core_course_renderer $renderer */
    $renderer = $format->get_renderer($PAGE);
    echo $renderer->header();
    echo $actionui->get_confirmation_dialog(
        output: $renderer,
        course: $course,
        action: $action,
        ids: $ids,
        targetsectionid: $targetsectionid,
        targetcmid: $targetcmid,
    );
    echo $renderer->footer();
    die;
}

$updates = $format->get_stateupdates_instance();
$actions = $format->get_stateactions_instance();

if (!is_callable([$actions, $action])) {
    throw new moodle_exception("Invalid course state action $action in ".get_class($actions));
}

// Execute the action.
$actions->$action($updates, $course, $ids, $targetsectionid, $targetcmid);

// Dispatch the hook for post course content update.
$hook = new \core_courseformat\hook\after_course_content_updated(
    course: $course,
);
\core\di::get(\core\hook\manager::class)->dispatch($hook);

redirect($returnurl);
