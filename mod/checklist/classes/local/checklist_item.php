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
 * Class to hold a checklist item.
 *
 * @package   mod_checklist
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_checklist\local;

use data_object;
use moodle_url;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/completion/data_object.php');
require_once($CFG->dirroot.'/mod/checklist/lib.php');

class checklist_item extends data_object {
    public $table = 'checklist_item';
    public $requiredfields = [
        'id', 'checklist', 'userid', 'displaytext', 'position', 'indent', 'itemoptional', 'duetime',
        'eventid', 'colour', 'moduleid', 'hidden', 'grouping', 'linkcourseid', 'linkurl'
    ];

    // DB fields.
    public $checklist;
    public $userid;
    public $displaytext;
    public $position;
    public $indent = 0;
    public $itemoptional = CHECKLIST_OPTIONAL_NO;
    public $duetime = 0;
    public $eventid = 0;
    public $colour = 'black';
    public $moduleid = 0;
    public $hidden = CHECKLIST_HIDDEN_NO;
    public $grouping = 0;
    public $linkcourseid = null;
    public $linkurl = null;

    // Extra status fields (for a particular student).
    public $usertimestamp = 0;
    public $teachermark = CHECKLIST_TEACHERMARK_UNDECIDED;
    public $teachertimestamp = 0;
    public $teacherid = null;

    protected $teachername = null;
    /** @var checklist_comment|null */
    protected $comment = null;
    protected $editme = false;
    protected $modulelink = null;

    // Name of the grouping (set by add_grouping_names).
    public $groupingname = null;

    const LINK_MODULE = 'module';
    const LINK_COURSE = 'course';
    const LINK_URL = 'url';

    public function __construct(array $params = null, $fetch = true) {
        // Really ugly hack to stop travis complaining about $required_fields.
        $this->{'required_fields'} = $this->requiredfields;
        parent::__construct($params, $fetch);
    }

    public static function fetch($params) {
        return self::fetch_helper('checklist_item', __CLASS__, $params);
    }

    public static function fetch_all($params, $sort = false) {
        $ret = self::fetch_all_helper('checklist_item', __CLASS__, $params);
        if (!$ret) {
            $ret = [];
        }
        if ($sort) {
            self::sort_items($ret);
        }
        return $ret;
    }

    public static function sort_items(&$items) {
        if (!$items) {
            return;
        }
        uasort($items, function (checklist_item $a, checklist_item $b) {
            if ($a->position < $b->position) {
                return -1;
            }
            if ($a->position > $b->position) {
                return 1;
            }
            // Sort by id, if the positions are the same.
            if ($a->id < $b->id) {
                return -1;
            }
            if ($a->id > $b->id) {
                return 1;
            }
            return 0;
        });
    }

    public function store_status($usertimestamp = null, $teachermark = null, $teachertimestamp = null, $teacherid = null) {
        if ($usertimestamp !== null) {
            $this->usertimestamp = $usertimestamp;
        }
        if ($teachermark !== null) {
            if (!checklist_check::teachermark_valid($teachermark)) {
                debugging('Unexpected teachermark value: '.$teachermark);
                $teachermark = CHECKLIST_TEACHERMARK_UNDECIDED;
            }
            $this->teachermark = $teachermark;
        }
        if ($teachertimestamp !== null) {
            $this->teachertimestamp = $teachertimestamp;
        }
        if ($teacherid !== null) {
            $this->teacherid = $teacherid;
        }
    }

    public function is_checked($byteacher) {
        if ($this->userid > 0 || !$byteacher) {
            // User custom items are always checked-off by students (regardless of checklist settings).
            return $this->usertimestamp > 0;
        } else {
            return ($this->teachermark == CHECKLIST_TEACHERMARK_YES);
        }
    }

    public function is_checked_teacher() {
        return $this->is_checked(true);
    }

    public function is_checked_student() {
        return $this->is_checked(false);
    }

    public function is_heading() {
        return ($this->itemoptional == CHECKLIST_OPTIONAL_HEADING);
    }

    public function is_required() {
        return ($this->itemoptional == CHECKLIST_OPTIONAL_NO);
    }

    public function is_optional() {
        return ($this->itemoptional == CHECKLIST_OPTIONAL_YES);
    }

    private function image_url($imagename, $component) {
        global $CFG, $OUTPUT;
        if ($CFG->branch < 33) {
            return $OUTPUT->pix_url($imagename, $component);
        }
        return $OUTPUT->image_url($imagename, $component);
    }

    public function get_teachermark_image_url() {
        static $images = null;
        if ($images === null) {
            $images = [
                CHECKLIST_TEACHERMARK_YES => $this->image_url('tick_box', 'mod_checklist'),
                CHECKLIST_TEACHERMARK_NO => $this->image_url('cross_box', 'mod_checklist'),
                CHECKLIST_TEACHERMARK_UNDECIDED => $this->image_url('empty_box', 'mod_checklist'),
            ];
        }
        return $images[$this->teachermark];
    }

    public function get_teachermark_text() {
        static $text = null;
        if ($text === null) {
            $text = [
                CHECKLIST_TEACHERMARK_YES => get_string('teachermarkyes', 'mod_checklist'),
                CHECKLIST_TEACHERMARK_NO => get_string('teachermarkno', 'mod_checklist'),
                CHECKLIST_TEACHERMARK_UNDECIDED => get_string('teachermarkundecided', 'mod_checklist'),
            ];
        }
        return $text[$this->teachermark];
    }

