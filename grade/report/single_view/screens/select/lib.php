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
 * The gradebook simple view - initial view to select your search options
 *
 * @package   simple_view
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class single_view_select extends single_view_screen {
    public function init($self_item_is_empty = false) {
        global $DB;

        $this->item = $DB->get_record('course', array('id' => $this->courseid));
    }

    public function html() {
        global $OUTPUT;

        $html = '';

        $types = grade_report_single_view::valid_screens();

        foreach ($types as $type) {
            $class = grade_report_single_view::classname($type);

            $screen = new $class($this->courseid, null, $this->groupid);

            if (!$screen instanceof selectable_items) {
                continue;
            }

            $options = $screen->options();

            if (empty($options)) {
                continue;
            }

            $params = array(
                'id' => $this->courseid,
                'item' => $screen->item_type(),
                'group' => $this->groupid
            );

            $url = new moodle_url('/grade/report/single_view/index.php', $params);
            $html .= $OUTPUT->heading($screen->description());

            $html .= $OUTPUT->single_select($url, 'itemid', $options);
        }

        if (empty($html)) {
            $OUTPUT->notification(get_string('no_screens', 'gradereport_single_view'));
        }

        return $html;
    }
}
