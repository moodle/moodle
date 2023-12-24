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

namespace mod_forum\event;

use coding_exception;
use moodle_url;

/**
 * The mod_forum discussion lock updated event.
 *
 * @package    mod_forum
 * @copyright  2022 Université Rennes 2 {@link https://www.univ-rennes2.fr}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion_lock_updated extends \core\event\base {
    /**
     * Init method.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'forum_discussions';
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with id '$this->userid' {$this->other['status']} the discussion: $this->objectid".
            " in the forum: {$this->other['forumid']}";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventdiscussionlockupdated', 'mod_forum');
    }

    /**
     * Get URL related to the action
     *
     * @return moodle_url
     */
    public function get_url(): moodle_url {
        return new moodle_url('/mod/forum/discuss.php', ['d' => $this->objectid]);
    }

    /**
     * Custom validation.
     *
     * @throws coding_exception
     * @return void
     */
    protected function validate_data(): void {
        parent::validate_data();
        if (!isset($this->other['forumid'])) {
            throw new coding_exception('forumid must be set in $other.');
        }
        if (!isset($this->other['status'])) {
            throw new \coding_exception('The \'status\' value must be set in other.');
        }
        if (!in_array($this->other['status'], ['locked', 'unlocked'], true)) {
            throw new \coding_exception('The \'status\' value must be \'locked\' or \'unlocked\'.');
        }
        if ($this->contextlevel != CONTEXT_MODULE) {
            throw new coding_exception('Context passed must be module context.');
        }
        if (!isset($this->objectid)) {
            throw new coding_exception('objectid must be set to the discussionid.');
        }
    }

    /**
     * Forum discussion object id mappings.
     *
     * @return array
     */
    public static function get_objectid_mapping(): array {
        return ['db' => 'forum_discussions', 'restore' => 'forum_discussion'];
    }

    /**
     * Forum id mappings.
     *
     * @return array
     */
    public static function get_other_mapping(): array {
        return ['forumid' => ['db' => 'forum', 'restore' => 'forum']];
    }
}
