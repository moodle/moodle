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
 * Base class for question events.
 *
 * @package    core
 * @copyright  2019 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for question events.
 *
 * @package    core
 * @since      Moodle 3.7
 * @copyright  2019 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_base extends base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'question';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        $cat = $this->other['categoryid'] . ',' . $this->contextid;
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
                ['courseid' => SITEID, 'edit' => $cat, 'lastchanged' => $this->objectid]);
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['categoryid'])) {
            throw new \coding_exception('The \'categoryid\' must be set in \'other\'.');
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
        $othermapped['categoryid'] = ['db' => 'question_categories', 'restore' => 'question_categories'];
        return $othermapped;
    }

    /**
     * Create a event from question object
     *
     * @param object $question
     * @param object|null $context
     * @param array|null $other will override the categoryid pre-filled out on the first line.
     * @return base
     * @throws \coding_exception
     */
    public static function create_from_question_instance($question, $context = null, $other = null) {

        $params = ['objectid' => $question->id, 'other' => ['categoryid' => $question->category]];

        if (!empty($question->contextid)) {
            $params['contextid'] = $question->contextid;
        }

        $params['context'] = $context;

        if (!empty($other)) {
            $params['other'] = $other;
        }

        $event = self::create($params);
        return $event;
    }
}

