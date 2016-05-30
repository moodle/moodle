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
 * Grade report viewed event.
 *
 * @package    core
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Grade report viewed event class.
 *
 * @package    core
 * @since      Moodle 2.8
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class grade_report_viewed extends base {

    /** string $reporttype The report type being viewed. */
    protected $reporttype;

    /**
     * Initialise the event data.
     */
    protected function init() {
        $reporttype = explode('\\', $this->eventname);
        $shorttype = explode('_', $reporttype[1]);
        $this->reporttype = $shorttype[1];

        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventgradeviewed', 'grades');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the $this->reporttype report in the gradebook.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        $url = '/grade/report/' . $this->reporttype . '/index.php';
        return new \moodle_url($url, array('id' => $this->courseid));
    }

    /**
     * Custom validation.
     *
     * To be overwritten by child classes.
     */
    protected function validate_data() {
        parent::validate_data();
    }
}
