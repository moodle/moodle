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

namespace tool_mfa\event;

use stdClass;

/**
 * Event for when user successfully passed all MFA factor checks.
 *
 * @property-read array $other {
 *      Extra information about event.
 * }
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_passed_mfa extends \core\event\base {

    /**
     * Create instance of event.
     *
     * @param stdClass $user the User object of the User who passed all MFA factor checks.
     *
     * @return user_passed_mfa the user_passed_mfa event
     *
     * @throws \coding_exception
     */
    public static function user_passed_mfa_event(stdClass $user): user_passed_mfa {

        // Build debug info string.
        $factors = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        $debug = '';
        foreach ($factors as $factor) {
            $debug .= "<br> Factor {$factor->name} status: {$factor->get_state()}";
        }

        $data = [
            'relateduserid' => null,
            'context' => \context_user::instance($user->id),
            'other' => [
                'userid' => $user->id,
                'debug' => $debug,
            ],
        ];

        return self::create($data);
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init(): void {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with id '{$this->other['userid']}' successfully passed MFA. <br> Information: {$this->other['debug']}";
    }

    /**
     * Return localised event name.
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_name(): string {
        return get_string('event:userpassedmfa', 'tool_mfa');
    }
}
