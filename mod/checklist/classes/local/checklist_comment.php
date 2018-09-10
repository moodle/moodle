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
 * A comment added, by a teacher, to a checklist item
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

class checklist_comment extends data_object {
    public $table = 'checklist_comment';
    public $requiredfields = [
        'id', 'itemid', 'userid', 'commentby', 'text'
    ];

    // DB fields.
    public $itemid;
    public $userid;
    public $commentby;
    public $text;

    // Extra data.
    protected $commentbyname = null;

    protected static $courseid = null;

    public function __construct(array $params = null, $fetch = true) {
        // Really ugly hack to stop travis complaining about $required_fields.
        $this->{'required_fields'} = $this->requiredfields;
        parent::__construct($params, $fetch);
    }

    public static function fetch($params) {
        return self::fetch_helper('checklist_comment', __CLASS__, $params);
    }

    public static function fetch_all($params, $sort = false) {
        $ret = self::fetch_all_helper('checklist_comment', __CLASS__, $params);
        if (!$ret) {
            $ret = [];
        }
        return $ret;
    }

    /**
     * @param int $userid
     * @param int[] $itemids
     * @return checklist_comment[] $itemid => $check
     */
    public static function fetch_by_userid_itemids($userid, $itemids) {
        global $DB;

        $ret = [];
        if (!$itemids) {
            return $ret;
        }

        list($isql, $params) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
        $params['userid'] = $userid;
        $comments = $DB->get_records_select('checklist_comment', "userid = :userid AND itemid $isql", $params);
        foreach ($comments as $comment) {
            $ret[$comment->itemid] = new checklist_comment();
            self::set_properties($ret[$comment->itemid], $comment);
        }
        return $ret;
    }

    /**
     * @return string|null
     */
    public function get_commentby_name() {
        return $this->commentbyname;
    }

    /**
     * @return moodle_url
     */
    public function get_commentby_url() {
        return new moodle_url('/user/view.php', ['id' => $this->commentby, 'course' => self::$courseid]);
    }

    /**
     * @param checklist_comment[] $comments
     */
    public static function add_commentby_names($comments) {
        global $DB;

        $userids = [];
        foreach ($comments as $comment) {
            if ($comment->commentby) {
                $userids[] = $comment->commentby;
            }
        }
        if (!$userids) {
            return;
        }

        $commentusers = $DB->get_records_list('user', 'id', $userids, '', 'id,'.get_all_user_name_fields(true));
        foreach ($comments as $comment) {
            if ($comment->commentby) {
                if (isset($commentusers[$comment->commentby])) {
                    $comment->commentbyname = fullname($commentusers[$comment->commentby]);
                }
            }
        }
    }

    public static function set_courseid($courseid) {
        self::$courseid = $courseid;
    }
}