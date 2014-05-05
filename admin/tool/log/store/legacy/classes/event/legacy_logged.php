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

namespace logstore_legacy\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Legacy log emulation event class.
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class legacy_logged extends \core\event\base {

    public function init() {
        throw new \coding_exception('legacy events cannot be triggered');
    }

    public static function get_name() {
        return get_string('eventlegacylogged', 'logstore_legacy');
    }

    public function get_description() {
        return $this->other['module'] . ' ' . $this->other['action'] . ' ' . $this->other['info'];
    }

    public function get_url() {
        global $CFG;
        require_once("$CFG->dirroot/course/lib.php");

        $url = \make_log_url($this->other['module'], $this->other['url']);
        if (!$url) {
            return null;
        }
        return new \moodle_url($url);
    }
}
