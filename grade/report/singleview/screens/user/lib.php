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
 * The gradebook simple view - grades view (for a user)
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class gradereport_singleview_user extends gradereport_singleview_tablelike implements gradereport_selectable_items {

    private $categories = array();

    public $structure;

    public function description() {
        return get_string('gradeitems', 'grades');;
    }

    public function options() {
        return array_map(function($item) {
            return $item->get_name();
        }, $this->items);
    }

    public function item_type() {
        return 'grade';
    }

    public function display_group_selector() {
        return false;
    }

    public function init($selfitemisempty = false) {
        global $DB;

        if (!$selfitemisempty) {
            $this->item = $DB->get_record('user', array('id' => $this->itemid));
        }

        $params = array('courseid' => $this->courseid);

        $seq = new grade_seq($this->courseid, true);
        foreach ($seq->items as $key => $item) {
            if (isset($item->itemmodule)) {
                list($courseid, $cmid) = get_course_and_cm_from_instance($item->iteminstance, $item->itemmodule);
                $seq->items[$key]->cmid = $cmid->id;
            }
        }

        $this->items = array_filter($seq->items, gradereport_singleview::filters());

        unset($seq);

        $this->setup_structure();

        $this->definition = array(
            'finalgrade', 'feedback', 'override', 'exclude'
        );
        $this->set_headers($this->original_headers());
    }

    public function original_headers() {
        return array(
            '', // For filter icon.
            '', // For activity icon.
            get_string('assessmentname', 'gradereport_singleview'),
            get_string('gradecategory', 'grades'),
            get_string('range', 'grades'),
            get_string('grade', 'grades'),
            get_string('feedback', 'grades'),
            $this->make_toggle_links('override'),
            $this->make_toggle_links('exclude')
        );
    }

    public function format_line($item) {
        global $OUTPUT;

        $grade = $this->fetch_grade_or_default($item, $this->item->id);
        $lockicon = '';

        // UCSB add lock icon indicator.
        $lockeditem = $lockeditemgrade = 0;
        if (!empty($grade->locked)) {
            $lockeditem = 1;
        }
        if (!empty($grade->grade_item->locked)) {
            $lockeditemgrade = 1;
        }
        // Check both grade and grade item.
        if ($lockeditem || $lockeditemgrade) {
             $lockicon = $OUTPUT->pix_icon('t/locked', 'grade is locked');
        }

        $realuserid = '';
        if (isset($item->cmid)) {
            $realuserid = $item->cmid;
        }
        $url = new moodle_url('/mod/' . $item->itemmodule . '/view.php', array('id' => $realuserid));
        $iconstring = get_string('filtergrades', 'gradereport_singleview', $item->get_name());
        $grade->label = $item->get_name();

        $line = array(
            $OUTPUT->action_icon($this->format_link('grade', $item->id), new pix_icon('t/editstring', $iconstring)),
            $this->format_icon($item) . $lockicon,
            html_writer::link($url, $item->get_name()),
            $this->category($item),
            $this->factory()->create('range')->format($item)
        );
        return $this->format_definition($line, $grade);
    }

    private function format_icon($item) {
        $element = array('type' => 'item', 'object' => $item);
        return $this->structure->get_element_icon($element);
    }

    private function category($item) {
        if (empty($item->categoryid)) {

            if ($item->itemtype == 'course') {
                return $this->course->fullname;
            }

            global $DB;

            $params = array('id' => $item->iteminstance);
            $elem = $DB->get_record('grade_categories', $params);

            return $elem->fullname;
        }

        if (!isset($this->categories[$item->categoryid])) {
            $category = $item->get_parent_category();

            $this->categories[$category->id] = $category;
        }

        return $this->categories[$item->categoryid]->get_name();
    }

    public function heading() {
        return fullname($this->item);
    }
}