    public function get_teachermark_class() {
        static $classes = null;
        if ($classes === null) {
            $classes = [
                CHECKLIST_TEACHERMARK_YES => 'teachermarkyes',
                CHECKLIST_TEACHERMARK_NO => 'teachermarkno',
                CHECKLIST_TEACHERMARK_UNDECIDED => 'teachermarkundecided',
            ];
        }
        return $classes[$this->teachermark];
    }

    public function toggle_hidden() {
        if ($this->hidden == CHECKLIST_HIDDEN_BYMODULE) {
            return; // Do not override items linked to hidden Moodle activities.
        }
        if ($this->hidden == CHECKLIST_HIDDEN_NO) {
            $this->hidden = CHECKLIST_HIDDEN_MANUAL;
        } else {
            $this->hidden = CHECKLIST_HIDDEN_NO;
        }
        $this->update();
    }

    public function hide_item() {
        if (!$this->moduleid) {
            return;
        }
        if ($this->hidden != CHECKLIST_HIDDEN_NO) {
            return;
        }
        $this->hidden = CHECKLIST_HIDDEN_MANUAL;
        $this->update();
    }

    public function show_item() {
        if (!$this->moduleid) {
            return;
        }
        if ($this->hidden != CHECKLIST_HIDDEN_MANUAL) {
            return;
        }
        $this->hidden = CHECKLIST_HIDDEN_NO;
        $this->update();
    }

    public function set_checked_student($userid, $checked, $timestamp = null) {
        if ($checked == $this->is_checked_student()) {
            return false; // No change.
        }

        // Update checkmark in the database.
        $check = new checklist_check(['item' => $this->id, 'userid' => $userid]);
        $check->set_checked_student($checked, $timestamp);
        $check->save();

        // Update the stored value in this item.
        $this->usertimestamp = $check->usertimestamp;
        return true;
    }

    /**
     * For the given item, clear all student checkmarks (leaving teacher marks untouched).
     */
    public function clear_all_student_checks() {
        global $DB;
        $DB->set_field_select('checklist_check', 'usertimestamp', 0, 'item = ? AND usertimestamp > 0', [$this->id]);
    }

    public function set_teachermark($userid, $teachermark, $teacherid) {
        if ($teachermark == $this->teachermark) {
            return false; // No change.
        }

        if (!checklist_check::teachermark_valid($teachermark)) {
            throw new \coding_exception('Invalid teachermark '.$teachermark);
        }

        // Update checkmark in the database.
        $check = new checklist_check(['item' => $this->id, 'userid' => $userid]);
        $check->set_teachermark($teachermark, $teacherid);
        $check->save();

        // Update the stored value in this item.
        $this->teachertimestamp = $check->teachertimestamp;
        $this->teachermark = $check->teachermark;
        $this->teacherid = $check->teacherid;

        return true;
    }

    public function get_teachername() {
        return $this->teachername;
    }

    public function get_comment() {
        return $this->comment;
    }

    public function set_editme($editme = true) {
        $this->editme = $editme;
    }

    public function is_editme() {
        return $this->editme;
    }

    public function get_link_url() {
        if ($this->modulelink) {
            return $this->modulelink;
        }
        if ($this->linkcourseid) {
            return new moodle_url('/course/view.php', ['id' => $this->linkcourseid]);
        }
        if ($this->linkurl) {
            return new moodle_url($this->linkurl);
        }
        return null;
    }

    public function get_link_type() {
        if ($this->modulelink) {
            return self::LINK_MODULE;
        }
        if ($this->linkcourseid) {
            return self::LINK_COURSE;
        }
        if ($this->linkurl) {
            return self::LINK_URL;
        }
        return null;
    }

    public function set_modulelink(moodle_url $link) {
        $this->modulelink = $link;
    }

    /**
     * Check if this item can be automatically updated.
     * i.e. is it linked to an activity or linked to a course with completion enabled
     *
     * @return bool
     */
    public function is_auto_item() {
        if ($this->moduleid) {
            return true;
        }
        if ($this->linkcourseid) {
            $completion = new \completion_info(get_course($this->linkcourseid));
            if ($completion->is_enabled()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add links from the checklist items to the comments on them (for a particular user).
     * @param checklist_item[] $items (indexed by id)
     * @param checklist_comment[] $comments (indexed by itemid)
     */
    public static function add_comments($items, $comments) {
        foreach ($items as $item) {
            if (isset($comments[$item->id])) {
                $item->comment = $comments[$item->id];
            }
        }
    }

    /**
     * Add the names of all the teachers who have updated the checklist items.
     * @param checklist_item[] $items
     */
    public static function add_teacher_names($items) {
        global $DB;

        $userids = [];
        foreach ($items as $item) {
            if ($item->teacherid) {
                $userids[] = $item->teacherid;
            }
        }
        if (!$userids) {
            return;
        }

        $teachers = $DB->get_records_list('user', 'id', $userids, '', 'id,'.get_all_user_name_fields(true));
        foreach ($items as $item) {
            if ($item->teacherid) {
                if (isset($teachers[$item->teacherid])) {
                    $item->teachername = fullname($teachers[$item->teacherid]);
                }
            }
        }
    }

    /**
     * @param checklist_item[] $items
     * @param int $courseid
     */
    public static function add_grouping_names($items, $courseid) {
        $groupings = \checklist_class::get_course_groupings($courseid);
        if (!$groupings) {
            return;
        }
        foreach ($items as $item) {
            if ($item->grouping && isset($groupings[$item->grouping])) {
                $item->groupingname = $groupings[$item->grouping];
            }
        }
    }
}
