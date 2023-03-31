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
 * File containing the class activity navigation renderable.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_moove\output\core_course;

use renderable;
use templatable;
use url_select;

/**
 * The class activity navigation renderable.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_navigation extends \core_course\output\activity_navigation {

    /**
     * Constructor.
     *
     * @param \cm_info|null $prevmod The previous module to display, null if none.
     * @param \cm_info|null $nextmod The next module to display, null if none.
     * @param array $activitylist The list of activity URLs (as key) and names (as value) for the activity dropdown menu.
     */
    public function __construct($prevmod, $nextmod, $activitylist = array()) {
        // Check if there is a previous module to display.
        if ($prevmod) {
            $linkurl = new \moodle_url($prevmod->url, array('forceview' => 1));
            $linkname = $prevmod->get_formatted_name();
            if (!$prevmod->visible) {
                $linkname .= ' ' . get_string('hiddenwithbrackets');
            }

            $attributes = [
                'class' => 'btn btn-link',
                'id' => 'prev-activity-link',
            ];
            $this->prevlink = new \action_link($linkurl, $linkname, null, $attributes);
        }

        // Check if there is a next module to display.
        if ($nextmod) {
            $linkurl = new \moodle_url($nextmod->url, array('forceview' => 1));
            $linkname = $nextmod->get_formatted_name();
            if (!$nextmod->visible) {
                $linkname .= ' ' . get_string('hiddenwithbrackets');
            }

            $attributes = [
                'class' => 'btn btn-link',
                'id' => 'next-activity-link',
            ];
            $this->nextlink = new \action_link($linkurl, $linkname, null, $attributes);
        }

        // Render the activity list dropdown menu if available.
        if (!empty($activitylist)) {
            $select = new url_select($activitylist, '', array('' => get_string('jumpto')));
            $select->set_label(get_string('jumpto'), array('class' => 'sr-only'));
            $select->attributes = array('id' => 'jump-to-activity');
            $this->activitylist = $select;
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output Renderer base.
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $data = new \stdClass();
        if ($this->prevlink) {
            $data->prevlink = $this->prevlink->export_for_template($output);
        }

        if ($this->nextlink) {
            $data->nextlink = $this->nextlink->export_for_template($output);
        }

        if ($this->activitylist) {
            $data->activitylist = $this->activitylist->export_for_template($output);
        }

        return $data;
    }
}
