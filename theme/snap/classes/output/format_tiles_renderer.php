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
 * Snap Tiles format renderer.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output;

use core_courseformat\base as course_format;
use format_tiles\output\renderer;
use theme_snap\output\format_tiles\content\tiles_content;

if (class_exists('format_tiles\output\renderer')) {
    class format_tiles_renderer extends renderer {

    public function render_from_template($templatename, $data) {
        global $CFG;

            // Always work data as object.
            if (is_array($data)) {
                $data = (object)$data;
            }
            // Emulates overwriting of export_for_template method for all templates.
            // Get data for mustache OpenLMS templates.
            $isediting = $this->page->user_is_editing();
            $format = course_get_format($this->page->course->id);
            $course = $format->get_course();
            $editingonparam = optional_param('notifyeditingon', 0, PARAM_INT);
            $currenturl = $CFG->wwwroot . '/course/view.php?id=' . $course->id;
            if ($editingonparam === 0) {
                $currenturl = $currenturl . '&notifyeditingon=1';
            }
            $data->imgurltools = $this->output->image_url('course_dashboard', 'theme');
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

            return $this->output->render_from_template($templatename, $data);
        }

        /**
         * Render the enable bulk editing button.
         * @param course_format $format the course format
         * @return string|null the enable bulk button HTML (or null if no bulk available).
         */
        public function bulk_editing_button(course_format $format): ?string {
            // Snap modifications to course formats do not support this feature.
            return '';
        }
    }
}
