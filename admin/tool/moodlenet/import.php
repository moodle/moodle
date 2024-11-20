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
 * This is the main endpoint which MoodleNet instances POST to.
 *
 * MoodleNet instances send the user agent to this endpoint via a form POST.
 * Then:
 * 1. The POSTed resource information is put in a session store for cross-request access.
 * 2. This page makes a GET request for admin/tool/moodlenet/index.php (the import confirmation page).
 * 3. Then, depending on whether the user is authenticated, the user will either:
 * - If not authenticated, they will be asked to login, after which they will see the confirmation page (leveraging $wantsurl).
 * - If authenticated, they will see the confirmation page immediately.
 *
 * @package     tool_moodlenet
 * @copyright   2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_moodlenet\local\import_info;
use tool_moodlenet\local\remote_resource;
use tool_moodlenet\local\url;

require_once(__DIR__ . '/../../../config.php');

// The integration must be enabled for this import endpoint to be active.
if (!get_config('tool_moodlenet', 'enablemoodlenet')) {
    throw new \moodle_exception('moodlenetnotenabled', 'tool_moodlenet');
}

$resourceurl = required_param('resourceurl', PARAM_URL);
$resourceinfo = required_param('resource_info', PARAM_RAW);
$resourceinfo = json_decode($resourceinfo);
$type = optional_param('type', 'link', PARAM_TEXT);
$course = optional_param('course', 0, PARAM_INT);
$section = optional_param('section', 0, PARAM_INT);
// If course isn't provided, course and section are null.
if (empty($course)) {
    $course = null;
    $section = null;
}
$name = validate_param($resourceinfo->name, PARAM_TEXT);
$description = validate_param($resourceinfo->summary, PARAM_TEXT);

// Only accept POSTs.
if (!empty($_POST)) {
    // Store information about the import of the resource for the current user.
    $importconfig = (object) [
        'course' => $course,
        'section' => $section,
        'type' => $type,
    ];
    $metadata = (object) [
        'name' => $name,
        'description' => $description ?? ''
    ];

    require_once($CFG->libdir . '/filelib.php');
    $importinfo = new import_info(
        $USER->id,
        new remote_resource(new \curl(), new url($resourceurl), $metadata),
        $importconfig
    );
    $importinfo->save();

    // Redirect to the import confirmation page, detouring via the log in page if required.
    redirect(new moodle_url('/admin/tool/moodlenet/index.php', ['id' => $importinfo->get_id()]));

}

// Invalid or missing POST data. Show an error to the user.
throw new \moodle_exception('missinginvalidpostdata', 'tool_moodlenet');
