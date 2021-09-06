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
 * Questions imported event.
 *
 * @package    core
 * @copyright  2019 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Question category imported event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int categoryid: The ID of the category where the question resides
 *      - string format: The format of file import
 * }
 *
 * @package    core
 * @since      Moodle 3.7
 * @copyright  2019 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questions_imported extends question_base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventquestionsimported', 'question');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' imported questions in '" . $this->other['format'] .
                "' format into the category with id '" . $this->other['categoryid'] . "'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        if ($this->courseid) {
            $cat = $this->other['categoryid'] . ',' . $this->contextid;
            if ($this->contextlevel == CONTEXT_MODULE) {
                return new \moodle_url('/question/edit.php', ['cmid' => $this->contextinstanceid, 'cat' => $cat]);
            }
            return new \moodle_url('/question/edit.php', ['courseid' => $this->courseid, 'cat' => $cat]);
        }
        return new \moodle_url('/question/bank/managecategories/category.php',
                                 ['courseid' => SITEID, 'edit' => $this->other['categoryid']]);
    }

    /**
     * Custom validations.
     *
     * other['categoryid'] and other['format'] is required.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['format'])) {
            throw new \coding_exception('The \'format\' must be set in \'other\'.');
        }
    }

    /**
     * Returns DB mappings used with backup / restore.
     * This is needed to override the function in question_base
     *
     * @return array
     */
    public static function get_objectid_mapping() {
        // No mappings.
        return [];
    }
}
