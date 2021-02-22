<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Mobile support for zoom.
 *
 * @package     mod_zoom
 * @copyright   2018 Nick Stefanski <nmstefanski@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_zoom\output;

defined('MOODLE_INTERNAL') || die();

use context_module;
use mod_zoom_external;

/**
 * Mobile output class for zoom
 *
 * @package     mod_zoom
 * @copyright   2018 Nick Stefanski <nmstefanski@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns the zoom course view for the mobile app,
     *  including meeting details and launch button (if applicable).
     * @param  array $args Arguments from tool_mobile_get_content WS
     *
     * @return array   HTML, javascript and otherdata
     */
    public static function mobile_course_view($args) {
        global $OUTPUT, $DB;

        $args = (object) $args;
        $cm = get_coursemodule_from_id('zoom', $args->cmid);

        // Capabilities check.
        require_login($args->courseid, false, $cm, true, true);

        $context = context_module::instance($cm->id);

        require_capability('mod/zoom:view', $context);
        // Right now we're just implementing basic viewing, otherwise we may
        // need to check other capabilities.
        $zoom = $DB->get_record('zoom', array('id' => $cm->instance));

        // WS to get zoom state.
        try {
            $zoomstate = mod_zoom_external::get_state($cm->id);
        } catch (\Exception $e) {
            $zoomstate = array();
        }

        // Format date and time.
        $starttime = userdate($zoom->start_time);
        $duration = format_time($zoom->duration);

        // Get audio option string.
        $optionaudio = get_string('audio_' . $zoom->option_audio, 'mod_zoom');

        $data = array(
            'zoom' => $zoom,
            'available' => $zoomstate['available'],
            'status' => $zoomstate['status'],
            'start_time' => $starttime,
            'duration' => $duration,
            'option_audio' => $optionaudio,
            'cmid' => $cm->id,
            'courseid' => $args->courseid
        );

        return array(
            'templates' => array(
                array(
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_zoom/mobile_view_page', $data),
                ),
            ),
            'javascript' => "this.loadMeeting = function(result) { window.open(result.joinurl, '_system'); };",
            // This JS will redirect to a joinurl passed by the mod_zoom_grade_item_update WS.
            'otherdata' => '',
            'files' => ''
        );
    }

}
