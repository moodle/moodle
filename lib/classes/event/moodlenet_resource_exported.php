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
 * MoodleNet send attempt event.
 *
 * @package    core
 * @copyright  2023 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlenet_resource_exported extends \core\event\base {

    /**
     * Set basic properties for the event.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['crud'] = 'c';

        // Used by teachers, but not for direct educational value to their students.
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Fetch the localised general event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('moodlenet:eventresourceexported');
    }

    /**
     * Fetch the non-localised event description.
     * This description format is designed to work for both single activity and course sharing.
     *
     * @return string
     */
    public function get_description() {
        $outcome = $this->other['success'] ? 'successfully shared' : 'failed to share';

        if (!empty($this->other['cmids'])) {
            $cmids = implode("', '", $this->other['cmids']);
            $description = "The user with id '{$this->userid}' {$outcome} activities to MoodleNet with the " .
                "following course module ids, from context with id '{$this->data['contextid']}': '{$cmids}'.";
        } else if (!empty($this->other['courseid'])) {
            $courseid = implode("', '", $this->other['courseid']);
            $description = "The user with id '{$this->userid}' {$outcome} course to MoodleNet with the " .
                "following course id, from context with id '{$this->data['contextid']}': '{$courseid}'.";
        }

        return rtrim($description, ", '");
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url($this->other['resourceurl']);
    }
}
