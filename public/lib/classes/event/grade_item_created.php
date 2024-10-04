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
 * Grade item created event.
 *
 * @package    core
 * @copyright  2019 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Grade item created event class.
 *
 * @package    core
 * @since      Moodle 3.8
 * @copyright  2019 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_item_created extends base {

    /** @var \grade_item $gradeitem */
    protected $gradeitem;

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = 'grade_items';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventgradeitemcreated', 'core_grades');
    }

    /**
     * Utility method to create a new event.
     *
     * @param \grade_item $gradeitem
     * @return grade_item_created
     */
    public static function create_from_grade_item(\grade_item $gradeitem) {
        $event = self::create([
            'objectid' => $gradeitem->id,
            'courseid' => $gradeitem->courseid,
            'context' => \context_course::instance($gradeitem->courseid),
            'other' => [
                'itemname' => $gradeitem->itemname,
                'itemtype' => $gradeitem->itemtype,
                'itemmodule' => $gradeitem->itemmodule,
            ],
        ]);

        $event->gradeitem = $gradeitem;

        return $event;
    }

    /**
     * Get grade object.
     *
     * @throws \coding_exception
     * @return \grade_item
     */
    public function get_grade_item() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_grade_item() is intended for event observers only');
        }

        if (!isset($this->gradeitem)) {
            $this->gradeitem = \grade_item::fetch(['id' => $this->objectid]);
        }

        return $this->gradeitem;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '" . $this->userid . "' created a grade item with id '" . $this->objectid . "'" .
            " of type '" . $this->other['itemtype'] . "' and name '" . $this->other['itemname'] . "'" .
            " in the course with the id '" . $this->courseid . "'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        $url = new \moodle_url('/grade/edit/tree/index.php');
        $url->param('id', $this->courseid);

        return $url;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     */
    protected function validate_data() {
        parent::validate_data();

        if (!array_key_exists('itemname', $this->other)) {
            throw new \coding_exception('The \'itemname\' value must be set in other.');
        }

        if (!array_key_exists('itemtype', $this->other)) {
                throw new \coding_exception('The \'itemtype\' value must be set in other.');
        }

        if (!array_key_exists('itemmodule', $this->other)) {
            throw new \coding_exception('The \'itemmodule\' value must be set in other.');
        }
    }

}
