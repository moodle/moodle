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

namespace mod_turnitintooltwo\event;

/*
 * Log event when
 */

defined('MOODLE_INTERNAL') || die();

class list_submissions extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['level'] = self::LEVEL_PARTICIPATING; // For 2.6, this appears to have been renamed to 'edulevel' in 2.7.
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'turnitintooltwo';
    }

    public static function get_name() {
        return get_string('listsubmissions', 'mod_turnitintooltwo');
    }

    public function get_description() {
        return $this->other['desc'];
    }

    public function get_url() {
        return new \moodle_url('/mod/turnitintooltwo/view.php', array( 'id' => $this->objectid));
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['desc'])) {
            throw new \coding_exception('The \'desc\' value must be set in other.');
        }

        if ($this->contextlevel != CONTEXT_MODULE) {
            throw new \coding_exception('Context level must be CONTEXT_MODULE.');
        }
    }
}
