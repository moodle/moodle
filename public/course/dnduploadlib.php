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
 * Library to handle drag and drop course uploads
 *
 * @package    core
 * @subpackage lib
 * @copyright  2012 Davo smith
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');

/**
 * Add the Javascript to enable drag and drop upload to a course page
 *
 * @deprecated since Moodle 5.0
 * @todo Remove this method in Moodle 6.0 (MDL-83627).
 * @param object $course The currently displayed course
 * @param array $modnames The list of enabled (visible) modules on this site
 * @return void
 */
#[\core\attribute\deprecated(
    replacement: 'core_courformat::base\\use_component returning true',
    since: '5.0',
    mdl: 'MDL-82341',
    reason: 'Moodle 3.9 course editor is deprecated. Make your format compatible to 4.0 editor.',
)]
function dndupload_add_to_course($course, $modnames) {
    global $CFG, $PAGE;

    \core\deprecation::emit_deprecation(__FUNCTION__);

    $showstatus = optional_param('notifyeditingon', false, PARAM_BOOL);

    // Get all handlers.
    $handler = new dndupload_handler($course, $modnames);
    $jsdata = $handler->get_js_data();
    if (empty($jsdata->types) && empty($jsdata->filehandlers)) {
        return; // No valid handlers - don't enable drag and drop.
    }

    // Add the javascript to the page.
    $jsmodule = array(
        'name' => 'coursedndupload',
        'fullpath' => '/course/dndupload.js',
        'strings' => array(
            array('addfilehere', 'moodle'),
            array('dndworkingfiletextlink', 'moodle'),
            array('dndworkingfilelink', 'moodle'),
            array('dndworkingfiletext', 'moodle'),
            array('dndworkingfile', 'moodle'),
            array('dndworkingtextlink', 'moodle'),
            array('dndworkingtext', 'moodle'),
            array('dndworkinglink', 'moodle'),
            array('namedfiletoolarge', 'moodle'),
            array('actionchoice', 'moodle'),
            array('servererror', 'moodle'),
            array('filereaderror', 'moodle'),
            array('upload', 'moodle'),
            array('cancel', 'moodle'),
            array('changesmadereallygoaway', 'moodle')
        ),
        'requires' => array('node', 'event', 'json', 'anim')
    );
    $vars = array(
        array('courseid' => $course->id,
              'maxbytes' => get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $course->maxbytes),
              'handlers' => $handler->get_js_data(),
              'showstatus' => $showstatus)
    );

    $PAGE->requires->js_init_call('M.course_dndupload.init', $vars, true, $jsmodule);
}
