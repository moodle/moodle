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
namespace bbbext_simple\bigbluebuttonbn;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\logger;

/**
 * A class to deal with completion rules addons in a subplugin
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class custom_completion_addons extends \mod_bigbluebuttonbn\local\extension\custom_completion_addons {

    /**
     * Get defined custom rule
     *
     * @return string[]
     */
    public static function get_defined_custom_rules(): array {
        return ['completionextraisehandtwice'];
    }

    /**
     * Get state
     *
     * @param string $rule
     * @return int
     */
    public function get_state(string $rule): int {
        $instance = instance::get_from_cmid($this->cm->id);
        $logs = logger::get_user_completion_logs($instance, $this->userid, [logger::EVENT_SUMMARY]);
        $raisehandcount = 0;
        foreach ($logs as $log) {
            $summary = json_decode($log->meta);
            $raisehandcount += intval($summary->data->engagement->raisehand ?? 0);
        }
        if ($raisehandcount >= 2) {
            return COMPLETION_COMPLETE;
        }
        return COMPLETION_INCOMPLETE;
    }

    /**
     * Get rule description
     *
     * @return array
     * @throws \coding_exception
     */
    public function get_custom_rule_descriptions(): array {
        return [
            'completionextraisehandtwice' => get_string('completionextraisehandtwice_desc', 'bbbext_simple'),
        ];
    }

    /**
     * Get sort order
     *
     * @return string[]
     */
    public function get_sort_order(): array {
        return [
            'completionattendance',
        ];
    }
}
