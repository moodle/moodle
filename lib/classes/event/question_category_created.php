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
 * Question category created event.
 *
 * @package    core
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Question category created event class.
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_created extends base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'question_categories';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventquestioncategorycreated', 'question');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' created the question category with id '$this->objectid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        if ($this->courseid) {
            $cat = $this->objectid . ',' . $this->contextid;
            if ($this->contextlevel == CONTEXT_MODULE) {
                return new \moodle_url('/question/edit.php', array('cmid' => $this->contextinstanceid, 'cat' => $cat));
            }
            return new \moodle_url('/question/edit.php', array('courseid' => $this->courseid, 'cat' => $cat));
        }

        // Bad luck, there does not seem to be any simple intelligent way
        // to go to specific question category in context above course,
        // let's try to edit it from frontpage which may surprisingly work.
        return new \moodle_url('/question/category.php', array('courseid' => SITEID, 'edit' => $this->objectid));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        if ($this->contextlevel == CONTEXT_MODULE) {
            return array($this->courseid, 'quiz', 'addcategory', 'view.php?id=' . $this->contextinstanceid,
                $this->objectid, $this->contextinstanceid);
        }
        // This is not related to individual quiz at all.
        return null;
    }
}
