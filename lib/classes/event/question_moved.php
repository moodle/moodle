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
 * Question moved event.
 *
 * @package    core
 * @copyright  2019 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Question moved event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int newcategoryid: The ID of the new category of the question
 *      - int oldcategoryid: The ID of the old category of the question
 * }
 *
 * @package    core
 * @since      Moodle 3.7
 * @copyright  2019 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_moved extends question_base {

    /**
     * Init method.
     */
    protected function init() {
        parent::init();
        $this->data['crud'] = 'u';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventquestionmoved', 'question');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' moved the question with the id of '$this->objectid'" .
                " from the category with the id of '" . $this->other['oldcategoryid'] .
                "' to the category with the id of '" . $this->other['newcategoryid'] . "'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        $cat = $this->other['newcategoryid'] . ',' . $this->contextid;
        if ($this->courseid) {
            if ($this->contextlevel == CONTEXT_MODULE) {
                return new \moodle_url('/question/edit.php',
                        ['cmid' => $this->contextinstanceid, 'cat' => $cat, 'lastchanged' => $this->objectid]);
            }
            return new \moodle_url('/question/edit.php',
                    ['courseid' => $this->courseid, 'cat' => $cat, 'lastchanged' => $this->objectid]);
        }
        // Lets try viewing from the frontpage for contexts above course.
        return new \moodle_url('/question/edit.php',
                ['courseid' => SITEID, 'cat' => $cat, 'lastchanged' => $this->objectid]);
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {

        if (!isset($this->other['oldcategoryid'])) {
            throw new \coding_exception('The \'oldcategoryid\' must be set in \'other\'.');
        }
        if (!isset($this->other['newcategoryid'])) {
            throw new \coding_exception('The \'newcategoryid\' must be set in \'other\'.');
        }
    }

    /**
     * Returns DB mappings used with backup / restore.
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        return ['db' => 'question', 'restore' => 'question'];
    }

    /**
     * Used for maping events on restore
     *
     * @return array
     */
    public static function get_other_mapping() {

        $othermapped = [];
        $othermapped['newcategoryid'] = ['db' => 'question_categories', 'restore' => 'question_categories'];
        $othermapped['oldcategoryid'] = ['db' => 'question_categories', 'restore' => 'question_categories'];

        return $othermapped;
    }
}
