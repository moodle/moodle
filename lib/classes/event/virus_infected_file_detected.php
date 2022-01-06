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
 * Fle infected event
 *
 * @package    core
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();
/**
 * Fle infected event
 *
 * @package    core
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class virus_infected_file_detected extends \core\event\base {
    /**
     * Event data
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Return event description
     *
     * @return string description
     * @throws \coding_exception
     */
    public function get_description() {
        if (isset($this->other['incidentdetails'])) {
            return format_text($this->other['incidentdetails'], FORMAT_MOODLE);
        } else {
            return get_string('fileinfecteddesc', 'antivirus');
        }
    }

    /**
     * Return event name
     *
     * @return string name
     * @throws \coding_exception
     */
    public static function get_name() {
        return get_string('fileinfectedname', 'antivirus');
    }

    /**
     * Return event report link
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public function get_url() {
        return new \moodle_url('/report/infectedfiles/index.php');
    }
}
