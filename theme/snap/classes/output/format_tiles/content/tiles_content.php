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
 * Contains the default content output class.
 *
 * @package   format_tiles
 * @copyright 2022 David Watson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output\format_tiles\content;

use format_tiles\output\courseformat\content as content;
use theme_snap\output\shared;

/**
 * Format tiles class to render course content.
 *
 * @package   format_tiles
 * @copyright 2022 David Watson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (class_exists('format_tiles\output\courseformat\content')) {
    class tiles_content extends content {

        /**
         * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
         *
         * @param \renderer_base $output typically, the renderer that's calling this function
         * @return \stdClass data context for a mustache template
         */
        public function export_for_template(\renderer_base $output) {
            global $PAGE, $CFG, $OUTPUT;

            // Get default tiles data for template.
            $data = parent::export_for_template($output);

            // Get data for 'theme_snap/format_tiles/content' OpenLMS template.
            $isediting = $PAGE->user_is_editing();
            $course = $this->format->get_course();
            $editingonparam = optional_param('notifyeditingon', 0, PARAM_INT);
            $currenturl = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
            if ($editingonparam === 0) {
                $currenturl = $currenturl . '&notifyeditingon=1';
            }
            $data->imgurltools = $OUTPUT->image_url('course_dashboard', 'theme');
            $data->urlcoursetools = $currenturl . '#coursetools';
            if (has_capability('moodle/course:update', \context_system::instance())) {
                $data->has_edit_capability = true;
                $urleditmode = $CFG->wwwroot . '/course/view.php?id=' . $course->id . '&sesskey=' . sesskey();
                if ($isediting) {
                    $urleditmode .= '&edit=off';
                    $editstring = get_string('turneditingoff');
                } else {
                    $urleditmode .= '&edit=on';
                    $editstring = get_string('editmodetiles', 'theme_snap');
                }
                $data->urleditmode = $urleditmode;
                $data->editstring = $editstring;
            }

        // Additional output HTML to render Snap Course tools and edit mode button in footer.
        $data->course_tools = shared::course_tools(true);

            return $data;
        }
    }
}