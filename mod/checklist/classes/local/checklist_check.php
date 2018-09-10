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
 * Holds the checkmark information
 *
 * @package   mod_checklist
 * @copyright 2016 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_checklist\local;

use data_object;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/completion/data_object.php');
require_once($CFG->dirroot.'/mod/checklist/lib.php');

class checklist_check extends data_object {
    public $table = 'checklist_check';
    public $requiredfields = [
        'id', 'item', 'userid', 'usertimestamp', 'teachermark', 'teachertimestamp', 'teacherid'
    ];

    // DB fields.
    public $item;
    public $userid;
    public $usertimestamp = 0;
    public $teachermark = CHECKLIST_TEACHERMARK_UNDECIDED;
    public $teachertimestamp = 0;
    public $teacherid = null;

    public function __construct(array $params = null, $fetch = true) {
        // Really ugly hack to stop travis complaining about $required_fields.
        $this->{'required_fields'} = $this->requiredfields;
        parent::__construct($params, $fetch);
    }

    public static function fetch($params) {
        return self::fetch_helper('checklist_check', __CLASS__, $params);
    }

    public static function fetch_all($params, $sort = false) {
        $ret = self::fetch_all_helper('checklist_check', __CLASS__, $params);
        if (!$ret) {
            $ret = [];
        }
        return $ret;
    }

    /**
     * @param $userid
     * @param $itemids
     * @return checklist_check[] $itemid => $check
     */
    public static function fetch_by_userid_itemids($userid, $itemids) {
        global $DB;

        $ret = [];
        if (!$itemids) {
            return $ret;
        }

        list($isql, $params) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
        $params['userid'] = $userid;
        $checks = $DB->get_records_select('checklist_check', "userid = :userid AND item $isql", $params);
        foreach ($checks as $check) {
            $ret[$check->item] = new checklist_check();
            self::set_properties($ret[$check->item], $check);
        }
        return $ret;
    }

    public static function teachermark_valid($teachermark) {
        return in_array($teachermark, [CHECKLIST_TEACHERMARK_YES, CHECKLIST_TEACHERMARK_NO, CHECKLIST_TEACHERMARK_UNDECIDED]);
    }

    protected function check_fields_valid() {
        if (!self::teachermark_valid($this->teachermark)) {
            debugging('Unexpected teachermark value: '.$this->teachermark);
            $this->teachermark = CHECKLIST_TEACHERMARK_UNDECIDED;
        }
    }

    public function save() {
        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    public function insert() {
        $this->check_fields_valid();
        return parent::insert();
    }

    public function update() {
        $this->check_fields_valid();
        return parent::update();
    }

    public function is_checked_student() {
        return $this->usertimestamp > 0;
    }

    public function is_checked_teacher() {
        return ($this->teachermark == CHECKLIST_TEACHERMARK_YES);
    }

    public function set_teachermark($teachermark, $teacherid) {
        $this->teachermark = $teachermark;
        $this->teacherid = $teacherid;
        $this->teachertimestamp = time();
    }

    public function set_checked_student($checked, $timestamp = null) {
        $timestamp = $timestamp ?: time();
        $this->usertimestamp = $checked ? $timestamp : 0;
    }
}
