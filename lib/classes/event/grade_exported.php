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

namespace core\event;

/**
 * Abstract grade report exported event class.
 *
 * @package    core
 * @since      Moodle 3.2
 * @copyright  2016 Zane Karl <zkarl@oid.ucla.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_exported extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        if (!($this instanceof grade_exported)) {
            throw new Exception('grade_exported abstract is NOT extended by a valid component.');
        }
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised export type.
     *
     * @return string
     */
    public static function get_export_type() {
        $classname = explode('\\', get_called_class());
        $exporttype = explode('_', $classname[0]);
        return $exporttype[1] ?? '';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        $component = 'gradeexport_' . self::get_export_type();
        if (get_string_manager()->string_exists('eventgradeexported', $component)) {
            return get_string('eventgradeexported', $component);
        }

        // Fallback to generic name.
        return get_string('eventgradeexported', 'core_grades');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid'"
                . " exported grades using the ".
                $this->get_export_type() ." export in the gradebook.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        $url = '/grade/export/' . $this->get_export_type() . '/export.php';
        return new \moodle_url($url, array('id' => $this->courseid));
    }
}