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
 * The gradebook simple view - grades view (for an activity)
 *
 * @package   singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class singleview_grade extends singleview_tablelike
    implements selectable_items, item_filtering {

    private $requires_extra = false;

    private $requires_paging = false;

    var $structure;

    private static $allow_categories;

    public static function allow_categories() {
        if (is_null(self::$allow_categories)) {
            self::$allow_categories = get_config('moodle', 'grade_overridecat');
        }

        return self::$allow_categories;
    }

    public static function filter($item) {
        return (
            self::allow_categories() or !(
                $item->is_course_item() or $item->is_category_item()
            )
        );
    }

    public function description() {
        return get_string('users');
    }

    public function options() {
        return array_map(function($user) { 
            if (!empty($user->alternatename)) {
                return $user->alternatename . ' (' . $user->firstname . ') ' . $user->lastname;
            } else {
                return fullname($user);
            } 
        }, $this->items); 
    }

    public function item_type() {
        return 'user';
    }

    public function original_definition() {
        $def = array('finalgrade', 'feedback');

        if ($this->requires_extra) {
            $def[] = 'override';
        }

        $def[] = 'exclude';

        return $def;
    }

    public function init($self_item_is_empty = false) {
        $roleids = explode(',', get_config('moodle', 'gradebookroles'));

        $this->items = get_role_users(
            $roleids, $this->context, false, '',
            'u.lastname, u.firstname', null, $this->groupid
        );

        if ($self_item_is_empty) {
            return;
        }

        // Only page when necessary.
        if (count($this->items) > $this->perpage) {
            $this->requires_paging = true;

            $this->all_items = $this->items;

            $this->items = get_role_users(
                $roleids, $this->context, false, '',
                'u.lastname, u.firstname', null, $this->groupid,
                $this->perpage * $this->page, $this->perpage
            );
        }

        global $DB;

        $params = array(
            'id' => $this->itemid,
            'courseid' => $this->courseid
        );

        $this->item = grade_item::fetch($params);

        $filter_fun = grade_report_singleview::filters();

        $allowed = $filter_fun($this->item);

        if (empty($allowed)) {
            print_error('not_allowed', 'gradereport_singleview');
        }

        $this->requires_extra = !$this->item->is_manual_item();

        $this->setup_structure();

        $this->set_definition($this->original_definition());
        $this->set_headers($this->original_headers());
    }

    public function original_headers() {
        $headers = array(
            '', // for filter icon.
            '', // for user picture.
            get_string('firstname') . ' (' . get_string('alternatename') . ') ' . get_string('lastname'),
            get_string('range', 'grades'),
            get_string('grade', 'grades'),
            get_string('feedback', 'grades')
        );

        return $this->additional_headers($headers);
    }

    public function format_line($item) {
        global $OUTPUT;

        $grade = $this->fetch_grade_or_default($this->item, $item->id);

        // UCSB add lock icon indicator.
        $lockicon = '';

        // CODE to make steve happy for his simple mind.
	$locked_grade = $locked_grade_item = 0;
        if ( ! empty($grade->locked) )  $locked_grade = 1;
        if ( ! empty($grade->grade_item->locked) ) $locked_grade_item = 1;
        // check both grade and grade item.
        if ( $locked_grade || $locked_grade_item )
            $lockicon = $OUTPUT->pix_icon('t/locked', 'grade is locked') . ' ';

        if (!empty($item->alternatename)) {
            $fullname = $lockicon . $item->alternatename . ' (' . $item->firstname . ') ' . $item->lastname;
        } else {
            $fullname = $lockicon . fullname($item);
        }

        $item->imagealt = $fullname;
        $url = new moodle_url("/user/view.php", array('id' => $item->id, 'course' => $this->courseid));

        $line = array( 
            $OUTPUT->action_icon($this->format_link('grade', $item->id), new pix_icon('t/editstring', get_string('filtergrades', 'gradereport_singleview', $fullname))),
            $OUTPUT->user_picture($item),
            html_writer::tag('a', $fullname, array('href' => $url)),
            $this->item_range()
        );
        return $this->format_definition($line, $grade);
    }

    public function additional_headers($headers) {
        if ($this->requires_extra) {
            $headers[] = $this->make_toggle_links('override');
        }

        $headers[] = $this->make_toggle_links('exclude');

        return $headers;
    }

    public function item_range() {
        if (empty($this->range)) {
            $this->range = $this->factory()->create('range')->format($this->item);
        }

        return $this->range;
    }

    public function supports_paging() {
        return $this->requires_paging;
    }

    public function pager() {
        global $OUTPUT;

        return $OUTPUT->paging_bar(
            count($this->all_items), $this->page, $this->perpage,
            new moodle_url('/grade/report/singleview/index.php', array(
                'perpage' => $this->perpage,
                'id' => $this->courseid,
                'groupid' => $this->groupid,
                'itemid' => $this->itemid,
                'item' => 'grade'
            ))
        );
    }

    public function heading() {
        return $this->item->get_name();
    }
}
