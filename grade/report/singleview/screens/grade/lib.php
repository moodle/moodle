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
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class gradereport_singleview_grade extends gradereport_singleview_tablelike
    implements gradereport_selectable_items, gradereport_item_filtering {

    private $totalitemcount = 0;

    private $requiresextra = false;

    private $requirespaging = true;

    public $structure;

    private static $allowcategories;

    public static function allowcategories() {
        if (is_null(self::$allowcategories)) {
            self::$allowcategories = get_config('moodle', 'grade_overridecat');
        }

        return self::$allowcategories;
    }

    public static function filter($item) {
        return (
            self::allowcategories() or !(
                $item->is_course_item() or $item->is_category_item()
            )
        );
    }

    public function description() {
        return get_string('users');
    }

    public function options() {
        $options = array_map(function($user) {
            if (!empty($user->alternatename)) {
                return $user->alternatename . ' (' . $user->firstname . ') ' . $user->lastname;
            } else {
                return fullname($user);
            }
        }, $this->items);

        return $options;
    }

    public function item_type() {
        return 'user';
    }

    public function original_definition() {
        $def = array('finalgrade', 'feedback');

        if ($this->requiresextra) {
            $def[] = 'override';
        }

        $def[] = 'exclude';

        return $def;
    }

    public function init($selfitemisempty = false) {
        $roleids = explode(',', get_config('moodle', 'gradebookroles'));

        $this->items = get_role_users(
            $roleids, $this->context, false, '',
            'u.lastname, u.firstname', null, $this->groupid,
            $this->perpage * $this->page, $this->perpage
        );

        $this->totalitemcount = count_role_users($roleids, $this->context);

        if ($selfitemisempty) {
            return;
        }

        global $DB;

        $params = array(
            'id' => $this->itemid,
            'courseid' => $this->courseid
        );

        $this->item = grade_item::fetch($params);

        $filterfun = gradereport_singleview::filters();

        $allowed = $filterfun($this->item);

        if (empty($allowed)) {
            print_error('notallowed', 'gradereport_singleview');
        }

        $this->requiresextra = !$this->item->is_manual_item();

        $this->setup_structure();

        $this->set_definition($this->original_definition());
        $this->set_headers($this->original_headers());
    }

    public function original_headers() {
        return array(
            '', // For filter icon.
            '', // For user picture.
            get_string('firstname') . ' (' . get_string('alternatename') . ') ' . get_string('lastname'),
            get_string('range', 'grades'),
            get_string('grade', 'grades'),
            get_string('feedback', 'grades'),
            $this->make_toggle_links('override'),
            $this->make_toggle_links('exclude')
        );
    }

    public function format_line($item) {
        global $OUTPUT;

        $grade = $this->fetch_grade_or_default($this->item, $item->id);

        $lockicon = '';

        $lockedgrade = $lockedgradeitem = 0;
        if (!empty($grade->locked)) {
            $lockedgrade = 1;
        }
        if (!empty($grade->grade_item->locked)) {
            $lockedgradeitem = 1;
        }
        // Check both grade and grade item.
        if ( $lockedgrade || $lockedgradeitem )
            $lockicon = $OUTPUT->pix_icon('t/locked', 'grade is locked') . ' ';

        if (!empty($item->alternatename)) {
            $fullname = $lockicon . $item->alternatename . ' (' . $item->firstname . ') ' . $item->lastname;
        } else {
            $fullname = $lockicon . fullname($item);
        }

        $item->imagealt = $fullname;
        $url = new moodle_url("/user/view.php", array('id' => $item->id, 'course' => $this->courseid));
        $iconstring = get_string('filtergrades', 'gradereport_singleview', $fullname);
        $grade->label = $fullname;

        $line = array(
            $OUTPUT->action_icon($this->format_link('user', $item->id), new pix_icon('t/editstring', $iconstring)),
            $OUTPUT->user_picture($item),
            html_writer::link($url, $fullname),
            $this->item_range()
        );

        return $this->format_definition($line, $grade);
    }

    public function item_range() {
        if (empty($this->range)) {
            $this->range = $this->factory()->create('range')->format($this->item);
        }

        return $this->range;
    }

    public function supports_paging() {
        return $this->requirespaging;
    }

    public function pager() {
        global $OUTPUT;

        return $OUTPUT->paging_bar(
            $this->totalitemcount, $this->page, $this->perpage,
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
